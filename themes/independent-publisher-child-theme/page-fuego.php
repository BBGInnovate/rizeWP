<?php
/**
 * The Template for displaying all single posts.
 *
 * @package Independent Publisher
 * @since   Independent Publisher 1.0 
  template name: Fuego

 */

$pageBodyID = "fuego";


get_header(); ?>

	<div id="primary" class="content-area">
		<main id="content" class="site-content" role="main">

		<?php 

		//require('../../fuego/init.php');
		require(dirname(__FILE__).'/../../fuego/init.php');
		use OpenFuego\app\Getter as Getter;
		$fuego = new Getter();
		$items = $fuego->getItems(20, 24, FALSE, TRUE, 2); // quantity, hours, scoring, metadata
		$counter=0;

		function twitterify($ret) {
			$ret = preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t< ]*)#", "\\1<a href=\"\\2\" target=\"_blank\">\\2", $ret);
			$ret = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r< ]*)#", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2", $ret);
			$ret = preg_replace("/@(\w+)/", "<a href=\"http://www.twitter.com/\\1\" target=\"_blank\">@\\1</a>", $ret);
			$ret = preg_replace("/#(\w+)/", "<a href=\"http://search.twitter.com/search?q=\\1\" target=\"_blank\">#\\1</a>", $ret);
			return $ret;
		}

		foreach ($items as $key => $item) {
			$counter=$counter+1;
			$title = $item['tw_text'];
			$url = $item['url'];
			$desc = '<p>'.$item['tw_text'].'</p>';
			$author = $item['tw_screen_name'];
			$image = "";
			$provider_name = "Africa Rizing";
			$provider_url = "https://africa.rizing.org";

			$weightedCount = $item['weighted_count'];

			$twitterImage = $item['tw_profile_image_url'];;
			$tweetUrl = "";

			$quoteMakerName = "";
			$quoteMakerHandle = "";
			$quotedMakerImage = "";
			$quotedTweet = "";
			$quotedTweetUrl = "";

			$convertedTime = $item['first_seen'];
			$dt = new DateTime("@$convertedTime");
			/*$dateStamp = $dt->format('Y-m-d H:i:s');*/
			$dateStamp = $dt->format('F d, Y g:i e');

			$imageSize = false;

			$isTwitter = false;

			/* often times some metadata values are set and others aren't, so we check each one.  The fuego backend process fills this section using an embed.ly api key */
			if ( isset ($item['metadata']) ) {
				$m = $item['metadata'];

				if ($m['provider_name'] == 'Twitter'){
					/*If it's a quoted tweet... */
					$isTwitter = true;

					//Remove twitter link to quoted material
					$desc = preg_replace($'/(https:\/\/t\.co\/)[A-z0-9\.]*/', '', $desc);

					//Convert links, #hashtags and @name to clickable links.
					$desc = twitterify($desc);

					$twitterImage=$item['tw_profile_image_url_bigger'];

					$tweetUrl = $item[tw_tweet_url];


					/*person quoted*/
					if ( isset ( $m['title'] ) ) {
						$quoteMakerName = $m['title'];
						$quoteMakerName = str_replace("on Twitter","", $quoteMakerName);
					}

					if ( isset ( $m['author_name'] ) ) {
						$quoteMakerHandle = $m['author_name'];	
					}

					if ( isset ($m['url'] ) ) {
						$quotedTweetUrl = $m['url'];
					}
					
					if ( isset ($m['description'] ) ) {
						$quotedTweet = $m['description'];
						$quotedTweet = twitterify($quotedTweet);
					}

					if ( isset ($m['thumbnail_url'] ) ) {
						$quoteMakerImage = $m['thumbnail_url'];
					}

				}else{
					if ( isset ( $m['title'] ) ) {
						$title = $m['title'];	
					}

					if ( isset ($m['url'] ) ) {
						$url = $m['url'];
					}
					
					if ( isset ($m['description'] ) ) {
						$desc = '<p>'.$m['description'].'</p>';
					}

					if ( isset ($m['thumbnail_url'] ) ) {
						$image=$m['thumbnail_url'];
					}
					if ( isset ($m['thumbnail_width'] ) ){
						if ($m['thumbnail_width'] <= 700 && $m['thumbnail_height'] <= 500){
							$imageSize = true;
						}
					}

					if ( isset ($m['provider_name'] ) ) {
						$provider_name=$m['provider_name'];
					}

					if ( isset ($m['provider_url'] ) ) {
						$provider_url=$m['provider_url'];
					}
				}
			}

		?>
			<article data-weighted-count='<?php echo $weightedCount ?>'>

				<?php if (!$isTwitter){ ?>
					<header class='entry-header'>
						<h5 class='entry-category'><a href='<?php echo $provider_url; ?>'><?php echo $provider_name; ?></a></h5>
						<h1 class='entry-title'><?php echo "<a href='$url'>$title</a>"; ?></h1>
					</header>
					<div class='entry-content'>
					<?php 
						if ($image != "" && $imageSize) {
							echo "<a href='$url'>"; 
							echo "<div class='listThumbnail' style='background-image: url($image);'></div>";
							echo "</a>"; 
						}
						echo "<a href='$url'>"; 
						echo $desc; 

						echo "</a>"; 
					?>
					</div>
					<footer class="entry-meta" style='border-top:none;'>
						<span class="byline">
							<span class="author vcard">first shared by <a class="url fn n" href="http://wprize/wprize/author/jflowers45/" title="View all posts by jflowers45" rel="author">
							<?php echo "<span class='twitterImageCredit' style='background-image: url(".$twitterImage.");'>" ?>
								<img src='../wp-content/images/transparentSquare.png'>
							</span>
							<?php echo "<a href='http://twitter.com/$author'>@$author</a>"; ?></span>
						</span>						
						<span class="sep sep-byline"> | </span>
						<time class="entry-date" datetime="2015-10-14T16:56:08+00:00" itemprop="datePublished" pubdate="pubdate"><?php echo $dateStamp ?></time>
					</footer>

				<?php } else { ?>

					<header class='entry-header'>
						<h5 class='entry-category'><a href='<?php echo $tweetUrl; ?>' style='float:none;'>OVERHEARD ON TWITTER</a></h5>
					</header>
					<div class='entry-content twitter-conversation'>
						<a href='https://twitter.com/<?php echo $author; ?>'>
							<div class='twitterProfilePhoto' style='background-image:url(<?php echo $twitterImage ?>)'>
								<img src='../wp-content/images/transparentSquare.png'>
							</div>
						</a>
						<div class='tweetAuthor'>
							<p class='tweetAuthorName'>
								<?php echo $author; ?>
							</p>
							<p style='display: block; margin-bottom:0;'>
								<a href='https://twitter.com/<?php echo $author; ?>'>@<?php echo $author; ?></a>
							</p>
						</div>
						<div class='clearAll'></div>
						<div class='tweet' style=''>
							<?php echo $desc; ?>
							<?php /* echo preg_replace($pattern, $replacement, $desc);  */ ?>
						</div>

						<div class='clearAll'></div>

						<div class='quotedTweet'>
							<a href='https://twitter.com/<?php echo $quoteMakerHandle; ?>'>
								<div class='twitterProfilePhoto' style='background-image:url(<?php echo $quoteMakerImage; ?>)' >
									<img src='../wp-content/images/transparentSquare.png'>
								</div>
							</a>
							<div class='quoteMaker'>
								<p class='quoteMakerName'><?php echo $quoteMakerName; ?> </p>
								<p>
									<a href='https://twitter.com/<?php echo $quoteMakerHandle; ?>'>
										@<?php echo $quoteMakerHandle; ?>
									</a>
								</p>
							</div>
							<div class='clearAll'></div>
							<div class='quotedTweetText'>
								<p><?php echo $quotedTweet; ?></p>
							</div>
						</div>
					</div>
					<footer class="entry-meta" style='border-top:none;'>
						<span class="byline"><span class="author vcard">first shared by <a class="url fn n" href="http://wprize/wprize/author/jflowers45/" title="View all posts by jflowers45" rel="author"><?php echo "<a href='http://twitter.com/$author'>@$author</a>"; ?></span></span>						
						<span class="sep sep-byline"> | </span>
						<time class="entry-date" datetime="2015-10-14T16:56:08+00:00" itemprop="datePublished" pubdate="pubdate"><?php echo $dateStamp ?></time>
					</footer>

				<?php } ?>
			</article>
		<?php 

		}


		?>

		</main>
		<!-- #content .site-content -->
	</div><!-- #primary .content-area -->

<?php 

/* 
			<article>
				<header class='entry=header'>
					<h5 class='entry-category'><a>fuego</a></h5>
					<h1 class='entry-title'>title</h1>
				</header>
				<div class='entry-content'>body</div>
				<footer class="entry-meta" style='border-top:none;'>
					<span class="byline"><span class="author vcard"><a class="url fn n" href="http://wprize/wprize/author/jflowers45/" title="View all posts by jflowers45" rel="author">twitterHandle</a></span></span>						
					<span class="sep sep-byline"> | </span>
					<time class="entry-date" datetime="2015-10-14T16:56:08+00:00" itemprop="datePublished" pubdate="pubdate">date</time>
				</footer>
			</article>*/


get_sidebar(); ?>
<?php get_footer(); ?>