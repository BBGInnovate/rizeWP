<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package Independent Publisher
 * @since   Independent Publisher 1.0
 */


/* ODDI CUSTOM: several variables can be passed into the header */ 
global $ogImage, $ogTitle, $ogDescription;
global $pageBodyID, $metaAuthor, $metaKeywords;

if (! isset( $pageBodyID ) ) {
	$pageBodyID="defaultPageBody";
}

if (! isset( $ogImage ) ) {
	$ogImage=DEFAULT_IMAGE;
}

if (! isset( $ogTitle ) ) {
	$ogTitle=DEFAULT_TITLE;
}

if (! isset( $ogDescription ) ) {
	$ogDescription=DEFAULT_DESCRIPTION;
}

if (! isset( $metaAuthor ) ) {
	$metaAuthor=DEFAULT_AUTHOR;
}

if (! isset( $metaKeywords ) ) {
	$metaKeywords=DEFAULT_KEYWORDS;
}

$ogUrl = get_permalink();

/* remove smart quotes from title */
$ogTitle = iconv('UTF-8', 'ASCII//TRANSLIT', $ogTitle);  

/* remove html tags, smart quotes and trailing ellipses from description */
$ogDescription = wp_strip_all_tags($ogDescription); 
$ogDescription = iconv('UTF-8', 'ASCII//TRANSLIT', $ogDescription); 
$ogDescription = str_replace("[&hellip;]", "...", $ogDescription); 
$ogDescription = str_replace('"','&qout;')


?><!DOCTYPE html>
<html <?php independent_publisher_html_tag_schema(); ?> <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	<title><?php wp_title( '-', true, 'right' ); ?></title>
	<link rel="profile" href="http://gmpg.org/xfn/11" />
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
	<!--[if lt IE 9]>
	<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
	<![endif]-->

	<meta name="apple-mobile-web-app-title" content="AfricaRizing" />
	<link rel="apple-touch-icon-precomposed" sizes="120x120" href="/wp-content/uploads/2015/10/retina_icon.png" />

	<!-- for Google -->
	<meta name="description" content="<?php echo $ogDescription; ?>"/>
	<meta name="keywords" content="<?php echo $metaKeywords; ?>" />
	<meta name="author" content="<?php echo $metaAuthor; ?>" />

	<!-- for Facebook -->
	<meta property="og:locale" content="en_US">
	<meta property="og:type" content="website" />
	<meta property="og:title" content="<?php echo $ogTitle; ?>" />
	<meta property="og:description" content="<?php echo $ogDescription; ?>" />
	<meta property="og:image" content="<?php echo $ogImage; ?>" />
	<meta property="og:url" content="<?php echo $ogUrl; ?>" />

	<!-- for Twitter -->
	<meta property="twitter:card" content="summary">
	<meta property="twitter:title" content="<?php echo $ogTitle; ?>">
	<meta property="twitter:description" content="<?php echo $ogDescription; ?>">
	<meta property="twitter:image" content="<?php echo $ogImage; ?>">
	<meta property="twitter:url" content="<?php echo $ogUrl; ?>">

	<!-- other og:tags -->
	<meta property="og:site_name" content="<?php echo get_bloginfo('name'); ?>" />

	<?php 
		wp_head();
	?>
</head>

<body id="<?php echo $pageBodyID; ?>" <?php body_class(); ?> itemscope="itemscope" itemtype="http://schema.org/WebPage">

<?php // Displays full-width featured image on Single Posts if applicable ?>
<?php independent_publisher_full_width_featured_image(); ?>

<?php // Makes the Header Image a small icon floating in the top left corner when Multi Author Mode is enabled ?>
<?php if ( independent_publisher_is_multi_author_mode() && is_single() ) : ?>
	<div class="site-master-logo">
		<?php if ( get_header_image() ) : ?>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" rel="home">
				<img class="no-grav" src="<?php echo esc_url( get_header_image() ); ?>" height="<?php echo absint( get_custom_header()->height ); ?>" width="<?php echo absint( get_custom_header()->width ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" />
			</a>
		<?php endif; ?>
	</div>
<?php endif; ?>

<div id="page" class="hfeed site">
	<header id="masthead" class="site-header" role="banner" itemscope itemtype="http://schema.org/WPHeader">

		<div class="site-header-info">
			<?php if ( is_single() ) : ?>
				<?php // Show only post author info on Single Pages ?>
				<?php independent_publisher_posted_author_card(); ?>
			<?php else : ?>
				<?php // Show Header Image, Site Title, and Site Tagline on everything except Single Pages ?>
				<?php independent_publisher_site_info(); ?>
			<?php endif; ?>
		</div>






		<?php // Show navigation menu on everything except Single pages, unless Show Primary Nav Menu on Single Pages is enabled ?>
		<?php if ( ! is_single() || independent_publisher_show_nav_on_single() ) : ?>
			<nav role="navigation" class="site-navigation main-navigation">
				<a class="screen-reader-text skip-link" href="#content" title="<?php esc_attr_e( 'Skip to content', 'independent-publisher' ); ?>"><?php _e( 'Skip to content', 'independent-publisher' ); ?></a>

				<?php // If this is a Single Post and we have a menu assigned to the "Single Posts Menu", show that ?>
				<?php if ( is_single() && has_nav_menu( 'single' ) ) : ?>
					<?php wp_nav_menu( array( 'theme_location' => 'single', 'depth' => 1 ) ); ?>
				<?php else : ?>
					<?php wp_nav_menu( array( 'theme_location' => 'primary', 'depth' => 1 ) ); ?>
				<?php endif; ?>

			</nav><!-- .site-navigation .main-navigation -->
		<?php endif; ?>

		<?php do_action( 'independent_publisher_header_after' ); ?>
	</header>
	<!-- #masthead .site-header -->


	<div id="main" class="site-main">