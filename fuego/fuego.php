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

$algo="recent";
$displayFormat="json";
$minWeightedCount=2;
$maxItems=20;
$hours=24;

$showConsole = true;
$requireQuality=false;

if (isset ($_GET['algo']) ) {
	$algo = $_GET['algo'];
}

if ( isset ( $_GET['displayFormat'] ) ) {
	$displayFormat=$_GET['displayFormat'];
}

if ( isset ( $_GET['minWeightedCount'] ) ) {
	$showConsole = false;
	$minWeightedCount = $_GET['minWeightedCount'];
}

if ( isset ( $_GET['maxItems'] ) ) {
	$maxItems = $_GET['maxItems'];
}
if ( isset ( $_GET['hours'] ) ) {
	$hours = $_GET['hours'];
}
if ( $algo=="smart" ) {
	$requireQuality = true;
}

$recentChecked = ($algo == 'recent' ? 'checked' : '');
$smartChecked = ($algo == 'smart' ? 'checked' : '');
$rssChecked = ($displayFormat == 'rss' ? 'checked' : '');
$jsonChecked = ($displayFormat == 'json' ? 'checked' : '');
$humanChecked = ($displayFormat == 'human' ? 'checked' : '');


$fuego = new Getter();


if ($showConsole) : ?>

	<script src='https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js'></script>
	<script type='text/javascript'>
		function onSubmit(){
			var weightedCount=document.getElementById('weightedCount').value;
			var displayFormat = document.getElementById('displayFormat').checked;
			return false;
		}
	</script>
	<form onSubmit='onSubmit();'>
		<label for='minWeightedCount'>Minimum Weighted Count</label><input id='minWeightedCount' name='minWeightedCount' type='text' value='<?php echo $minWeightedCount; ?>'>
		<BR><BR>
		<label for='maxItems'>Max Items</label><input id='maxItems' name='maxItems' type='text' value='<?php echo $maxItems; ?>'>
		<BR><BR>
		Display Type: 
		<input type='radio' name='displayFormat' value='human' <?php echo "$humanChecked"; ?> >human
		<input type='radio' name='displayFormat' value='rss' <?php echo "$rssChecked"; ?> >rss
		<input type='radio' name='displayFormat' value='json' <?php echo "$jsonChecked"; ?> >json
		<BR><BR>
		Algorithm:
		<input type='radio' name='algo' value='smart' <?php echo "$smartChecked"; ?> >Be Smart
		<input type='radio' name='algo' value='recent' <?php echo "$recentChecked"; ?> >Most Recent
		<BR><BR>

		<input type='submit' value='GO'>
	</form>
<?php die(); endif;


$items = $fuego->getItems($maxItems, $hours, $requireQuality, TRUE, $minWeightedCount); // quantity, hours, scoring, metadata

/*
	print "<a href='fuego.php'>BE SMART</a> | <a href='fuego.php?showAll'>MOST RECENT 20</a> | <a target='blank' href='fuego.php?showAll&rss'>RSS ALL</a> | <a target='blank' href='fuego.php?rss'>RSS SMART</a><BR><BR>";
	print '<pre>';
	print_r($items);
	print '</pre>';
*/
if ($displayFormat=='json') {
	print '<pre>';
	print_r($items);
	print '</pre>';	
} else if ($displayFormat=='human')  {
	echo '
		<html>
			<head>
				<title>RIZE Fuego</title>
				<style>
					IMG {max-width:800; max-height:400;}
				</style>
			</head>
			<body>
';
	if ( $items ) {
		$counter=0;
		foreach ($items as $key => $item) {
			$counter=$counter+1;
			$title = $item['tw_text'];
			$url = $item['url'];
			$desc = $item['tw_text'];
			$image="";

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

				if ( isset ($m['thumbnail_url'] ) ) {
					$image=$m['thumbnail_url'];
				}
			}
			echo '<h2><a href="' . $url . '">' . $title . '</a> (' . $counter . ')</h2>';
			if ($image != "") {
				echo '<img src="' . $image . '">';
			}
			echo 'Count: ' . $item['count'] . ' <BR>
			Weighted Count: ' . $item['weighted_count'] . '<BR>
			Desc: ' . $desc . ' <BR>';
		};
	}
	
} else {



	echo '
<?xml version="1.0" ?>
<rss version="2.0">
	<channel>
		<title>RIZE FUEGO FEED</title>
		<description>A feed used by RIZE team to go to fuego</description>
		<link>http://africa.rizing.org</link>
		<language>en-us</language>
	';
	if ( $items ) {
		$counter=0;
		foreach ($items as $key => $item) {
			$counter=$counter+1;
			$title = $item['tw_text'];
			$url = $item['url'];
			$desc = $item['tw_text'];
			$image="";

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

				if ( isset ($m['thumbnail_url'] ) ) {
					$image=$m['thumbnail_url'];
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
				<weightedCount>' . $item['weighted_count'] . '</weightedCount>
				<score>' . $item['score'] . '</score>

				<counter>' . $counter . '</counter>
			';

			if ($image != "") {
				echo "<enclosure url='$image' type='image/jpeg'/>"; //possibly lengths (size in bytes)
			}

			echo '
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
