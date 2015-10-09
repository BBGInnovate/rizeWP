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
			<h1 class="page-title">In Focus</h1>
			<p class="intro">Our daily digest of links, trends, hashtags and the stories that matter most</p>
			<?php

				$qParams=array( 
					'post_type' => array('post', 'page'),
					'posts_per_page' => 10,
					'orderby' => 'post_date',
					'order' => 'desc',
					'featured' => 'yes'
				);
				query_posts($qParams);
				//query_posts("posts_per_page=10&post_type=page,post&orderby=post_date&order=desc&featured=yes"); 
				
				/* determine whether any of these posts are pinned */
				$featuredInFocusPostID=0;
				if (have_posts()) : while (have_posts()) : 
					the_post();
					$isPinnedFocusPost = get_post_meta(get_the_ID(), 'pinned_in_focus_post', true);
        			if ( ( $isPinnedFocusPost === '1' || $isPinnedFocusPost === 'true' ) ) {
						$featuredInFocusPostID=get_the_ID();
						get_template_part( 'content', get_post_format() );
					}
				endwhile; endif; // done our wordpress loop. Will start again for each category 
				rewind_posts();

				$postNumInCategory=0;
				$counter=0;
				if (have_posts()) : while (have_posts()) : 
					the_post(); 
					if ($featuredInFocusPostID != get_the_ID()) {
						$counter=$counter+1;
						if ($counter<5) {
							get_template_part( 'content', get_post_format() );	
						}
					}
				endwhile; endif; // done our wordpress loop. Will start again for each category ?>

		</main>
		<!-- #content .site-content -->
	</div><!-- #primary .content-area -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>