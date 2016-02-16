<?php

/**
 * The Template for displaying all single posts.
 *
 * @package Independent Publisher
 * @since   Independent Publisher 1.0 
  template name: Trending Hack

 */

error_reporting(E_ALL);
$ch = curl_init( "https://africa2.rizing.org/trending/");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result=curl_exec ($ch);
curl_close ($ch);
$result=str_replace("https://africa2.rizing.org","https://africa.rizing.org",$result);
$result=str_replace("url(/wp-content/","url(https://africa2.rizing.org/wp-content/",$result);
echo $result;
 
 ?>