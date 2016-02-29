<?php
/**
 * Random Image Link System
 *
 * @author 		@MaxAbsorbency on twitter
 *
 */	

require_once('./config.php');

$mysqli = new mysqli($CONFIG['sql_host'], $CONFIG['sql_user'], $CONFIG['sql_pass'], $CONFIG['sql_database']);

if ($mysqli->connect_errno) {
    echo "Database connection failed with error: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

?>

<HTML>
<HEAD>
<TITLE><?php echo $CONFIG['page_title']; ?></TITLE>

<style>
html,
body {
  margin:0;
  padding:0;
  height:100%;
  background: #000000;
}

a.imgLink {
	font-family: "Arial Black";
	font-size: 34px;

}

a.contactLink {
	font-family: "Arial Black";
	font-size: 12px;

}

a:link {
	font-weight: bold;
	text-decoration: none;
	color: #68EC33;
}

a:visited {
	font-weight: bold;
	text-decoration: none;
	color: #247304;
}

a:hover {
  color: orange;
  text-decoration: underline;
}

a:active {
  color: orange;
}

#content{
  text-align: center; 
  padding-top: 20px; 
  padding-bottom: 110px;
}

#wrapper {
  min-height:100%;
  position:relative;
  padding-left: 0px;
  padding-right: 0px;
  margin: 0 auto;
}

#header {
  padding:10px;
  height: 30px; 
  color: white;
  font: Verdana 33px;
  text-shadow: 2px 2px black; 
  text-align: left; 
}

#footer {
  position:absolute;
  bottom:0;
  width:100%;
  height:60px;
  color: white;
}

#footerText {
	font-weight: bold;
	text-decoration: none;
	color: #68EC33;	
  font-family: "Arial Black";
}

</style>


</HEAD>

<BODY>
<?php

$img_id = 0;
$img_filename = '0000.jpg';
$freshHit = false;

//Get the max value of img_id so we know if someone is requesting an image with a higher ID than the highest ID in the system
$sql = "SELECT max(img_id) total from " . $CONFIG['table_name'];
if(!$count_result = $mysqli->query($sql)){ die('Query 1 error [' . $mysqli->error . ']'); }
$count_row = $count_result->fetch_assoc();


$incoming_id = "";
if(!is_numeric($_GET["id"])) {
  $incoming_id = "";
} else {
  $incoming_id = intval($_GET["id"]);
}

if(empty($incoming_id)){
  $sql = "SELECT * FROM " . $CONFIG['table_name'] . " where reports < 5 ORDER BY RAND( ) LIMIT 1";
  if(!$result = $mysqli->query($sql)){ die('Query 2 error [' . $mysqli->error . ']'); }
  $row = $result->fetch_assoc();

	$img_id = $row['IMG_ID'];
  $img_filename = $row['FILENAME'];
  $img_location = $CONFIG['img_directory'] . $img_filename;

	$freshHit = true;

} else {

  //If an ID that is too high is given, get the image with the highest available ID
	if($incoming_id > intval($count_row['total'])){
    $sql = "SELECT * from " . $CONFIG['table_name'] . " where img_id = (select max(img_id) from " . $CONFIG['table_name'] . " where reports < 5)";

    if(!$result = $mysqli->query($sql)){ die('Query 3 error [' . $mysqli->error . ']'); }
    $row = $count_result->fetch_assoc();

    $img_id = $row['IMG_ID'];
    $img_filename = $row['FILENAME'];
    $img_location = $CONFIG['img_directory'] . $img_filename;

    $freshHit = false;

	} else {

    //If an int ID is given, make sure it is a valid record
    $sql = "SELECT * from " . $CONFIG['table_name'] . " where img_id = " . $incoming_id;

    if(!$result = $mysqli->query($sql)){ die('Query 4 error [' . $mysqli->error . ']'); }
    $row = $result->fetch_assoc();

    //If this image has been reported too many times, get a random image again
    if(intval($row['REPORTS']) > 5){
      $sql = "SELECT * FROM " . $CONFIG['table_name'] . " where reports < 5 ORDER BY RAND( ) LIMIT 1";
      if(!$result = $mysqli->query($sql)){ die('Query 2 error [' . $mysqli->error . ']'); }
      $row = $result->fetch_assoc();

      $img_id = $row['IMG_ID'];
      $img_filename = $row['FILENAME'];
      $img_location = $CONFIG['img_directory'] . $img_filename;

      $freshHit = true;


    } else {

      //Otherwise, display the image
      $img_id = $row['IMG_ID'];
      $img_filename = $row['FILENAME'];
      $img_location = $CONFIG['img_directory'] . $img_filename;

      $freshHit = false;
    } 

	}
}

$hitCount = 0;

$sql = "SELECT hits from " . $CONFIG['table_name'] . " where img_id = " . $img_id;

if(!$result = $mysqli->query($sql)){
	die('Query 6 error [' . $mysqli->error . ']' . $sql);
}

$row = $result->fetch_assoc();
$hitCount = $row['hits'];

//If rarity flag is set to true, display rarity flavor text
$rarityText = '';

if($CONFIG['rarity_flag']){

  if($hitCount == 0) { $rarityText = $CONFIG['rarity0']; }
  else if ($hitCount == 420) $rarityText = $CONFIG['rarity5']; //Special case
  else if($hitCount > 0 && $hitCount <= 15) { $rarityText = $CONFIG['rarity1']; }
  else if($hitCount > 15 && $hitCount <= 30) { $rarityText = $CONFIG['rarity2']; }
  else if($hitCount > 30 && $hitCount <= 50) { $rarityText = $CONFIG['rarity3']; }
  else if($hitCount > 50) { $rarityText = $CONFIG['rarity4']; }

}

if($freshHit){
	$sql = "UPDATE " . $CONFIG['table_name'] . " SET hits = hits + 1 where img_id = " . $img_id;

	if(!$result = $mysqli->query($sql)){
		die('Query 7 error [' . $mysqli->error . '] ' . $sql);
	}
}

?>

<div id="wrapper">
	<div id="header"></div>
	<div id="content">

		<a href="./" class=><img id="img" style="text-align: center;" name="img" src="<?php echo $img_location ?>" alt="#<?php echo $img_id; ?>"></a>

		<br />

		<label for="img"><a href="<?php echo $CONFIG['site_url'] ?>?id=<?php echo $img_id ?>" class="imgLink"><?php echo $CONFIG['link_text'] ?></a></label>
	</div>


	<div id="footer">
		<div id="footerText">This is #<?php echo $img_id ?> and has been viewed <?php echo $hitCount; ?> times. <?php echo $rarityText; ?></div>
		<a href="<?php echo $CONFIG['contact_link'] ?>" class="contactLink">Contact Me</a> || <a href="<?php echo $CONFIG['source_code_url'] ?>" class="contactLink">Source</a>
	</div>
</div>


</BODY>
</HTML>