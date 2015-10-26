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
			$iframe = "";
			/* often times some metadata values are set and others aren't, so we check each one.  The fuego backend process fills this section using an embed.ly api key */
			if ( isset ($item['metadata']) ) {
				$m = $item['metadata'];

				if ($m['provider_name'] == 'Twitter'){
					$iframe = $item['tw_tweet_url'];
				}else{

					if ( isset ( $m['title'] ) ) {
						$title = $m['title'];	
					}

					/*If it's a quoted tweet, show the url for the tweet that's quoting it. */
					if ( isset ($m['url'] ) ) {
						/*
						if ($m['provider_name'] == 'Twitter'){
							$url = $item['tw_tweet_url'];
						} else {
						*/
						$url = $m['url'];
						/*}*/
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
				<?php if ($iframe == ""){ ?>
				<header class='entry=header'>
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

				<a href='https://twitter.com/drawinghands'>
					<div class='twitterUserPhoto' style='background:url(http://pbs.twimg.com/profile_images/649550853574144000/fgcPWqbU_normal.jpg) no-repeat center center /cover; width: 25px; height: 25px;'></div>
				</a>
				<div><a href='https://twitter.com/drawinghands'>@drawinghands</a></div>
				<div><p><a href=''>The text of the tweet.</a></p></div>
				<div class='quotedTweet' style='padding:20px; border-radius: 5px; background-color: #CCC;'>
					<a href='https://twitter.com/drawinghands'>
						<div class='twitterUserPhoto' style='background:url(http://pbs.twimg.com/profile_images/649550853574144000/fgcPWqbU_normal.jpg) no-repeat center center /cover; width: 25px; height: 25px;'></div>
					</a>
					<div>Brian Williamson <span>| <a href='https://twitter.com/drawinghands'>@quotedPerson</a></span></div>
					<p>The quoted tweet the quick brown fox jumped over the lazy dog's back.</p>
				</div>

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