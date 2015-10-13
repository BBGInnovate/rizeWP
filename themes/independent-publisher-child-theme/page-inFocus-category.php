<?php

/**
 template name: In Focus (Category Version)
 * The template for displaying our "in focus" view.
 * In this version we just show the most recent 'in focus' post
 * @package Independent Publisher
 * @since   Independent Publisher 1.0
 */

$pageBodyID = "inFocus";
get_header(); ?> 

	<div id="primary" class="content-area">
		<main id="content" class="site-content" role="main">
			<h1 class="page-title">Trending</h1>
			<?php

				$cat_id=get_cat_id('Trending');
				if ($cat_id ==0) {
					//echo "there is no in focus category ... please create one locally"; 
				} else {
					$catDesc=strip_tags(category_description($cat_id));
					if ($catDesc != "") {
						echo "<p class='intro'>$catDesc</p>";	
					}
					$qParams=array( 
						'post_type' => array('post'),
						'posts_per_page' => 1,
						'orderby' => 'post_date',
						'order' => 'desc',
						'cat' => $cat_id,
					);
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