<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

$dbhost = 'localhost:3306';
$dbuser = 'root';
$dbpass = '';
$conn = mysql_connect($dbhost, $dbuser, $dbpass);
if(! $conn ) {
  die('ERROR: Could not connect: ' . mysql_error());
}
$sql = "
	SELECT TIMESTAMPDIFF(MINUTE,first_seen, CONVERT_TZ(now(),'+00:00','-5:00')) as minutesSinceLastLink
	FROM openfuego_links
	order by link_id desc
	LIMIT 1
";

mysql_select_db('fuego');
$retval = mysql_query( $sql, $conn );
if(! $retval ) {
  die('ERROR: Could not get data: ' . mysql_error());
}
$row = mysql_fetch_array($retval, MYSQL_ASSOC);
mysql_close($conn);

$result =  array('minutesSinceLastLink' => $row['minutesSinceLastLink']);
header('Content-Type: application/json');
echo json_encode($result);

?>