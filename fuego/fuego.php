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
$displayFormat="json";
if ( isset ( $_GET['rss'] ) ) {
	$displayFormat='rss';
}

$fuego = new Getter();
$items = $fuego->getItems(20, 24, $requireQuality, TRUE); // quantity, hours, scoring, metadata

if ($displayFormat=='json') {
	print "<a href='fuego.php'>BE SMART</a> | <a href='fuego.php?showAll'>MOST RECENT 20</a> | <a target='blank' href='fuego.php?showAll&rss'>RSS ALL</a> | <a target='blank' href='fuego.php?rss'>RSS SMART</a><BR><BR>";
	print '<pre>';
	print_r($items);
	print '</pre>';
} else if ($displayFormat=='rss') {
	echo '
<?xml version="1.0" ?>
<rss version="0.92">
	<channel>
		<title>RIZE FUEGO FEED</title>
		<description>A feed used by RIZE team to go to fuego</description>
		<link>http://africa.rizing.org</link>
		<language>en-us</language>
	';
	if ( $items ) {
		foreach ($items as $key => $item) {
			$title = $item['tw_text'];
			$link = $item['url'];
			$desc = $item['tw_text'];

			/* often times some metadata values are set and others aren't, so we check each one.  The fuego backend process fills this section using an embed.ly api key */
			if ( isset ($item['metadata']) ) {
				$m = $item['metadata'];

				if ( isset ( $m['title'] ) ) {
					$title = $m['title'];	
				}
				if ( isset ($m['url'] ) ) {
					$url = $m['url'];
				}
				
				if ( isset ($m['description'] ) ) {
					$desc = $m['description'];
				}
			}
			echo '
			<item>
				<title>' . $title . '</title>
				<link>' . $url . '</link>
				<description>
					<![CDATA[
					' . $desc . '
					]]>
				</description>
			</item>
			';
		};
	}
	echo '
	</channel>
</rss>
	';
}


?>
