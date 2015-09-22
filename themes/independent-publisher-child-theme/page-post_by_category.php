<?php

/**
 template name: One post per Featured Category
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package Independent Publisher
 * @since   Independent Publisher 1.0
 */

get_header(); ?>

	<div id="primary" class="content-area">
		<main id="content" class="site-content" role="main">

			<?php
			// get all the categories from the database
			$cats = get_categories(); 

				// loop through the categries
				foreach ($cats as $cat) {
					// setup the cateogory ID
					$cat_id= $cat->term_id;
					// Make a header for the cateogry
					echo "<h2>".$cat->name."</h2>";
					// create a custom wordpress query
					query_posts("cat=$cat_id&posts_per_page=100");
					// start the wordpress loop!
					if (have_posts()) : while (have_posts()) : the_post(); ?>

									<article id="post-<?php the_ID(); ?>" <?php independent_publisher_post_classes(); ?>>
										<header class="entry-header">
											<?php 
											/* Show entry title meta only when 
											 * Show Full Content First Post enabled AND 
											 * this is the very first standard post AND 
											 * we're on the home page AND this is not a sticky post 
											 */ 
											?>
											
											<?php
												/* ODDI: Show large image on first instance in loop */
												$useFullThumbnail=false;

												if( $wp_query->current_post == 0 && !is_paged() ) { 
													$useFullThumbnail=false;
												}

												if ( $useFullThumbnail && has_post_thumbnail() ) {
													the_post_thumbnail();
												}
												
											?>
											
											<?php if ( independent_publisher_show_full_content_first_post() && ( independent_publisher_is_very_first_standard_post() && is_home() && ! is_sticky() ) ) : ?>
												<h2 class="entry-title-meta">
													<span class="entry-title-meta-author"><?php independent_publisher_posted_author() ?></span> <?php echo independent_publisher_entry_meta_category_prefix() ?> <?php echo independent_publisher_post_categories( '', true ); ?>
													<span class="entry-title-meta-post-date">
														<span class="sep"> <?php echo apply_filters( 'independent_publisher_entry_meta_separator', '|' ); ?> </span>
														<?php independent_publisher_posted_on_date() ?>
													</span>
													<?php do_action( 'independent_publisher_entry_title_meta', $separator = ' | ' ); ?>
												</h2>
											<?php endif; ?>
											<h1 class="entry-title">
												<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'independent-publisher' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
											</h1>
										</header>
										<!-- .entry-header -->

										<div class="entry-content">


											<?php 
												
												if( !$useFullThumbnail && has_post_thumbnail() ) { 

													/* ODDI: Add thumbnail for each post in loop */
													$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'thumbnail' ); 
													$url = $thumb['0']; 

													$img_id = get_post_thumbnail_id($post->ID); // This gets just the ID of the img
													$alt_text = get_post_meta($img_id , '_wp_attachment_image_alt', true);

													/*echo "<img src='$url' alt='$alt_text' class='listThumbnail'/>"; */
													echo "<div class='listThumbnail' style='background-image: url(".$url.");'></div>"; 
												}
											?>


											<?php 
											/* Only show excerpts for Standard post format OR Chat format,
											 * when this is not both the very first standard post and also a Sticky post AND
											 * when excerpts enabled or One-Sentence Excerpts enabled AND
											 * this is not the very first standard post when Show Full Content First Post enabled 
											 */
											?>
											<?php if ( ( ! get_post_format() || 'chat' === get_post_format() ) &&
													   ( ! ( independent_publisher_is_very_first_standard_post() && is_sticky() ) ) &&
													   ( independent_publisher_use_post_excerpts() || independent_publisher_generate_one_sentence_excerpts() ) &&
													   ( ! ( independent_publisher_show_full_content_first_post() && independent_publisher_is_very_first_standard_post() && is_home() ) )
											) :
												?>

												<?php the_excerpt(); ?>

											<?php
											else : ?>

												<?php /* Only show featured image for Standard post and gallery post formats */ ?>
												<?php if ( has_post_thumbnail() && in_array( get_post_format(), array( 'gallery', false ) ) ) : ?>
													<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'independent_publisher' ), the_title_attribute( 'echo=0' ) ) ); ?>"><?php the_post_thumbnail( 'independent_publisher_post_thumbnail' ); ?></a>
												<?php endif; ?>

												<?php the_content( independent_publisher_continue_reading_text() ); ?>
												<?php wp_link_pages(
													array(
														'before' => '<div class="page-links">' . __( 'Pages:', 'independent-publisher' ),
														'after'  => '</div>'
													)
												); ?>

											<?php endif; ?>
										</div>
										<!-- .entry-content -->

										<?php 
										/* Show Continue Reading link when this is a Standard post format AND 
										 * One-Sentence Excerpts options is enabled AND
									 	 * we're not showing the first post full content AND 
									 	 * this is not a sticky post 
									 	 */
										?>
										<?php if ( false === get_post_format() && independent_publisher_generate_one_sentence_excerpts() && independent_publisher_is_not_first_post_full_content() && ! is_sticky() ) : ?>
											<?php independent_publisher_continue_reading_link(); ?>
										<?php endif; ?>

										<footer class="entry-meta">

											<?php 
											/* Show author name and post categories only when post type == post AND 
											 * we're not showing the first post full content 
											 */ 
											?>
											<?php if ( 'post' == get_post_type() && independent_publisher_is_not_first_post_full_content() ) : // post type == post conditional hides category text for Pages on Search ?>
												<?php independent_publisher_posted_author_cats() ?>
											<?php endif; ?>

											<?php /* Show post date when show post date option enabled */
											?>
											<?php if ( independent_publisher_show_date_entry_meta() ) : ?>
												<?php echo independent_publisher_get_post_date() ?>
											<?php endif; ?>

											<?php 
											/* Show post word count when post is not password-protected AND 
											 * this is a Standard post format AND
											 * post word count option enabled AND 
											 * we're not showing the first post full content
											 */
											?>
											<?php if ( ! post_password_required() && false === get_post_format() && independent_publisher_show_post_word_count() && independent_publisher_is_not_first_post_full_content() ) : ?>
												<?php echo independent_publisher_get_post_word_count() ?>
											<?php endif; ?>

											<?php /* Show comments link only when post is not password-protected AND comments are enabled on this post */ ?>
											<?php if ( ! post_password_required() && comments_open() && ! independent_publisher_hide_comments() ) : ?>
												<span class="comments-link"><?php comments_popup_link( __( 'Comment', 'independent-publisher' ), __( '1 Comment', 'independent-publisher' ), __( '% Comments', 'independent-publisher' ) ); ?></span>
											<?php endif; ?>

											<?php $separator = apply_filters( 'independent_publisher_entry_meta_separator', '|' ); ?>
											<?php edit_post_link( __( 'Edit', 'independent-publisher' ), '<span class="sep"> ' . $separator . ' </span> <span class="edit-link">', '</span>' ); ?>

										</footer>
										<!-- .entry-meta -->
									</article><!-- #post-<?php the_ID(); ?> -->





					<?php endwhile; endif; // done our wordpress loop. Will start again for each category ?>


				<?php } // done the foreach statement ?>

		</main>
		<!-- #content .site-content -->
	</div><!-- #primary .content-area -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>