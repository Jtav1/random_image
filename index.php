<?php
/**
 * Pepe Link System
 *
 * @author 		@MaxAbsorbency on twitter
 *
 */	

require_once('./config.php');

$mysqli = new mysqli($INFO['sql_host'], $INFO['sql_user'], $INFO['sql_pass'], $INFO['sql_database']);

if ($mysqli->connect_errno) {
    echo "Database connection failed with error: (" . $mysqli->connect_errno . ") " . $mysqli->connect_error;
}

?>

<HTML>
<HEAD>
<TITLE>Pepe Link Generator</TITLE>

<style>
html,
body {
  margin:0;
  padding:0;
  height:100%;
  background: #000000;
}

a.pepeLink {
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


img.chromoji { 
  width:1em !important; 
  height:1em !important; 
}

.chromoji-font, 
#chromoji-font { 
  font-size:1em !important; 
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

#menubar {
  background: rgba(22, 22, 22, 0.9); 
  padding-top: 0.1em;
  padding-bottom: 0.1em;
  padding-right: 0;
  text-align: center;
}

#menubar ul {
  margin-left: 0;
  padding-left: 0;
  display: inline;
} 

#menubar ul li {
  margin-left: 0em;
  margin-bottom: 0em;
  border: 0px solid #000;
  list-style: none;
  display: inline;
}

#menubar ul li a {
  text-decoration: none;
  padding: 4px 38px 5px;
  border-radius: 5px;
/*  box-shadow: 1px 1px 5px #989999; */
  background: #6694ae url("../img/grad.png") repeat-x;
  font: bold 16px sans-serif ;
  color: rgba(66, 66, 66, 1);
}

#menubar ul li a.current {
  background: #5ca2ca url("../img/grad.png") repeat-x;
}

#context {
  text-indent: 0em; 
  list-style-position: outside;
}

#context li {
  padding-bottom: 1em;
}

#footerText {
	font-weight: bold;
	text-decoration: none;
	color: #68EC33;	
}

hr {
  display: block;
  height: 1px;
  border: 0;
  border-top: 1px solid #aaa;
  margin: 1em 0;
  padding: 0;
}

</style>


</HEAD>

<BODY>
<?php

$pepe_id = 0;
$pepe_filename = '0000.jpg';
$freshHit = false;

//GET RANDOM PEPE ID IF ONE IS NOT GIVEN
$sql = "SELECT max(pepe_id) total from pepe_list";

if(!$result = $mysqli->query($sql)){
	die('Query error [' . $mysqli->error . ']');
}

$row = $result->fetch_assoc();

if(empty($_GET["id"])){
	$pepe_id = rand(1,intval($row['total']));
	$freshHit = true;
} else {
	if(intval($_GET["id"]) > intval($row['total'])){
		$pepe_id = intval($row['total']);
	} else {
		$pepe_id = $_GET["id"];
	}
}

//GET FILENAME FOR THE PEPE ID - THIS WILL NORMALLY BE THE PEPE_ID PADDED TO 4 DIGITS WITH LEADING 0s. FILE EXTENSION COULD BE WHATEVER.
$sql = "SELECT filename from pepe_list where pepe_id = " . $pepe_id;

if(!$result = $mysqli->query($sql)){
	die('Query error [' . $mysqli->error . ']');
}

$row = $result->fetch_assoc();
$pepe_filename = $row['filename'];
$pepe_location = $INFO['pepe_directory'] . $pepe_filename;


$hitCount = 0;
$rarityText = "SAMPLE TEXT";

$sql = "SELECT hits from pepe_list where pepe_id = " . $pepe_id;

if(!$result = $mysqli->query($sql)){
	die('Query error [' . $mysqli->error . ']');
}

$row = $result->fetch_assoc();
$hitCount = $row['hits'];


if($hitCount == 0) { $rarityText = $INFO['rarity0']; }
else if ($hitCount == 420) $rarityText = $INFO['rarity5']; //Special case
else if($hitCount > 0 && $hitCount <= 15) { $rarityText = $INFO['rarity1']; }
else if($hitCount > 15 && $hitCount <= 30) { $rarityText = $INFO['rarity2']; }
else if($hitCount > 30 && $hitCount <= 50) { $rarityText = $INFO['rarity3']; }
else if($hitCount > 50) { $rarityText = $INFO['rarity4']; }


if($freshHit){
	$sql = "UPDATE pepe_list SET hits = hits + 1 where pepe_id = " . $pepe_id;

	if(!$result = $mysqli->query($sql)){
		die('Query error [' . $mysqli->error . ']');
	}
}


?>

<div id="wrapper">
	<div id="header"></div>
	<div id="content" style="text-align: center; padding-top: 20px; padding-bottom: 110px;">

		<a href="./" class=><img id="pepe" style="text-align: center;" name="pepe" src="<?php echo $pepe_location; ?>" alt="Pepe #<?php echo $pepe_id; ?>"></a>

		<br />

		<label for="pepe"><a href="https://dix.sexy/pepe/?id=<?php echo $pepe_id ?>" class="pepeLink">LINK DIRECTLY TO THIS PEPE</a></label>
	</div>


	<div id="footer">
		<div id="footerText">This pepe has been visited <?php echo $hitCount; ?> times. <?php echo $rarityText; ?></div>
		<a href="https://twitter.com/MaxAbsorbency" class="contactLink">Contact Me</a>
	</div>
</div>


</BODY>
</HTML>