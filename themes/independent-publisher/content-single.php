<?php
/**
 * @package Independent Publisher
 * @since   Independent Publisher 1.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> itemscope="itemscope" itemtype="http://schema.org/BlogPosting" itemprop="blogPost">
	<?php if ( has_post_thumbnail() && ! independent_publisher_has_full_width_featured_image() ) : ?>
		<?php the_post_thumbnail( 'independent_publisher_post_thumbnail', array( 'itemprop' => 'image' ) ); ?>
	<?php endif; ?>
	<header class="entry-header">
	<?php if ( independent_publisher_post_has_post_cover_title() ): ?>
		<h2 class="entry-title-meta">
      <span class="entry-title-meta-author">
        <?php if ( ! independent_publisher_categorized_blog() ) {
			echo independent_publisher_entry_meta_author_prefix() . ' ';
		}
		independent_publisher_posted_author() ?></span>
		<?php if ( get_post_meta( get_the_ID(), 'independent_publisher_primary_category', true ) ) { // check for a custom field named 'independent_publisher_primary_category'
            echo independent_publisher_entry_meta_category_prefix() . ' ' . get_post_meta( get_the_ID(), 'independent_publisher_primary_category', true ); // show the primary category as set in ACF
      	} else if ( independent_publisher_categorized_blog() ) {
        	   echo independent_publisher_entry_meta_category_prefix() . ' ' . independent_publisher_post_categories( '', true );
      	} ?>
			<span class="entry-title-meta-post-date">
				<span class="sep"> <?php echo apply_filters( 'independent_publisher_entry_meta_separator', '|' ); ?> </span>
				<?php independent_publisher_posted_on_date() ?>
			</span>
			<?php do_action( 'independent_publisher_entry_title_meta', $separator = ' | ' ); ?>
		</h2>
	<?php else: ?>
		<h2 class="entry-title-meta">
			<span class="entry-title-meta-author">
				<?php if ( ! independent_publisher_categorized_blog() ) {
					echo independent_publisher_entry_meta_author_prefix() . ' ';
				}
				independent_publisher_posted_author() ?>
			</span>
			<?php if ( independent_publisher_categorized_blog() ) {
				echo independent_publisher_entry_meta_category_prefix() . ' ' . independent_publisher_post_categories( '', true );
			} ?>
			<span class="entry-title-meta-post-date">
				<span class="sep"> <?php echo apply_filters( 'independent_publisher_entry_meta_separator', '|' ); ?> </span>
				<?php independent_publisher_posted_on_date() ?>
			</span>
			<?php do_action( 'independent_publisher_entry_title_meta', $separator = ' | ' ); ?>
		</h2>
		<h1 class="entry-title" itemprop="name"><?php the_title(); ?></h1>
	<?php endif; ?>
	</header>
	<!-- .entry-header -->
	<div class="entry-content" itemprop="mainContentOfPage">
		<?php the_content(); ?>
		<?php wp_link_pages(
			array(
				'before'           => '<div class="page-links-next-prev">',
				'after'            => '</div>',
				'nextpagelink'     => '<button class="next-page-nav">' . __( 'Next page &rarr;', 'independent-publisher' ) . '</button>',
				'previouspagelink' => '<button class="previous-page-nav">' . __( '&larr; Previous page', 'independent-publisher' ) . '</button>',
				'next_or_number'   => 'next'
			)
		); ?>
		<?php wp_link_pages(
			array(
				'before' => '<div class="page-links">' . __( 'Pages:', 'independent-publisher' ),
				'after'  => '</div>'
			)
		); ?>

	</div>
	<!-- .entry-content -->



	<?php do_action( 'independent_publisher_before_post_bottom_tag_list' ); ?>

	<?php if ( get_the_tag_list() ) : ?>
		<?php $tag_list_title = apply_filters( 'independent_publisher_tag_list_title', __( 'Related Content by Tag', 'independent-publisher' ) ); ?>
		<?php $tag_list = (string)get_the_tag_list( '<ul class="taglist"><li class="taglist-title">' . $tag_list_title . '</li><li>', '</li><li>', '</li></ul>' ); ?>
		<div id="taglist">
			<?php echo $tag_list; ?>
		</div>
	<?php endif; ?>




	<?php independent_publisher_posted_author_bottom_card() ?>

	<footer class="entry-meta">


		<?php do_action( 'independent_publisher_entry_meta_top' ); ?>

		<?php if ( comments_open() && ! independent_publisher_hide_comments() ) : ?>
			<div id="share-comment-button">
				<button>
					<i class="share-comment-icon"></i><?php echo independent_publisher_comments_call_to_action_text() ?>
				</button>
			</div>
		<?php endif; ?>

		<?php edit_post_link( __( 'Edit', 'independent-publisher' ), '<span class="edit-link">', '</span>' ); ?>





		
		<div id="creativeCommons">
			<div id="container">
				<a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/4.0/">
					<img alt="Creative Commons License" style="border-width:0" src="https://i.creativecommons.org/l/by-nc-sa/4.0/80x15.png" />
				</a><br />
				This work by <a xmlns:cc="http://creativecommons.org/ns#" href="http://africa.rizing.org" property="cc:attributionName" rel="cc:attributionURL">Africa Rizing</a> is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/4.0/">Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License</a>.
			</div>
		</div>

	</footer>
	<!-- .entry-meta -->

</article><!-- #post-<?php the_ID(); ?> -->
