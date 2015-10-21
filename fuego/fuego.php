<?php 
 /*
  * getItems($quantity, $hours, $scoring, $metadata)
  *
  * $quantity (int): Number of links desired. Default 20.
  * $hours (int): How far back to look for links. Default 24.
  * $scoring (bool): TRUE to employ  "freshness vs. quality" algorithm
  *   or FALSE to simply return most frequently tweeted links. Default TRUE.
  * $metadata (bool): TRUE to hydrate URLs with Embed.ly metadata.
  *   An API key must be set in config.php. Default FALSE.
 */

require('./init.php');
use OpenFuego\app\Getter as Getter;


$requireQuality=true;
if ( isset ( $_GET['showAll']) ) {
   $requireQuality=false;
} 

$fuego = new Getter();
$items = $fuego->getItems(20, 24, $requireQuality, TRUE); // quantity, hours, scoring, metadata

print "<a href='fuego.php'>BE SMART</a> | <a href='fuego.php?showAll'>MOST RECENT 20</a><BR><BR>";

print '<pre>';
print_r($items);
print '</pre>';
?>
