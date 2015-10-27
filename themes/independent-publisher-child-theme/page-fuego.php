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
		foreach ($items as $key => $item) {
			$counter=$counter+1;
			$title = $item['tw_text'];
			$url = $item['url'];
			$desc = '<p>'.$item['tw_text'].'</p>';
			$author = $item['tw_screen_name'];
			$image = "";
			$provider_name = "Africa Rizing";
			$provider_url = "https://africa.rizing.org";


			$twitterImage = "";
			$tweetUrl = "";

			$quoteMakerName = "";
			$quoteMakerHandle = "";
			$quotedMakerImage = "";
			$quotedTweet = "";
			$quotedTweetUrl = "";

			$isTwitter = false;

			/* often times some metadata values are set and others aren't, so we check each one.  The fuego backend process fills this section using an embed.ly api key */
			if ( isset ($item['metadata']) ) {
				$m = $item['metadata'];

				if ($m['provider_name'] == 'Twitter'){
					$isTwitter = true;

					$twitterImage=$item['tw_profile_image_url_bigger'];

					$tweetUrl = $item[tw_tweet_url];

					/*If it's a quoted tweet... */
					/*
					if ( isset ( $m['title'] ) ) {
						$quoteMakerName = $m['title'];	
					}

					if ( isset ( $m['author_name'] ) ) {
						$quoteMakerHandle = $m['author_name'];	
					}

					if ( isset ($m['url'] ) ) {
						$quotedTweetUrl = $m['url'];
					}
					
					if ( isset ($m['description'] ) ) {
						$quotedTweet = $m['description'];
					}

					if ( isset ($m['thumbnail_url'] ) ) {
						$quotedMakerImage = $m['thumbnail_url'];
					}
					*/


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

					if ( isset ($m['provider_name'] ) ) {
						$provider_name=$m['provider_name'];
					}

					if ( isset ($m['provider_url'] ) ) {
						$provider_url=$m['provider_url'];
					}
				}
			}

		?>
			<article>
				<?php if (!$isTwitter){ ?>
				<header class='entry-header'>
					<h5 class='entry-category'><a href='<?php echo $provider_url; ?>'><?php echo $provider_name; ?></a></h5>
					<h1 class='entry-title'><?php echo "<a href='$url'>$title</a>"; ?></h1>
				</header>
				<div class='entry-content'>
				<?php 
					if ($image != "") {
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
					<span class="byline"><span class="author vcard">first shared by <a class="url fn n" href="http://wprize/wprize/author/jflowers45/" title="View all posts by jflowers45" rel="author"><?php echo "<a href='http://twitter.com/$author'>@$author</a>"; ?></span></span>						
					<span class="sep sep-byline"> | </span>
					<time class="entry-date" datetime="2015-10-14T16:56:08+00:00" itemprop="datePublished" pubdate="pubdate">date</time>
				</footer>


				<?php } else { ?>


				<header class='entry-header'>
					<h5 class='entry-category'><a href='<?php echo $tweetUrl; ?>' style='float:none;'>OVERHEARD ON TWITTER</a></h5>
				</header>
				<div class='entry-content'>
					<a href='https://twitter.com/<?php echo $author; ?>'>
						<div class='twitterUserPhoto' style='background:url(<?php echo $twitterImage ?>) no-repeat center center /cover; width: 10%; height: auto; border-radius: 50%; float:left;'>
							<img src='../images/transparentSquare.png'>
						</div>
					</a>
					<div style='float: left; max-width:600px;'>
						<p style='display: inline-block; vertical-align: 30%;'>
							<a href='https://twitter.com/<?php echo $author; ?>'>@<?php echo $author; ?></a>
						</p>
						<?php echo $desc; ?>
					</div>
					<div class='clearAll'></div>
					<div class='quotedTweet' style='padding:20px; border-radius: 5px; background-color: #F1F1F1; width: 90%; margin: 10px 5%;'>
						<a href='https://twitter.com/<?php echo $quoteMakerHandle; ?>'>
							<div class='twitterUserPhoto' style='background:url(<?php echo $quoteMakerImage; ?>) no-repeat center center /cover; width: 70px; height: 70px; border-radius: 35px; display: inline-block;'></div>
							<p style='display: inline-block; vertical-align: 30%;'><?php echo $quoteMakerName; ?> <span>| @<?php echo $quoteMakerHandle; ?></span></p>
						</a>
						<p><?php echo $quotedTweet; ?></p>
					</div>
				</div>
				<footer class="entry-meta" style='border-top:none;'>
					<span class="byline"><span class="author vcard">first shared by <a class="url fn n" href="http://wprize/wprize/author/jflowers45/" title="View all posts by jflowers45" rel="author"><?php echo "<a href='http://twitter.com/$author'>@$author</a>"; ?></span></span>						
					<span class="sep sep-byline"> | </span>
					<time class="entry-date" datetime="2015-10-14T16:56:08+00:00" itemprop="datePublished" pubdate="pubdate">date</time>
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