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

<style type="text/css">




</style>

	<div id="primary" class="content-area">
		<main id="content" class="site-content pagePostByCategory" role="main">

			<?php
				// get all the categories from the database
				$cats = get_categories();
				
				//only use featured categories
				$featuredCats=[];
				foreach ($cats as $cat) {
					$cat_id = $cat->term_id;
					$term = get_option( "taxonomy_" . $cat_id );
					if ( $term['featured'] == "1" ) {
						$featuredCats[] = $cat;
					}
				}

				//limit the number of categories to 6
				array_splice($featuredCats,6);

				// loop through the categries
				$currentCategoryNum = 0;
				foreach ($featuredCats as $cat) {
					$currentCategoryNum = $currentCategoryNum+1;
					$cat_id = $cat->term_id;
					$term = get_option( "taxonomy_" . $cat_id );

					echo "<div class='categoryContainer'>";
					echo "<h4 class='category'><a href=" . get_category_link($cat_id) . ">".$cat->name."</a></h2>";
					
					query_posts("cat=$cat_id&posts_per_page=3&orderby=post_date&order=desc");
					
					global $wp_query; 
					$totalPostsInCategory = $wp_query->found_posts;


					// start the wordpress loop!
					$postNumInCategory=0;
					if (have_posts()) : while (have_posts()) : the_post(); $postNumInCategory=$postNumInCategory+1; 
							if ($postNumInCategory==1) : 
							
							?>
									
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
												$useFullThumbnail=($currentCategoryNum==1);
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
													/*echo "<a href='".$the_permalink."'><div class='listThumbnail' style='background-image: url(".$url.");'></div></a>";*/ 
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
									</article><!-- #post-<?php the_ID(); ?> -->
							<?php 
								if ($totalPostsInCategory > 1) {
									echo '<ul class="fullWidth">';
								}
							?>

							<?php else: ?>
									<li><a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'independent-publisher' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a></li>
							<?php endif; ?>
							

					<?php endwhile; endif; // done our wordpress loop. Will start again for each category ?>
					<?php 
						if ($totalPostsInCategory > 1) {
							echo '</ul> <!-- .fullWidth -->';
						}
					?>
					</div> <!-- .categoryContainer -->


				<?php } // done the foreach statement ?>

		</main>
		<!-- #content .site-content -->
	</div><!-- #primary .content-area -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>