<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Independent Publisher
 * @since   Independent Publisher 1.0
 */


global $wp_query;
$pageBodyID="frontPage";
get_header(); 

?>

	<div id="primary" class="content-area">
		<main id="content" class="site-content" role="main">
			<?php if ( have_posts() ) : ?>
				<?php /* Start the Loop */ ?>


				<?php while ( have_posts() ) : the_post(); 
					/* CUSTOM BEHAVIOR -  only show first two posts on homepage */
					if ( ! is_paged() && $wp_query->current_post > (get_option( 'homepage_post_count' )-1)) {
						continue;
					}
					get_template_part( 'content', get_post_format() );
				endwhile; ?>

				<?php independent_publisher_content_nav( 'nav-below' ); ?>

			<?php else : ?>

				<?php get_template_part( 'content', 'none' ); ?>

			<?php endif; ?>

		</main>
		<!-- #content .site-content -->
	</div><!-- #primary .content-area -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
