<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the id=main div and all content after
 *
 * @package Independent Publisher
 * @since   Independent Publisher 1.0
 */
?>

</div><!-- #main .site-main -->

<footer id="colophon" class="site-footer" itemscope="itemscope" itemtype="http://schema.org/WPFooter" role="contentinfo">
	<div class="site-info">
		<?php echo independent_publisher_footer_credits(); ?>
	</div>
	<!-- .site-info -->
</footer><!-- #colophon .site-footer -->


</div><!-- #page .hfeed .site -->

<?php wp_footer(); ?>
<?php 
	
	/* Since the in-focus and mission pages are already part of the footer nav, we exclude them.
	   Simply add more entries to the list as needed */
	
	$excludeList=["/trending","/about","/guide", "/compass"];
    $menu_name = 'primary';
    if ( ( $locations = get_nav_menu_locations() ) && isset( $locations[ $menu_name ] ) ) {
		$menu = wp_get_nav_menu_object( $locations[ $menu_name ] );
		$menu_items = wp_get_nav_menu_items($menu->term_id);
		$menu_list = '<div id="focusMenu"><ul id="dropdown">';
		foreach ( (array) $menu_items as $key => $menu_item ) {
		    $title = $menu_item->title;
		    $url = $menu_item->url;
		    $excludeThisLink=false;
		    foreach ($excludeList as $urlToExclude) {
		    	if (strpos($url, $urlToExclude)) {
		    		$excludeThisLink=true;
		    	}
		    }
		    if ( ! $excludeThisLink) {
		    	$menu_list .= '<a href="' . $url . '"><li>' . $title . '</li></a>';	
		    }
		}
		$menu_list .= '</ul></div>';
    } else {
		//$menu_list = '<ul><li>Menu "' . $menu_name . '" not defined.</li></ul>';
    }
    echo $menu_list;
?>
<div id='footerNav'>
	<div class='maxWidth1200'>
		<ul>
			<li class='home'><a href='<?php echo site_url() . "/"; ?>'><span class='text'>home</span></a></li>
			<li class='trending'><a href='<?php echo site_url() . "/"; ?>index.php/trending/'><span class='text'>trending</span></a></li>
			<li class='newsletter'><a href='<?php echo site_url() . "/"; ?>index.php/guide/'><span class='text'>guide</span></a></li>
			<li class='focus'><a><span class='text'>in focus</span></a></li>
			<!--<li class='mission'><a href='<?php echo site_url() . "/"; ?>index.php/about/'><span class='text'>mission</span></a></li>-->
		</ul>
	</div>
</div>

</body>
</html>
