<?php
/**
 * The template for displaying Author archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Independent Publisher
 * @since   Independent Publisher 1.0
 */

/* we go through the loop once and reset it in order to get some vars for our og tags */
if ( have_posts() ) {
	the_post(); 

	$ogTitle= get_the_author() . ": Africa Rizing Bio"; 

	/* wordpress's get_avatar function outputs a full image tag ... we just want the src */
	$fullImageHTML=get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'independent_publisher_author_bio_avatar_size', 100 ) );
	$xpath = new DOMXPath(@DOMDocument::loadHTML($fullImageHTML));
	$ogImage = $xpath->evaluate("string(//img/@src)");

	$ogDescription=get_the_author_meta( 'description' );

	rewind_posts();
}
$pageBodyID="authorPage";
get_header(); ?>

	<section id="primary" class="content-area">
		<div id="content" class="site-content" role="main">

			<?php if ( have_posts() ) : ?>

				<?php
				/* Queue the first post, that way we know
				 * what author we're dealing with (if that is the case).
				 *
				 * We reset this later so we can run the loop
				 * properly with a call to rewind_posts().
				 */
				the_post();
				?>

				<!--
				<header class="archive-header">
					<h1 class="archive-title"><?php printf( '%s', '<span class="vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '" title="' . esc_attr( get_the_author() ) . '" rel="me">' . get_the_author() . '</a></span>' ); ?></h1>
				</header> -->

				<?php
				/* Since we called the_post() above, we need to
				 * rewind the loop back to the beginning that way
				 * we can run the loop properly, in full.
				 */
				rewind_posts();
				?>

				<?php get_template_part( 'author-bio' ); ?>

				<?php /* The loop */ ?>
				<?php while ( have_posts() ) : the_post(); ?>
					<?php get_template_part( 'content', get_post_format() ); ?>
				<?php endwhile; ?>

				<?php independent_publisher_content_nav( 'nav-below' ); ?>

			<?php else : ?>
				<?php get_template_part( 'content', 'none' ); ?>
			<?php endif; ?>

		</div>
		<!-- #content -->
	</section><!-- #primary .content-area -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>