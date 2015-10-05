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
<div id='footerNav'>
	<div class='maxWidth1200'>
		<ul>
			<li class='home'><a href='/'>home</a></li>
			<li class='focus'><a href='/index.php/in-focus/'>in focus</a></li>
			<li class='mission'><a href='/index.php/about/'>mission</a></li>
			<li class='categories'><a href='/index.php/categories/'>trending</a></li>
		</ul>
	</div>
</div>

</body>
</html>
