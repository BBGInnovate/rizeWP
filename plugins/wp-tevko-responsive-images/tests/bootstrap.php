<?php
$_tests_dir = false;

// ../tests ../{plugin} ../plugins
$dir = dirname( __FILE__ );

if ( 'plugins' === basename( dirname( dirname( $dir ) ) ) ) {
	$plugins = dirname( dirname( $dir ) );
	// wordpress core svn = ../wp-content ../src
	$step = dirname( dirname( $plugins ) );
	$tests = '/tests/phpunit';
	if ( 'src' === basename( $step ) ) {
		$_tests_dir = dirname( $step ) . $tests;
	// wordpress git = ../wp-content ../{root}
	} elseif ( is_dir( $step . '/tests' ) ) {
		$_tests_dir = $step . $tests;
	}
}

if ( ! $_tests_dir ) {
	$_tests_dir = getenv( 'WP_TESTS_DIR' );
	if ( ! $_tests_dir ) {
		$_tests_dir = '/tmp/wordpress-tests-lib';
	}
}

require_once( $_tests_dir . '/includes/functions.php' );

function _manually_load_plugin() {
	require dirname( __FILE__ ) . '/../wp-tevko-responsive-images.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require( $_tests_dir . '/includes/bootstrap.php' );
