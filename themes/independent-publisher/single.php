<?php
/**
 * The Template for displaying all single posts.
 *
 * @package Independent Publisher
 * @since   Independent Publisher 1.0 
 
 */

/* we go through the loop once and reset it in order to get some vars for our og tags */
if ( have_posts() ) {
	the_post(); 


	$metaAuthor= get_the_author(); 
	$metaAuthorTwitter= get_the_author_meta( 'twitterHandle' ); 
	$ogTitle=get_the_title();

	$metaKeywords= strip_tags(get_the_tag_list('',', ',''));

	$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'Full' ); 
	$ogImage = $thumb['0'];

	$socialImageID = get_post_meta( $post->ID, 'social_image',true );
	if ($socialImageID) {
		$socialImage = wp_get_attachment_image_src( $socialImageID , 'Full');
		$ogImage = $socialImage[0];
	}
	
	$ogDescription=independent_publisher_first_sentence_excerpt(); //get_the_excerpt()

	rewind_posts();
}

$pageBodyID="postDetail";
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
<div id='promo'>
	<div class='promoOffer'>
		<div id='closeX'>X</div>
		<h3>Have smarter conversations</h3>
		<div class='clearAll'></div>
		<p>Want to always know what’s trending? Sign up for the daily Africa Rizing newsletter.</p>
		<form action="https://tinyletter.com/africarizing" method="post" target="tinyletterhider" class="tinyletter-form">
			<label></label>
			<input type="email" placeholder="Your email address" name="email" id="name" />
			<span class="tinyletter-confirmation">You’re almost done! Check your email to confirm subscription.</span>
			<input type="hidden" value="1" name="embed"/>
			<button type="submit">Subscribe</button>
		</form>
		<iframe class="tinyletterhider" name="tinyletterhider"></iframe>
	</div>
</div>
<script type="text/javascript">
	var showOffer = true;
	var documentHeight = jQuery(document).height();
	var windowHeight = jQuery(window).height();
	var promoScrollConstant = 250;
	var deltaBottom = windowHeight + promoScrollConstant //windowHeight

	jQuery(document).scroll(function() {
		var y = jQuery(this).scrollTop();
		if (Cookies.get('promoOfferClosed') == 'true') {
			showOffer=false;
		}
		if (documentHeight - y < deltaBottom && showOffer) {
			jQuery('.promoOffer').fadeIn();
		} else {
			jQuery('.promoOffer').fadeOut();
		}
		jQuery('#closeX').click(function(){
			showOffer = false;
			jQuery('.promoOffer').fadeOut();

			//3 day expiration if they click the 'X' without subscribing
			//but if the cookie is already set, that means they hit subscribe and then X, so stick with the original 365 and don't overwrite
			if (Cookies.get('promoOfferClosed') != 'true') {
				Cookies.set('promoOfferClosed', 'true', { expires: 3 });
			}
		})
	});

	jQuery(document).ready(function(){
		jQuery( '.tinyletter-form' ).submit(function() {
			//2 year expiration if they click the 'X'
			Cookies.set('promoOfferClosed', 'true', { expires: 730 });

			jQuery('.fieldtogglization').hide();
			jQuery('form input#name').hide();
			jQuery('form button').hide();
			jQuery('.tinyletter-confirmation').slideDown();
		});
	})
</script>


