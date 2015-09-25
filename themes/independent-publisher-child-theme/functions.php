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
	/*$creativecommons = '<div id="creativecommons"><a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/4.0/"><img alt="Creative Commons License" style="border-width:0" src="https://i.creativecommons.org/l/by-nc-sa/4.0/80x15.png" /></a><br />This work by <a xmlns:cc="http://creativecommons.org/ns#" href="http://africa.rizing.org" property="cc:attributionName" rel="cc:attributionURL">Africa Rizing</a> is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/4.0/">Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License</a>.</div>';*/
	$creativecommons = '';

	$my_custom_footer = '<div id="attribution">Project Rize is a new digital media startup developed by <a href="http://bbg.gov/" target="_blank">US International Media</a> in partnership with <a href="http://voanews.com/" target="_blank">VOA News</a> to promote open discourse, democratic ideals and sustainable, civil societies throughout the world.</div>'.$creativecommons;
	return $my_custom_footer;
}

function array_swap($key1, $key2, $array) {
	$newArray = array ();
	foreach ($array as $key => $value) {
		if ($key == $key1) {
			$newArray[$key2] = $array[$key2];
		} elseif ($key == $key2) {
			$newArray[$key1] = $array[$key1];
		} else {
			$newArray[$key] = $value;
		}
	}
	return $newArray;
}

add_image_size( 'mugshot', 200, 200 ); // 220 pixels wide by 180 pixels tall, soft proportional crop mode
add_filter( 'image_size_names_choose', 'my_custom_sizes' );

function my_custom_sizes( $sizes ) {
	/*  NOTE: the $sizes array here is simply an associative array.  It doesn't provide actual dimensions.
		We are hardcoding that Mugshot goes second now (and thumbnail first) ... a more robust solution
		could leverage something like https://codex.wordpress.org/Function_Reference/get_intermediate_image_sizes 
	*/
	$newArray=array( 'mugshot' =>'Mugshot');
	foreach ($sizes as $key => $value) {
		$newArray[$key]=$value;
	}
	$reorderedSizes=array_swap("mugshot","thumbnail",$newArray);
	return $reorderedSizes;
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

										<div id='socialPost'>
											<ul>
												<li><a href='https://facebook.com/africarizing'></a></li>
												<li><a href='https://twitter.com/africarizing'></a></li>
											</ul>
										</div>

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
												<?php /* if ( independent_publisher_categorized_blog() ) {
													echo independent_publisher_entry_meta_category_prefix() . ' ' . independent_publisher_post_categories( '', true );
												} */?>
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


	/***** ODDI CUSTOM - HOOK INTO CATEGORY MANAGEMENT FORM TO ALLOW USERS TO SPECIFY THAT A CATEGORY IS FEATURED */
	//http://php.quicoto.com/add-metadata-categories-wordpress/
	function xg_edit_featured_category_field( $term ){
		//http://php.quicoto.com/add-metadata-categories-wordpress/
		$term_id = $term->term_id;
		$term_meta = get_option( "taxonomy_$term_id" );         
	?>
		<tr class="form-field">
			<th scope="row">
				<label for="term_meta[featured]"><?php echo _e('Featured') ?></label>
				<td>
					<select name="term_meta[featured]" id="term_meta[featured]">
						<option value="0" <?=($term_meta['featured'] == 0) ? 'selected': ''?>><?php echo _e('No'); ?></option>
						<option value="1" <?=($term_meta['featured'] == 1) ? 'selected': ''?>><?php echo _e('Yes'); ?></option>
					</select>                   
				</td>
			</th>
		</tr>
	<?php
	} //end xg_edit_featured_category_field
	add_action( 'category_edit_form_fields', 'xg_edit_featured_category_field' );

	// Save the field
	function xg_save_tax_meta( $term_id ){ 
	    if ( isset( $_POST['term_meta'] ) ) {
			$term_meta = array();
			// Be careful with the intval here. If it's text you could use sanitize_text_field()
			$term_meta['featured'] = isset ( $_POST['term_meta']['featured'] ) ? intval( $_POST['term_meta']['featured'] ) : '';
			// Save the option array.
			update_option( "taxonomy_$term_id", $term_meta );
		} 
	} // save_tax_meta
	add_action( 'edited_category', 'xg_save_tax_meta', 10, 2 ); 

	// Add column to Category list
	function xg_featured_category_columns($columns)
	{
	    return array_merge($columns, 
	              array('featured' =>  __('Featured')));
	}
	add_filter('manage_edit-category_columns' , 'xg_featured_category_columns');

	// Add the value to the column
	function xg_featured_category_columns_values( $deprecated, $column_name, $term_id) {
		if($column_name === 'featured'){ 
			$term_meta = get_option( "taxonomy_$term_id" );
			if($term_meta['featured'] === 1){
				echo _e('Yes');
			}else{
				echo _e('No');
			}	
		}
	}
	add_action( 'manage_category_custom_column' , 'xg_featured_category_columns_values', 10, 3 );
