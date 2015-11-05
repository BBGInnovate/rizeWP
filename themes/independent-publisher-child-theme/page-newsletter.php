<?php
/**
 * The Template for displaying all single posts.
 *
 * @package Independent Publisher
 * @since   Independent Publisher 1.0 
  template name: Newsletter

 */

$pageBodyID = "newsletter";
$trendingCatID=get_cat_id('Guide');

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

/* we go through the loop once and reset it in order to get some vars for our og tags */
if ( have_posts() ) {
	the_post(); 


	$metaAuthor= get_the_author(); 
	$ogTitle=get_the_title();

	$metaKeywords= strip_tags(get_the_tag_list('',', ',''));

	//$ogImage=get_the_post_thumbnail($post->ID, 'thumbnail');
	$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'Full' ); 
	$ogImage = $thumb['0']; 
	$ogDescription=independent_publisher_first_sentence_excerpt(); //get_the_excerpt()

	$trendingPostPermalink=post_permalink(get_the_id());
	$ogUrl=$trendingPostPermalink;

	rewind_posts();
}

/**** somehow classes were being to the body that we don't want ... specifically archive and category ***/
/* see http://wordpress.stackexchange.com/questions/15850/remove-classes-from-body-class */
function wpse15850_body_class( $wp_classes, $extra_classes ) {
	
	$blacklist = array( 'archive', 'category', 'category-trending' );
	$wp_classes = array_diff( $wp_classes, $blacklist );

	// Add the extra classes back untouched
	return array_merge( $wp_classes, (array) $extra_classes );
}
add_filter( 'body_class', 'wpse15850_body_class', 10, 2 );


get_header(); ?>

	<div id="primary" class="content-area">
		<main id="content" class="site-content" role="main">

			<?php while ( have_posts() ) : the_post(); ?>

				<?php get_template_part( 'content', 'single' ); ?>

				<?php
				// If comments are open or we have at least one comment, load up the comment template
				if ( comments_open() || '0' != get_comments_number() && ! independent_publisher_hide_comments() ) {
					comments_template( '', true );
				}
				?>
<!--
			-->

			<?php endwhile; // end of the loop. ?>

		</main>
		<!-- #content .site-content -->
	</div><!-- #primary .content-area -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>