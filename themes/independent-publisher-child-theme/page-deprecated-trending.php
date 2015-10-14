<?php

/**
 template name: Deprecated - Trending old style
 * The template for displaying our "in focus" view.
 * In this version we just show the most recent 'in focus' post
 * @package Independent Publisher
 * @since   Independent Publisher 1.0
 */

$pageBodyID = "trending";
$trendingCatID=get_cat_id('Trending');

/**** GET A SPECIAL PERMALINK FOR THE TWITTER AND FB SHARE BUTTONS *****/
$qParams=array( 
	'post_type' => array('post'),
	'posts_per_page' => 1,
	'orderby' => 'post_date',
	'order' => 'desc',
	'cat' => $trendingCatID,
);
//echo "catid " . $cat_id . "<BR>";
query_posts($qParams);

if (have_posts()) : while (have_posts()) : 
	the_post(); 
	$trendingPostPermalink=post_permalink(get_the_id());
	$ogUrl=$trendingPostPermalink;
endwhile; endif; 
rewind_posts();
/***** DONE GETTING SPECIAL PERMALINK ******/

get_header(); ?> 
	<!-- temporary fix -->
	<div id="logoOnPostPages">
		<a class="site-logo" href="https://africa.rizing.org/" title="Africa Rizing" rel="home">
			<img class="no-grav" src="https://africa.rizing.org/wp-content/uploads/2015/10/cropped-Rize-socialprofiles_500.png" height="501" width="501" alt="Africa Rizing">
		</a>
		<h1 class="site-title">
			<a href="https://africa.rizing.org/" title="Africa Rizing" rel="home">Africa <span class="orangeHighlight">Rizing</span></a>
		</h1>
		<h2 class="site-description">Connecting the next generation of global influencers from across the Continent, and around the world to engage in, ‘a smarter conversation’</h2>
	</div>
	<style type="text/css">
		#logoOnPostPages a.site-logo {
	    	margin-left: 0px;
		}
		@media only screen and (min-width: 1200px) {
			#logoOnPostPages{display: none;}
		}
	</style>
	<!-- end temporary fix -->

	
	<div id="primary" class="content-area">
		<main id="content" class="site-content" role="main">
			<h1 class="page-title">Trending</h1>
			<?php

				
				if ($trendingCatID ==0) {
					//echo "there is no in focus category ... please create one locally"; 
				} else {
					$catDesc=strip_tags(category_description($trendingCatID));
					if ($catDesc != "") {
						echo "<p class='intro'>$catDesc</p>";	
					}
					//echo "catid " . $cat_id . "<BR>";
					query_posts($qParams);

					if (have_posts()) : while (have_posts()) : 
						the_post(); 
						get_template_part( 'content', 'single' );	
					endwhile; endif; // done our wordpress loop. Will start again for each category 
				}
			?>

		</main>
		<!-- #content .site-content -->
	</div><!-- #primary .content-area -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>