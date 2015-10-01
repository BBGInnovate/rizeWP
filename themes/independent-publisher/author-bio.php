<?php
/**
 * The template for displaying Author bios.
 *
 * @package Independent Publisher
 * @since   Independent Publisher 1.0
 */
?>
<div class="author-bio">
	<div class="author-avatar">
		<?php echo get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'independent_publisher_author_bio_avatar_size', 150 ) ); ?>
	</div>
	<!-- .author-avatar -->
	<div class="author-description">
		<p class="author-bio">
			<?php if ( get_the_author_meta( 'description' ) ) : ?>
				<?php the_author_meta( 'description' ); ?>
			<?php endif; ?>
			<?php 
				/* ODDI CUSTOM: add twitter handle to bio */
				$twitterHandle = get_the_author_meta( 'twitterHandle' );
				$website = get_the_author_meta( 'user_url' );

				if ( $website && $website != '' ) {
					$website=' | <a href="' . $website . '">' . $website . '</a>';
				}


				if ( $twitterHandle && $twitterHandle != '' ) {
					$twitterHandle=str_replace("@", "", $twitterHandle);
					echo '<div id="authorContact"><a href="//www.twitter.com/' . $twitterHandle. '">@' . $twitterHandle . '</a> ' . $website .'</div>';
				}
			?>
		</p>
	</div>
	<!-- .author-description -->
</div> <!-- .author-bio -->