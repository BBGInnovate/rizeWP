<?php namespace OpenFuego\app;

use OpenFuego\lib\DbHandle as DbHandle;
use OpenFuego\lib\Metadata as Metadata;

class Getter {

	protected $_dbh;

	protected function getDbh() {
		if (!$this->_dbh) {
			$this->_dbh = new DbHandle();
		}
		
		return $this->_dbh;
	}

	public function hideLink($linkID) {
		/*** When a user is logged into wordpress, they can click 'hide link' on the fuego page to get rid of 
		individual links */
		try {
			$dbh = $this->getDbh();
			$sql = "
				UPDATE openfuego_links 
				SET hiddenFlag='Y' 
				WHERE link_id=  :linkID
			";
			$sth = $this->_dbh->prepare($sql);
			$sth->bindParam('linkID', $linkID, \PDO::PARAM_INT);
			$sth->execute();
	
		} catch (\PDOException $e) {
			Logger::error($e);
			return FALSE;
		}
	}

	public function updateEmbedlyCache($linkID,$embedlyJsonStr) {
		try {
			$dbh = $this->getDbh();

			$sql="
			INSERT IGNORE INTO embedlyCache (link_id, embedlyJson)
			VALUES (:link_id, :embedlyJson);
			";
			$sth = $dbh->prepare($sql);
			$sth->bindParam('link_id', $linkID);
			$sth->bindParam('embedlyJson', $embedlyJsonStr);
			$sth->execute();

	
		} catch (\PDOException $e) {
			Logger::error($e);
			return FALSE;
		}
	}

	public function updateLinkImage($linkID,$remoteImage,$localImage) {
		/** we store references to the remote and local image that's associated with an openfuego_link.  
			these images come from the embedly api ****/
		try {
			$dbh = $this->getDbh();
			
			$sql = "
				UPDATE openfuego_links 
				SET localImage=:localImage,
					remoteImage=:remoteImage
				WHERE link_id=  :linkID
			";
			$sth = $this->_dbh->prepare($sql);
			$sth->bindParam('linkID', $linkID, \PDO::PARAM_INT);
			$sth->bindParam('remoteImage', $remoteImage, \PDO::PARAM_STR);
			$sth->bindParam('localImage', $localImage, \PDO::PARAM_STR);
			$sth->execute();
			
		} catch (\PDOException $e) {
			Logger::error($e);
			return FALSE;
		}
	}


	public function checkEmbedlyCache($linkIDArray) {
		/** given array of link_ids, return all the ones that don't have an entry in embedlyCache table */
		$sql = '
			SELECT link_id, embedlyJSON
			FROM embedlyCache
			WHERE link_id IN ('.str_pad('',count($linkIDArray)*2-1,'?,').')
		';
		$dbh = self::getDbh();
		$sth = $dbh->prepare($sql);
		$sth->execute($linkIDArray);
		$rows =$sth->fetchAll(\PDO::FETCH_ASSOC);

		$embedlyData=[];
		foreach ($rows as $row) {
			$currentLinkID=$row['link_id'];
			$embedlyData[$currentLinkID]= array(
				'link_id' => $currentLinkID,
				'embedlyJSON' => $row['embedlyJSON']
			);
		}
		return $embedlyData;
	}

	public function getItems($quantity = 10, $hours = 24, $scoring = TRUE, $metadata = FALSE, $min_weighted_count = 24, $linkID=0) {
	
		/**** 
			This function hits the openfuego_links table.  
			Basic Flow:
				1) Fetch links from openfuego_links table.  Depending on URL params it may be one of 3 queries.
				2) Created items_filtered[] array, which has some logic to tack on additional variables.  
				   --this step was inherited from original Fuego code - it doesn't do a whole lot for us right now
				3) Figure out which of those have an entry in the embedlycache table
				4) Group all of those items together into a request for the embedly API - it takes up to 20 at a time so we should only have one
				   --store the results in the embedlycache table as pure json
				   --fetch & resize images into the imgcache folder
				   --updating openfuego_links with a reference to the remote image path and the local (newly resized) image path
				5) Attach tweet data to items_filtered
		***/


		$now = time();
		$quantity = (int)$quantity;
		$hours = (int)$hours;
		$date = date('Y-m-d H:i:s', $now);	

		$limit=$quantity;
		if ($scoring === false) {
			$min_weighted_count=1;
		}
	

		/****** BEGIN RUNNING SQL QUERY *****/
		try {
			$dbh = $this->getDbh();
			
			if ($linkID > 0) {
				$sql = "
					SELECT link_id, url, first_seen, first_user, weighted_count, count, localImage, remoteImage, first_user_fullname
					FROM openfuego_links
					WHERE link_id = :link_id
				";
				$sth = $this->_dbh->prepare($sql);
				$sth->bindParam('link_id', $linkID, \PDO::PARAM_INT);
				$sth->execute();
			} else {

				$doUnion = false;
				$doMultiply = false;
				$doOriginal = false;
				if (isset ( $_GET['union'] ) ) {
					$doUnion = true;
				} elseif (isset ( $_GET['multiply'] ) ) {
					$doMultiply = true;
				} elseif (isset ( $_GET['original'] ) ) {
					$doOriginal = true;
				}
				if ( !$doUnion && !$doMultiply && !$doOriginal) {
					$doUnion = true;
				}

				if ($doUnion) {
					$sql = "
						SELECT link_id, url, first_seen, first_user, weighted_count, count, localImage, remoteImage, first_user_fullname
						FROM 
							(
								(
									SELECT link_id, url, first_seen, first_user, weighted_count, count, localImage, remoteImage, first_user_fullname
									FROM openfuego_links
									WHERE weighted_count >= 10
										AND hiddenFlag='N'
										AND first_seen BETWEEN DATE_SUB(CONVERT_TZ(now(),'+00:00','-5:00'), INTERVAL 6 HOUR) AND CONVERT_TZ(now(),'+00:00','-5:00')
										AND url NOT LIKE '%nytimes.com%'
									ORDER BY weighted_count DESC
									LIMIT 20
								)
							UNION
								(
									SELECT link_id, url, first_seen, first_user, weighted_count, count, localImage, remoteImage, first_user_fullname
									FROM openfuego_links
									WHERE weighted_count >= 65
										AND hiddenFlag='N'
										AND first_seen BETWEEN DATE_SUB(CONVERT_TZ(now(),'+00:00','-5:00'), INTERVAL 24 HOUR) AND CONVERT_TZ(now(),'+00:00','-5:00')
										AND url NOT LIKE '%nytimes.com%'
									ORDER BY weighted_count DESC
								)
							) 
						AS combinedQueries
						ORDER BY weighted_count DESC
						LIMIT 20
					";
					$sth = $this->_dbh->prepare($sql);
					$sth->execute();
				} elseif ($doMultiply) {
					$sql = "
						SELECT first_seen, weighted_count, link_id, url, first_user,  count, localImage, remoteImage, first_user_fullname,
							TIMESTAMPDIFF(MINUTE,first_seen, CONVERT_TZ(now(),'+00:00','-5:00')) AS DiffMinutes,
							2 -TIMESTAMPDIFF(MINUTE,first_seen, CONVERT_TZ(now(),'+00:00','-5:00'))/1440 AS freshMultiplier,
							weighted_count * (2 -TIMESTAMPDIFF(MINUTE,first_seen, CONVERT_TZ(now(),'+00:00','-5:00'))/1440) AS freshAdjustedScore
						FROM openfuego_links
						WHERE TIMESTAMPDIFF(HOUR,first_seen, CONVERT_TZ(now(),'+00:00','-5:00')) < 24
							AND hiddenFlag='N'
							AND url NOT LIKE '%nytimes.com%'
						ORDER BY weighted_count * (2 -TIMESTAMPDIFF(MINUTE,first_seen, CONVERT_TZ(now(),'+00:00','-5:00'))/1440) DESC
						LIMIT 20
					";
					$sth = $this->_dbh->prepare($sql);
					$sth->execute();
				} elseif ($doOriginal) {
					$sql = "
						SELECT link_id, url, first_seen, first_user, weighted_count, count, localImage, remoteImage, first_user_fullname
						FROM openfuego_links
						WHERE weighted_count >= :min_weighted_count
							AND count > 1
							AND hiddenFlag='N'
							AND first_seen BETWEEN DATE_SUB(:date, INTERVAL :hours HOUR) AND :date
							AND url NOT LIKE '%nytimes.com%'
						ORDER BY weighted_count DESC
						LIMIT :limit;
					";
					$sth = $this->_dbh->prepare($sql);
					$sth->bindParam('date', $date, \PDO::PARAM_STR);
					$sth->bindParam('hours', $hours, \PDO::PARAM_INT);
					$sth->bindParam('min_weighted_count', $min_weighted_count, \PDO::PARAM_INT);
					$sth->bindParam('limit', $limit, \PDO::PARAM_INT);
					$sth->execute();
				}
			}
		} catch (\PDOException $e) {
			Logger::error($e);
			return FALSE;
		}
		$items = $sth->fetchAll(\PDO::FETCH_ASSOC);
		if (!$items) {
			return FALSE;
		}
		/****** DONE RUNNING SQL QUERY *****/



		/****** BEGIN CREATING ITEMS_FILTERED ARRAY 
			--note that we don't currently leverage the score/age/multiplier stuff as we've moved that to the sql queries
			--also note that we create the link_ids array here which we pass to the checkEmbedlyCache function later on.
		*****/
		
		$link_ids=[];  
		foreach ($items as $item) {
	
			$link_id = (int)$item['link_id'];
			$link_ids[]=$link_id;
	
			$url = $item['url'];
			$weighted_count = $item['weighted_count'];
			$multiplier = NULL;
			$score = NULL;
	
			$first_seen = $item['first_seen'];
			$first_seen = strtotime($first_seen);
			$age = $now - $first_seen;
			$age = $age / 3600; // to get hours
			$age = round($age, 1);
	
			$first_user = $item['first_user'];
	
			    if ($age <  ($hours/6))							{ $multiplier = 1.20-$age/$hours; }  // freshness boost!
			elseif ($age >= ($hours/6) && $age < ($hours/2))	{ $multiplier = 1.05-$age/$hours; }
			elseif ($age  > ($hours/2))							{ $multiplier = 1.01-$age/$hours; }
	
			$score = round($weighted_count * $multiplier);
	
			$items_filtered[] = array(
				'link_id' => $link_id,
				'url' => $url,
				'weighted_count' => $weighted_count,
				'first_seen' => $first_seen,
				'first_user' => $first_user,
				'first_user_fullname' => $item['first_user_fullname'],
				'age' => $age,
				'multiplier' => $multiplier,
				'score' => $score,
				'count' => $item['count'],
				'localImage' => $item['localImage'],
				'remoteImage' => $item['remoteImage']
			);
		}
		$scores = array();
		$ages = array();
		foreach ($items_filtered as $key => $item) {
			$scores[$key] = $scoring ? $item['score'] : $item['weighted_count'];
			$ages[$key] = $item['age'];
		}
		array_multisort($scores, SORT_DESC, $ages, SORT_ASC, $items_filtered);  // sort by score, then by age
		$items_filtered = array_slice($items_filtered, 0, $quantity);
		/****** DONE CREATING ITEMS_FILTERED ARRAY *****/

		/****** 
				BEGIN POPULATING LINK_META FROM EMBEDLYCACHE TABLE.  
				Hit the embedly API if necessary to update embedlyCache w/ new data
				Save remote images locally
		*****/	
		if ($metadata && defined('\OpenFuego\EMBEDLY_API_KEY') && \OpenFuego\EMBEDLY_API_KEY) {
	
			$metadata_params = is_array($metadata) ? $metadata : NULL;
			
			/* the goal is to fill out link_meta as [$key] => [$jsonObj] */
			$cachedLinkedIDs = self::checkEmbedlyCache($link_ids);

			$linkIDsFromURL=[];
			$linkIDsToFetch=[];
			$urlsToFetch=[];
			$link_meta = [];


			foreach ($items_filtered as $item_filtered) {
				$thisLinkID = $item_filtered['link_id'];
				$thisUrl = $item_filtered['url'];
				//if this item wasn't in the DB, append it to the list of URL's to grab from the embedly API
				if ( ! isset ( $cachedLinkedIDs[$thisLinkID] ) ) {
					$urlsToFetch[] = $thisUrl;
					$linkIDsToFetch[] = $thisLinkID;
					$linkIDsFromURL[$thisUrl]=$thisLinkID;
				} else {
					$cacheHit=$cachedLinkedIDs[$thisLinkID]['embedlyJSON'];
				//	var_dump($cacheHit);
					$link_meta[$thisLinkID]=json_decode($cacheHit, TRUE);
				}
			}
			
			if (count($urlsToFetch) > 0) {
				//echo "<!-- fetching these urls from EMBEDLY. " . implode($urlsToFetch) . "-->";
			}

			$urls_chunked = array_chunk($urlsToFetch, 20);  // Embedly handles maximum 20 URLs per request
			$urlCounter=-1;
			foreach ($urls_chunked as $urls_chunk) {
				$link_meta_json_str = Metadata::instantiate()->get($urls_chunk, $metadata_params);
				$link_meta_chunk = json_decode($link_meta_json_str, TRUE);
				foreach ($link_meta_chunk as $m) {
					$urlCounter++;
					$embedlyUrl=$m['url'];
					$currentLinkID=$linkIDsToFetch[$urlCounter];
					self::updateEmbedlyCache( $currentLinkID, json_encode($m));
					$link_meta[$currentLinkID]=$m;

					if ( isset ($m['thumbnail_url'] ) ) {
						$remoteImagePath=$m['thumbnail_url'];

						$imageSizeMax = \OpenFuego\IMAGE_SIZE_MAX; //Sets the max size for images to include as a thumbnail.
						$imageSizeMin = \OpenFuego\IMAGE_SIZE_MIN; //Sets the min size for images to include as a thumbnail.
						
						//if ($m['thumbnail_width'] <= $imageSizeMax && $m['thumbnail_height'] <= $imageSizeMax && $m['thumbnail_width'] >= $imageSizeMin){
						if ($m['thumbnail_width'] >= $imageSizeMin){
							$remoteImageArray=explode("/", $remoteImagePath);
							$remoteImageFilename=$remoteImageArray[count($remoteImageArray)-1];
							$extension="jpg";
							if (stripos($remoteImageFilename,"png")) {
								$extension="png";
							}
							$folderNum=$currentLinkID % 1000;
							$dirPath="/var/www/wordpress/wp-content/fuego/imgcache/$folderNum/";
							if (!file_exists($dirPath)) {
								mkdir($dirPath,0755);
							}
							$localFilename=$dirPath . $currentLinkID . ".$extension";;
							self::updateLinkImage($currentLinkID, $remoteImagePath, $localFilename);
							
							//file_put_contents($localFilename, file_get_contents($remoteImagePath));
							$img = new \Imagick($remoteImagePath);
					        $img->scaleImage(300,0);
					        $img->writeImage($localFilename);
						}
					}
				}
			}
			unset($urls, $urls_chunked, $urls_chunk, $link_meta_chunk);
		}
		/******* DONE POPULATING LINK_META ****/
		

		/**** BEGIN ATTACHING TWEET DATA TO ITEMS_FILTERED ****/
		$row_count = count($items_filtered);
		foreach ($items_filtered as $key => &$item_filtered) {
			$link_id = $item_filtered['link_id'];
			$url = $item_filtered['url'];
	
			preg_match('@^(?:https?://)?([^/]+)@i', $url, $matches);	
			$domain = $matches[1];
	
			if (strlen($domain) > 24) {
				preg_match('/[^.]+\.[^.]+$/', $domain, $matches);
				$domain = $matches[0];
			}
			
			$item_filtered['domain'] = $domain;
	
			$item_filtered['rank'] = $key + 1;
			
			$metadata = new Metadata();
			$status = $metadata->getTweet($link_id);
	
			$tw_id_str = $status['id_str'];
			$tw_screen_name = $status['screen_name'];
			$tw_text = $status['text'];
			$tw_profile_image_url = $status['profile_image_url'];
			$tw_profile_image_url_bigger = null;
			$tw_tweet_url = null;
			
			if ($tw_profile_image_url && $tw_screen_name && $tw_id_str) {
				$tw_profile_image_url_bigger = str_replace('_normal.', '_bigger.', $tw_profile_image_url);
				$tw_tweet_url = 'https://twitter.com/' . $tw_screen_name . '/status/' . $tw_id_str;
			}
	
			$item_filtered['tw_id_str'] = $tw_id_str;
	 		$item_filtered['tw_screen_name'] = $tw_screen_name;
			$item_filtered['tw_text'] = $tw_text;
			$item_filtered['tw_profile_image_url'] = $tw_profile_image_url;
			$item_filtered['tw_profile_image_url_bigger'] = $tw_profile_image_url_bigger;
			$item_filtered['tw_tweet_url'] = $tw_tweet_url;
	
			if (isset($link_meta)) {
				$item_filtered['metadata'] = $link_meta[$link_id];
			}
			
		}
		/**** DONE ATTACHING TWEET DATA TO ITEMS_FILTERED ****/

		return $items_filtered;
	}
}
