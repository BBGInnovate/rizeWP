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
			$desc = $item['tw_text'];
			$author = $item['tw_screen_name'];
			$image="";
			/* often times some metadata values are set and others aren't, so we check each one.  The fuego backend process fills this section using an embed.ly api key */
			if ( isset ($item['metadata']) ) {
				$m = $item['metadata'];

				if ( isset ( $m['title'] ) ) {
					$title = $m['title'];	
				}
				if ( isset ($m['url'] ) ) {
					$url = $m['url'];
				}
				
				if ( isset ($m['description'] ) ) {
					$desc = $m['description'];
				}

				if ( isset ($m['thumbnail_url'] ) ) {
					$image=$m['thumbnail_url'];
				}
			}

		?>
			<article>
				<header class='entry=header'>
					<h5 class='entry-category'><a>fuego</a></h5>
					<h1 class='entry-title'><?php echo "<a href='$url'>$title</a>"; ?></h1>
				</header>
				<div class='entry-content'>
				<?php 
					if ($image != "") {
					echo "<div class='listThumbnail' style='background-image: url($image);'></div>";
					}

					//echo $desc; 

					?>
				</div>
				<footer class="entry-meta" style='border-top:none;'>
					<span class="byline"><span class="author vcard">first shared by <a class="url fn n" href="http://wprize/wprize/author/jflowers45/" title="View all posts by jflowers45" rel="author"><?php echo "<a href='http://twitter.com/$author'>@$author</a>"; ?></span></span>						
					<span class="sep sep-byline"> | </span>
					<time class="entry-date" datetime="2015-10-14T16:56:08+00:00" itemprop="datePublished" pubdate="pubdate">date</time>
				</footer>
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