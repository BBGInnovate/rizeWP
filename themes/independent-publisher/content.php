<?php
	/**
	 * @package Independent Publisher
	 * @since   Independent Publisher 1.0
	 */

	global $pageBodyID; 
	global $featuredInFocusPostID;
	global $stickyDisplayed;

	/* ODDI CUSTOM: Show large image on first instance in loop */
	$useFullThumbnail = false;
	$useSmallThumbnail = false;
	$displayExcerpt=true;
	if ( empty ($stickyDisplayed)) {
		$stickyDisplayed=false;
	}
	
	if ( $pageBodyID == 'inFocus') {
		/*** IN FOCUS BEHAVIOR DEFINED HERE ****/

		//show full thumbnail for sticky in-focus post
		if ($featuredInFocusPostID == $post->ID) {
			$useFullThumbnail=true;
		}

		//hide excerpts for all non-sticky in-focus posts
		if ($featuredInFocusPostID != $post->ID) {
			$displayExcerpt=false;
		}

	} else {
		/**** MOST PAGES FOLLOW THIS BEHAVIOR ****/
		if (!is_paged()) {

			//first page behavior different than all that follow
			if (is_sticky() || ( ($wp_query->current_post == 0) && ! $stickyDisplayed)) {
				//show full thumbnail for first post
				$useFullThumbnail=true;
				$stickyDisplayed=true;
			} else {
				//show small thimbnail for the rest
				$useFullThumbnail=false;
				$useSmallThumbnail=true;
			}
		} else {
			//'OLDER POST' page behavior.  No thumbnails, no excerpts
			$displayExcerpt=false;
		}
	}

	/* SAFETY CHECK IN CASE THEY FORGET THUMBNAIL */
	if ( ! has_post_thumbnail() ) {
		$useFullThumbnail=false;
		$useSmallThumbnail=false;
	}
	
	$customClass="";
	if ($pageBodyID=='inFocus' && ($featuredInFocusPostID == $post->ID)) {
		$customClass="inFocusSticky";
	}
?>
<article id="post-<?php the_ID(); ?>" <?php independent_publisher_post_classes($customClass); ?>>
	<header class="entry-header">
		
		<?php
			if ( $useFullThumbnail) : 
		?>
				<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'independent-publisher' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark">
				<?php the_post_thumbnail(); ?>
				</a>
		<?php 
			endif; 
			if ( $pageBodyID != "categoryPage" && strlen(independent_publisher_post_categories())>0) :
		?>
				<h5 class='entry-category'>
					<?php echo independent_publisher_post_categories( '', true ); ?>
				</h5>
		<?php 
			endif;
		?>


		<h1 class="entry-title">
			<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'independent-publisher' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
		</h1>
	</header>
	<!-- .entry-header -->

	<div class="entry-content">

		<?php 
			
			if( $useSmallThumbnail) { 
				?>

				<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'independent-publisher' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark">


				<?php
				/* ODDI: Add thumbnail for each post in loop */
				$thumb = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'thumbnail' ); 
				$url = $thumb['0']; 

				$img_id = get_post_thumbnail_id($post->ID); // This gets just the ID of the img
				$alt_text = get_post_meta($img_id , '_wp_attachment_image_alt', true);

				/*echo "<img src='$url' alt='$alt_text' class='listThumbnail'/>"; */
				echo "<div class='listThumbnail' style='background-image: url(".$url.");'></div>"; 
				?>

				</a>

				<?php
			}
		?>


		<?php 
		/* Only show excerpts for Standard post format OR Chat format,
		 * when this is not both the very first standard post and also a Sticky post AND
		 * when excerpts enabled or One-Sentence Excerpts enabled AND
		 * this is not the very first standard post when Show Full Content First Post enabled 
		 */
		?>

		<?php if ( $displayExcerpt ) : ?>	
			<?php if ( ( ! get_post_format() || 'chat' === get_post_format() ) &&
					   ( ! ( independent_publisher_is_very_first_standard_post() && is_sticky() ) ) &&
					   ( independent_publisher_use_post_excerpts() || independent_publisher_generate_one_sentence_excerpts() ) &&
					   ( ! ( independent_publisher_show_full_content_first_post() && independent_publisher_is_very_first_standard_post() && is_home() ) )
			) :
				?>
				<?php the_excerpt(); ?>
			<?php endif; ?>
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
	<?php /*if ( false === get_post_format() && independent_publisher_generate_one_sentence_excerpts() && independent_publisher_is_not_first_post_full_content() && ! is_sticky() ) : ?>
		<?php independent_publisher_continue_reading_link(); ?>
	<?php endif; */ ?>

	<?php if ( true || $displayExcerpt ):  ?>
		<footer class="entry-meta">

			<?php 
			/* Show author name and post categories only when post type == post AND 
			 * we're not showing the first post full content 
			 */ 
			?>
			<?php if ( 'post' == get_post_type() && independent_publisher_is_not_first_post_full_content() ) : // post type == post conditional hides category text for Pages on Search ?>
				<?php /*independent_publisher_posted_author_cats()*/ ?>
				<?php independent_publisher_posted_author() ?>
			<?php endif; ?>
			<span class="sep sep-byline"> | </span>
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
	<?php endif; ?>
</article><!-- #post-<?php the_ID(); ?> -->
