<?php
/**
 * The Template for displaying all single posts.
 *
 * @package Independent Publisher
 * @since   Independent Publisher 1.0 
  template name: Fuego

 */

$pageBodyID = "fuego";
require(dirname(__FILE__).'/../../fuego/init.php');
use OpenFuego\app\Getter as Getter;
$fuego = new Getter();
		
if (  isset( $_GET['hideLink'] ) && current_user_can('publish_posts') ) {
	$fuego -> hideLink( $_GET['hideLink'] );
}


get_header(); ?>

	<div id="primary" class="content-area">
		<main id="content" class="site-content" role="main">

		<?php 

		//require('../../fuego/init.php');
		$items = $fuego->getItems(20, 24, FALSE, TRUE, 2); // quantity, hours, scoring, metadata
		$counter=0;

		function twitterify($ret) {
			$ret = str_replace("https://medium.com/@","https://medium.com/", $ret);

			$ret = preg_replace("#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t< ]*)#", "\\1<a href=\"\\2\" target=\"_blank\">\\2", $ret);
			$ret = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r< ]*)#", "\\1<a href=\"https://\\2\" target=\"_blank\">\\2", $ret);
			$ret = preg_replace("/@(\w+)/", "<a href=\"https://www.twitter.com/\\1\" target=\"_blank\">@\\1</a>", $ret);
			$ret = preg_replace("/#(\w+)/", "<a href=\"https://search.twitter.com/search?q=\\1\" target=\"_blank\">#\\1</a>", $ret);
			return $ret;
		}

		function ago($time) {
			$periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
			$lengths = array("60","60","24","7","4.35","12","10");

			$now = time();

				$difference     = $now - $time;
				$tense         = "ago";

			for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) {
			   $difference /= $lengths[$j];
			}

			$difference = round($difference);

			if($difference != 1) {
			   $periods[$j].= "s";
			}

			return "$difference $periods[$j] ago ";
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
			$linkID=$item['link_id'];

			$weightedCount = $item['weighted_count'];

			$twitterImage = $item['tw_profile_image_url'];;
			$tweetUrl = "";

			$quoteMakerName = "";
			$quoteMakerHandle = "";
			$quotedMakerImage = "";
			$quotedTweet = "";
			$quotedTweetUrl = "";

			$convertedTime = $item['first_seen'];

			$agoTime = ago($item['first_seen']);

			$dt = new DateTime("@$convertedTime");
			/*$dateStamp = $dt->format('Y-m-d H:i:s');*/
			$dateStamp = $dt->format('F d, Y g:i');

			$imageSize = false; //Test if the image falls within a range of sizes (not too big, not too small).
			$imageSizeMax = 500; //Sets the max size for images to include as a thumbnail.
			$imageSizeMin = 125; //Sets the min size for images to include as a thumbnail.

			$isTwitter = false;

			/* often times some metadata values are set and others aren't, so we check each one.  The fuego backend process fills this section using an embed.ly api key */
			if ( isset ($item['metadata']) ) {
				$m = $item['metadata'];

				if ($m['provider_name'] == 'Twitter'){
					/*If it's a quoted tweet... */
					$isTwitter = true;

					//Remove twitter link to quoted material
					$desc = preg_replace('/(https:\/\/t\.co\/)[A-z0-9\.]*/', '', $desc);

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

						/*trying to remove offending news org credits */
						$search = array(' - BBC News', ' - BBC World Service', ' - CNN.com', ' - FT.com', ' - CNNPolitics.com');
						$title = str_replace($search, '', $title);
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
						if ($m['thumbnail_width'] <= $imageSizeMax && $m['thumbnail_height'] <= $imageSizeMax && $m['thumbnail_width'] >= $imageSizeMin){
							$imageSize = true;
						}
					}

					if ( isset ($m['provider_name'] ) ) {
						$provider_name=$m['provider_name'];

						/* fix bad capitalization on BBC */
						$provider_name = str_replace('Bbc', 'BBC', $provider_name);
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
							<span class="author vcard"><span class='firstShared'>first shared by </span><a class="url fn n" href="http://twitter.com/<?php echo $author ?>" rel="author">
							<?php echo "<span class='twitterImageCredit' style='background-image: url(".$twitterImage.");'>" ?>
								<img src='../wp-content/images/transparentSquare.png'>
							</span>
							<?php echo "<a href='http://twitter.com/$author'>@$author</a>"; ?></span>
						</span>	
						<span class="sep sep-byline"> | </span>
						<time class="entry-date" itemprop="datePublished" pubdate="pubdate"><?php echo $agoTime ?></time>
					</footer>

				<?php } else { ?>

					<header class='entry-header'>
						<h5 class='entry-category'><a href='<?php echo $tweetUrl; ?>' style='float:none;'>Overheard on Twitter</a></h5>
					</header>
					<div class='entry-content twitter-conversation'>
						<a href='https://twitter.com/<?php echo $author; ?>' target='_blank'>
							<div class='twitterProfilePhoto' style='background-image:url(<?php echo $twitterImage ?>)'>
								<img src='../wp-content/images/transparentSquare.png'>
							</div>
						</a>
						<div class='tweetAuthor'>
							<p class='tweetAuthorName'>
								<?php echo $author; ?>
							</p>
							<p style='display: block; margin-bottom:0;'>
								<a href='https://twitter.com/<?php echo $author; ?>' target='_blank'>@<?php echo $author; ?></a>
							</p>
						</div>
						<div class='clearAll'></div>
						<div class='tweet' style=''>
							<?php echo $desc; ?>
							<?php /* echo preg_replace($pattern, $replacement, $desc);  */ ?>
						</div>

						<div class='clearAll'></div>

						<div class='quotedTweet'>
							<a href='https://twitter.com/<?php echo $quoteMakerHandle; ?>' target='_blank'>
								<div class='twitterProfilePhoto' style='background-image:url(<?php echo $quoteMakerImage; ?>)' >
									<img src='../wp-content/images/transparentSquare.png'>
								</div>
							</a>
							<div class='quoteMaker'>
								<p class='quoteMakerName'><?php echo $quoteMakerName; ?> </p>
								<p>
									<a href='https://twitter.com/<?php echo $quoteMakerHandle; ?>' target='_blank'>
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
						<?php echo $agoTime ?>
						<span class="byline"><span class="author vcard"><span class='firstShared'>first shared by </span><a class="url fn n" href="http://twitter.com/<?php echo $author ?>" rel="author"><?php echo "<a href='http://twitter.com/$author'>@$author</a>"; ?></span></span>						
						<span class="sep sep-byline"> | </span>
						<time class="entry-date" itemprop="datePublished" pubdate="pubdate"><?php echo $agoTime ?></time>
					</footer>

				<?php } ?>

				<?php 

					if (current_user_can('publish_posts')) {
						echo "<a href='/fuego?hideLink=$linkID' class='hideLink'>Hide this link</a><BR>";
					}
				?>
			</article>
		<?php 

		}


		?>

		<script type="text/javascript">
			jQuery(document).ready(function(){
				setTimeout(function() {window.location.reload();}, 300000);
			});
		</script>

		</main>
		<!-- #content .site-content -->
	</div><!-- #primary .content-area -->

<?php 

get_sidebar(); ?>
<?php get_footer(); ?>