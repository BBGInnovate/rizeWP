<?php

function __custom_independent_publisher_full_width_featured_image_size() {
    return "full";
}

add_filter( 'independent_publisher_full_width_featured_image_size', '__custom_independent_publisher_full_width_featured_image_size' );

/*
 * You can add your own functions here. You can also override functions that are
 * called from within the parent theme. For a complete list of function you can
 * override here, please see the docs:
 *
 * https://github.com/raamdev/independent-publisher#functions-you-can-override-in-a-child-theme
 *
 */

function independent_publisher_site_info() {
	?>
	<?php if ( get_header_image() ) : ?>
		<a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
			<img class="no-grav" src="<?php echo esc_url( get_header_image() ); ?>" height="<?php echo absint( get_custom_header()->height ); ?>" width="<?php echo absint( get_custom_header()->width ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" />
		</a>
	<?php endif; ?>
	<h1 class="site-title">
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">Africa <span class="orangeHighlight">Rizing</span></a>
	</h1>
	<h2 class="site-description"><?php bloginfo( 'description' ); ?></h2>
	<?php get_template_part( 'menu', 'social' ); ?>
<?php
}

/*
 * Uncomment the following to add a favicon to your site. You need to add favicon
 * image to the images folder of Independent Publisher Child Theme for this to work.
 */
/*
function blog_favicon() {
  echo '<link rel="Shortcut Icon" type="image/x-icon" href="'.get_bloginfo('stylesheet_directory').'/images/favicon.ico" />' . "\n";
}
add_action('wp_head', 'blog_favicon');
*/

/*
 * Add version number to main style.css file with version number that matches the
 * last modified time of the file. This helps when making frequent changes to the
 * CSS file as the browser will always load the newest version.
 */
/*
function independent_publisher_stylesheet() {
	wp_enqueue_style( 'independent-publisher-style', get_stylesheet_uri(), '', filemtime( get_stylesheet_directory() . '/style.css') );
}
*/

/*
 * Modifies the default theme footer.
 * This also applies the changes to JetPack's Infinite Scroll footer, if you're using that module.
 */
function independent_publisher_footer_credits() {
	$creativecommons = '<div id="creativecommons"><a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/4.0/"><img alt="Creative Commons License" style="border-width:0" src="https://i.creativecommons.org/l/by-nc-sa/4.0/80x15.png" /></a><br />This work by <a xmlns:cc="http://creativecommons.org/ns#" href="http://africa.rizing.org" property="cc:attributionName" rel="cc:attributionURL">Africa Rizing</a> is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/4.0/">Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License</a>.</div>';

	$my_custom_footer = '<div id="attribution">Project Rize is a new digital media startup developed by <a href="http://bbg.gov/" target="_blank">US International Media</a> in partnership with <a href="http://voanews.com/" target="_blank">VOA News</a> to promote open discourse, democratic ideals and sustainable, civil societies throughout the world.</div>'.$creativecommons;
	return $my_custom_footer;
}

add_image_size( 'mugshot', 200, 200 ); // 220 pixels wide by 180 pixels tall, soft proportional crop mode
add_filter( 'image_size_names_choose', 'my_custom_sizes' );

function my_custom_sizes( $sizes ) {
    return array_merge( $sizes, array(
        'mugshot' =>'Mugshot'
    ) );
}

/**
	 * Show Full Width Featured Image on single pages if post has full width featured image selected
	 * or if Auto-Set Featured Image as Post Cover option is enabled
	 */
	function independent_publisher_full_width_featured_image() {
		if ( independent_publisher_has_full_width_featured_image() ) {
			while ( have_posts() ) : the_post();
				if ( has_post_thumbnail() ) :
					if ( independent_publisher_post_has_post_cover_title() ):
						
						//tevkori_get_srcset_array( $id, $size = 'thumbnail' ) {
						//global $wpdb;
						
						$tempSources = tevkori_get_srcset_array( get_post_thumbnail_id(), 'full');
						//sources aren't automatically in numeric order.  ksort does the trick.
						ksort($tempSources);

						echo "<style>\n";
						$counter=0;
						$prevWidth=0;
						foreach( $tempSources as $key => $tempSource ) {
							//we're workign with an array of sourceset entries ... gotta get rid of the width part
							$counter++;
							
							$tempSource = preg_replace( '/(.*)\s(.*)/', '$1', $tempSource );	
							if ($counter == 1) {
								echo "\t.postCoverResponsive { background-image: url($tempSource) !important; }\n";
							} elseif ($counter < count($tempSources)) {
								echo "\t@media (min-width: " . ($prevWidth+1) . "px) and (max-width: " . $key . "px) {\n";
								echo "\t\t.postCoverResponsive { background-image: url($tempSource) !important; }\n";
								echo "\t}\n";
							} else {
								echo "\t@media (min-width: " . ($prevWidth+1) . "px) {\n";
								echo "\t\t.postCoverResponsive { background-image: url($tempSource) !important; }\n";
								echo "\t}\n";
							}
							$prevWidth=$key;
						}
						echo "</style>\n";

						$featured_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), apply_filters( 'independent_publisher_full_width_featured_image_size', 'independent_publisher_post_thumbnail' ));
						$featured_image_url = $featured_image_url[0];
						$postCoverTitleWrapperExtraClass="postCoverTitleAlwaysBelow";
						$post_has_cover_title_rize 	= get_post_meta( get_the_ID(), 'post_cover_overlay_post_title_rize', true);
						if ( ( $post_has_cover_title_rize === '1' || $post_has_cover_title_rize === 'true' ) ) {
							$postCoverTitleWrapperExtraClass="";
						}
					?>
						<div class="post-cover-title-wrapper <?php echo $postCoverTitleWrapperExtraClass; ?>">
							<div class="post-cover-title-image postCoverResponsive" ></div>
								<div class="post-cover-title-head">
									<header class="post-cover-title">
										<?php if ( independent_publisher_categorized_blog() ) { ?>
										<h5 class='entry-category'>
											<?php echo independent_publisher_post_categories( '', true ); ?>
										</h5>
										<?php } ?>
										<h1 class="entry-title" itemprop="name">
											<?php echo get_the_title(); ?>
										</h1>
										<?php $subtitle = get_post_meta(get_the_id(), 'independent_publisher_post_cover_subtitle', true); ?>
										<?php if ( $subtitle ): ?>
											<h2 class="entry-subtitle">
												<?php echo $subtitle;?>
											</h2>
										<?php endif; ?>
										<?php if ( ! is_page() ) : ?>
											<h3 class="entry-title-meta">
												<span class="entry-title-meta-author">
													<a class="author-avatar" href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>">
														<?php echo get_avatar( get_the_author_meta( 'ID' ), 32 ); ?>
													</a>
													<?php
														if ( ! independent_publisher_categorized_blog() ) {
															echo independent_publisher_entry_meta_author_prefix() . ' ';
														}
														independent_publisher_posted_author();
													?>
												</span>
												<?php if ( independent_publisher_categorized_blog() ) {
													echo independent_publisher_entry_meta_category_prefix() . ' ' . independent_publisher_post_categories( '', true );
												} ?>
												<span class="entry-title-meta-post-date">
													<span class="sep"> <?php echo apply_filters( 'independent_publisher_entry_meta_separator', '|' ); ?> </span>
													<?php independent_publisher_posted_on_date() ?>
												</span>
												<?php do_action( 'independent_publisher_entry_title_meta', $separator = ' | ' ); ?>
											</h3>
										<?php endif; ?>
									</header>
								</div>
							</div>
					<?php
					else:
						the_post_thumbnail( apply_filters( 'independent_publisher_full_width_featured_image_size', 'independent_publisher_post_thumbnail' ), array( 'class' => 'full-width-featured-image' ) );
					endif;
				endif;
			endwhile; // end of the loop.
		}
	}

