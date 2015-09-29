<?php

/**
 template name: In Focus
 * The template for displaying our "in focus" view.
 * Display all featured posts.  Cut them off @ 6.  Allow one sticky post
 * @package Independent Publisher
 * @since   Independent Publisher 1.0
 */

$pageBodyID = "inFocus";
get_header(); ?> 

	<div id="primary" class="content-area">
		<main id="content" class="site-content" role="main">
			<p><em>Here are the topics we're focused on covering right now.</em></p>
			<?php
				//$query_string."&featured=yes"
				query_posts("posts_per_page=6&orderby=post_date&order=desc&featured=yes"); 
				global $wp_query; 
				$totalPostsInCategory = $wp_query->found_posts;
				
				$postNumInCategory=0;
				if (have_posts()) : while (have_posts()) : 
					the_post(); 
					get_template_part( 'content', get_post_format() );
				endwhile; endif; // done our wordpress loop. Will start again for each category ?>

		</main>
		<!-- #content .site-content -->
	</div><!-- #primary .content-area -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>