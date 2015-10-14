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

	//$my_custom_footer = '<div id="attribution">Project Rize is a new digital media startup developed by <a href="http://bbg.gov/" target="_blank">US International Media</a> in partnership with <a href="http://voanews.com/" target="_blank">VOA News</a> to promote open discourse, democratic ideals and sustainable, civil societies throughout the world.</div>'.$creativecommons;
	$my_custom_footer = '<div id="attribution">Africa Rizing is the first publication from Project Rize, a new initiative from the <a href="http://bbg.gov/" target="_blank">BBG</a> to promote open discourse, democratic ideals and sustainable, civil societies throughout the world.<br/><a href="https://africa.rizing.org/privacy">Privacy Policy</a> & <a href="https://africa.rizing.org/terms">Terms</a> </div>'.$creativecommons;
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
add_image_size( 'largest', 1200, 9999 ); // new size at our max breaking point
add_image_size( 'gigantic', 1900, 9999 ); // for some huge monitors
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
		if ( true ||  independent_publisher_has_full_width_featured_image() ) {
			while ( have_posts() ) : the_post();
				if ( true || has_post_thumbnail() ) :
					if ( true ||  independent_publisher_post_has_post_cover_title() ):
						
						//tevkori_get_srcset_array( $id, $size = 'thumbnail' ) {
						//global $wpdb;
						$postCoverTitleWrapperExtraClass="postCoverTitleAlwaysBelow";
						$showPostCover=false;
						echo "<style>\n";
						if (independent_publisher_has_full_width_featured_image()  && has_post_thumbnail()) {
							$showPostCover=true;

							$tempSources = tevkori_get_srcset_array( get_post_thumbnail_id(), 'full');
							//sources aren't automatically in numeric order.  ksort does the trick.
							ksort($tempSources);

							
							$counter=0;
							$prevWidth=0;

							// Let's prevent any images with width > 1200px from being an output as part of responsive post cover
							foreach( $tempSources as $key => $tempSource ) {
								if ($key > 1900) {
									unset($tempSources[$key]);
								}
							}

							foreach( $tempSources as $key => $tempSource ) {
								
								//we're working with an array of sourceset entries ... gotta get rid of the width part
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
							


							$featured_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), apply_filters( 'independent_publisher_full_width_featured_image_size', 'independent_publisher_post_thumbnail' ));
							$featured_image_url = $featured_image_url[0];
							$post_has_cover_title_rize 	= get_post_meta( get_the_ID(), 'post_cover_overlay_post_title_rize', true);
							if ( ( $post_has_cover_title_rize === '1' || $post_has_cover_title_rize === 'true' ) ) {
								$postCoverTitleWrapperExtraClass="";
							}
						}
						echo "</style>\n";

						/*** PREPARE TWITTER AND FB SHARE URLS ****/
						$shareLink=get_permalink();
						global $pageBodyID;
						if ($pageBodyID=="trending") {
							global $trendingPostPermalink;
							$shareLink=$trendingPostPermalink;
						}
						
						/* remove html tags, smart quotes and trailing ellipses from description */
						$ogDescription=independent_publisher_first_sentence_excerpt();
						$ogDescription=wp_strip_all_tags($ogDescription); 
						$ogDescription = iconv('UTF-8', 'ASCII//TRANSLIT', $ogDescription); //smart quotes
						$ogDescription = str_replace("[&hellip;]", "...", $ogDescription);  

						//the title/headline field, followed by the URL and the author's twitter handle
						$twitterText= "";
						$twitterText .= get_the_title();
						$twitterHandle = get_the_author_meta( 'twitterHandle' );
						$twitterHandle=str_replace("@", "", $twitterHandle);
						if ( $twitterHandle && $twitterHandle != '' ) {
							$twitterText .= " by @" . $twitterHandle; 
						} else {
							$authorDisplayName=get_the_author();
							if ($authorDisplayName && $authorDisplayName!='') {
								$twitterText .= " by " . $authorDisplayName;
							}
						}
						$twitterText .= " " . $shareLink;
						$hashtags="";
						//$hashtags="testhashtag1,testhashtag2";

						///$twitterURL="//twitter.com/intent/tweet?url=" . urlencode(get_permalink()) . "&text=" . urlencode($ogDescription) . "&hashtags=" . urlencode($hashtags);
						$twitterURL="//twitter.com/intent/tweet?text=" . urlencode($twitterText);
						$fbUrl="//www.facebook.com/sharer/sharer.php?u=" . urlencode($shareLink);

					?>
						<div class="post-cover-title-wrapper <?php echo $postCoverTitleWrapperExtraClass; ?>">
							<?php if ($showPostCover) : ?>
							<div class="post-cover-title-image postCoverResponsive" ></div>
							<?php endif; ?>
								<div class="post-cover-title-head">
									<header class="post-cover-title">
										<?php if ( independent_publisher_categorized_blog() ) { ?>
										
										<?php if (true || $pageBodyID != "trending") : ?>
											<h5 class='entry-category'>
												<?php echo independent_publisher_post_categories( '', true ); ?>
											</h5>
										<?php endif; ?>

										<div id='socialPost'>
											<ul>
												<li class='facebook'><a class="share" id="facebook" href="<?php echo $fbUrl; ?>"></a></li>
												<li class='twitter'><a class="share" id="twitter" href="<?php echo $twitterURL; ?>"></a></li>
											</ul>
										</div>

										<?php } ?>

										<?php if (true || $pageBodyID != "trending") : ?>
											<h1 class="entry-title" itemprop="name">
												<?php echo get_the_title(); ?>
											</h1>
										<?php endif; ?>
										
										<?php $subtitle = get_post_meta(get_the_id(), 'independent_publisher_post_cover_subtitle', true); ?>
										<?php if ( $subtitle ): ?>
											<h2 class="entry-subtitle">
												<?php echo $subtitle;?>
											</h2>
										<?php endif; ?>
										<?php if ( $pageBodyID == "trending" || (! is_page()) ) : ?>
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


	/***** enqueue two stylesheets for easier collaboration temporarily */
	function independent_publisher_stylesheet() {
		wp_enqueue_style( 'independent-publisher-style', get_stylesheet_uri() );
		wp_enqueue_style( 'independent-publisher-style-gigi', '/wp-content/themes/independent-publisher-child-theme/style_gigi.css', '', filemtime( get_stylesheet_directory() . '/style_gigi.css') );

	}

	/*===================================================================================
	 * Add Author Metadata - found @ http://www.paulund.co.uk/how-to-display-author-bio-with-wordpress
	 * =================================================================================*/
	function add_to_author_profile( $contactmethods ) {
		$contactmethods['twitterHandle'] = 'Twitter Handle';
		//$contactmethods['linkedin_profile'] = 'Linkedin Profile URL'; 
		return $contactmethods;
	}
	add_filter( 'user_contactmethods', 'add_to_author_profile', 10, 1);


	/*===================================================================================
	 * CUSTOM PAGINATION LOGIC - we show X posts on front page but more posts on 'older post' pages
	 * the next several functions are for adding that functionality and also making it available in wordpress settings
	 * =================================================================================*/
	
	add_action('pre_get_posts', 'myprefix_query_offset', 1 ); 
	function myprefix_query_offset(&$query) {	
		
		/* don't show in focus posts on homepage */
		if ($query -> is_home()) {
			$trending_cat_id=get_cat_id('Trending');
			$tax_query = array(
			    array(
			        'taxonomy' => 'category',
			        'field' => 'term_id',
			        'terms' => $trending_cat_id,
			        'operator' => 'NOT IN',
			    )
			);
			$query->set( 'tax_query', $tax_query );
		}
		

		if ( ! ($query->is_home() &&  $query->is_main_query()) ) {
		    return;
		}
		$ppp = get_option('posts_per_page');

		if ( is_paged() ) {
			$offset = -1 * ($ppp - get_option( 'homepage_post_count' ));
			$sticky_posts = get_option( 'sticky_posts' );
			$numStickies=0;
			if (is_array($sticky_posts)) {
				$numStickies=sizeof($sticky_posts);
				$offset=$offset-$numStickies;
			}
			//note ... if we make more posts sticky than can take up a whole page, we'd have an issue.  
			//not worrying about that edge case as it's not feasible.
			$page_offset = $offset + ( (get_query_var('paged')-1) * $ppp );

			$query->set('offset', $page_offset);

		} else {
			//we handle the custom logic for the first page in index.php - so nothing to do in this clause
		}
	}
	
	// add a custom field to the 'READING' section of wordpress with the homepage post count
	function homepage_post_count_callback( $args ) {
		$val = get_option( 'homepage_post_count' );
		if (! $val ) {
			$val = 0;
		}
		$html = '<input type="text" id="homepage_post_count" name="homepage_post_count" value="' . $val . '" size="3" />';
		$html .= '<label for="homepage_post_count"></label>';
		echo $html;
	}

	function oddi_settings_api_init() {
		add_settings_field(
			'homepage_post_count',
			'Home Page Post Count',
			'homepage_post_count_callback',
			'reading'
		);
		register_setting('reading','homepage_post_count');
	}
		 
	add_action( 'admin_init', 'oddi_settings_api_init' );



	// ODDI CUSTOM: customize the youtube emebeds to always be responsive
	//see http://tutorialshares.com/youtube-oembed-urls-remove-showinfo/
	function custom_youtube_settings($code){
		if(strpos($code, 'youtu.be') !== false || strpos($code, 'youtube.com') !== false){
			//$return = preg_replace("@src=(['\"])?([^'\">\s]*)@", "src=$1$2&showinfo=0&rel=0&autohide=1", $code);
			
			//remove the width/height attributes
			$return = preg_replace(
				array('/width="\d+"/i', '/height="\d+"/i'),
   				array('',''),
   			$code);

			//wrap in a responsive div
			$return="<div class='embed-container'>" . $return . "</div>";
		} else {
			$return = $code;
		}
		return $return;
	}

	add_filter('embed_handler_html', 'custom_youtube_settings');
	add_filter('embed_oembed_html', 'custom_youtube_settings');

	/**** ODDI CUSTOM: restrict jpg quality ****/
	// see http://premium.wpmudev.org/blog/fix-jpeg-compression/
	add_filter( 'jpeg_quality', create_function( '', 'return 75;' ) );



	/***** ODDI CUSTOM - HOOK INTO CATEGORY MANAGEMENT FORM TO ALLOW USERS TO SPECIFY THAT A CATEGORY IS FEATURED */
	//taken from http://php.quicoto.com/add-metadata-categories-wordpress/

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
	

	// Add column to Category list
	function xg_featured_category_columns($columns) {
	    return array_merge($columns, 
	              array('featured' =>  __('Featured')));
	}
	
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

	/* if we ever decide to include featured categories again, just set this to true */
	$showFeaturedCategories=false;
	if ($showFeaturedCategories){
		add_action( 'category_edit_form_fields', 'xg_edit_featured_category_field' );
		add_action( 'edited_category', 'xg_save_tax_meta', 10, 2 ); 
		add_filter('manage_edit-category_columns' , 'xg_featured_category_columns');
		add_action( 'manage_category_custom_column' , 'xg_featured_category_columns_values', 10, 3 );
	}
	
	