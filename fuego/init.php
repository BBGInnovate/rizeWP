<?php namespace OpenFuego;
/**
  * Do not run this file directly.
  * Edit config.php to set up the application.
  * Then run fetch.php at the command line.
  *
  * This file must be included in scripts.
  *
  * Gracias.
**/

if (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < 50300) {
	die(__NAMESPACE__ . ' requires PHP 5.3.0 or higher.');
}

define('OPENFUEGO', TRUE);

require(__DIR__ . '/config.php');

if (isset($argv) && in_array('-v', $argv)) {
	define(__NAMESPACE__ . '\VERBOSE', TRUE);
}

else {
	define(__NAMESPACE__ . '\VERBOSE', FALSE);
}

if (\OpenFuego\VERBOSE == TRUE) {
	ini_set('display_errors', 1);
	ini_set('error_reporting', E_ALL);
}
else {
	ini_set('display_errors', 0);
}

require_once(__DIR__ . '/lib/TwitterOAuth/TwitterOAuth.class.php');
require_once(__DIR__ . '/lib/Phirehose/OAuthPhirehose.class.php');

spl_autoload_register(function($className) {
	$className = str_replace('OpenFuego' . '\\', '', $className);
	$className = strtr($className, '\\', DIRECTORY_SEPARATOR);	
	$path = __DIR__ . '/' . $className . '.class.php';

	if (is_readable($path)) {
		include_once($path);
	}
});

/* Setting miscellaneous constants */
define(__NAMESPACE__ . '\BASE_DIR', __DIR__);
define(__NAMESPACE__ . '\TMP_DIR', BASE_DIR . '/tmp');
define(__NAMESPACE__ . '\POSTMASTER', __NAMESPACE__ . '@' . __NAMESPACE__ . '.local'); // from address on error e-mails

if (!is_dir(TMP_DIR)) {
	mkdir(TMP_DIR);
	file_put_contents(TMP_DIR . '/nothing', 'This file was created to initialize the cache directory. You can delete it.');
}

const TWITTER_PREDICATE_LIMIT = 5000;

define(__NAMESPACE__ . '\BITLY_PRO_DOMAINS', serialize(
	array(  // assume these domains are shortened with Bitly
		'bit.ly',
		'bitly.com',
		'j.mp'
	)
));

define(__NAMESPACE__ . '\SHORT_DOMAINS', serialize(
	array(  // domains whose "long" urls are already short
		'twitpic.com',
		'instagr.am',
		'instagram.com',
		'yfrog.com',
		'twitpic.com',
		'vimeo.com',
		'i.imgur.com',
		'mlkshk.com',
		'lockerz.com',
		'path.com',
		'vine.co'
	)
));

const
	DB_DRIVER = 'mysql',
	USER_AGENT = 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
	REFERRER = 'http://google.com';

const 
	IMAGE_SIZE_MAX=5000, 
	IMAGE_SIZE_MIN=125,
	DAYS_TO_KEEP_ANY_LINK=2,	//regardless of weighted count, we keep links this long
	MIN_WEIGHTED_COUNT_PERMANENT_KEEP=10,  //never delete a link if it hits this threshold
	DAYS_TO_KEEP_SHORT_LINK_CACHE=2;  //the 'expanded urls' will be deleted after this number of days (since last_seen).  If a link is shared again later, the row will be inserted again
/*
if (file_exists(OPENFUEGO_DIR . '/openfuego-overrides.php')) {
	include_once(OPENFUEGO_DIR . '/openfuego-overrides.php');
}
*/