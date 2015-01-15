<?php
require "libs/view.php";

$VIEW = new VIEW();

if($_POST){
    $VIEW->setStartingSeason($_POST["year"]);
  } else {
    $VIEW->setStartingSeason("Select season...");
  }

?>

<!doctype html>
<html>
<head>

<title>  NCDA Pool Viewer: <?php echo $VIEW->label; ?> Season</title>

</head>

<body>

<form action="viewDisplay.php" method="post" id="view_form">
      <select name="year" value="options">
	<option value="Select season...">Select season...</option>
	<option value="2010">2010/2011 Season</option>
	<option value="2011">2011/2012 Season</option>
	<option value="2012">2012/2013 Season</option>
	<option value="2013">2013/2014 Season</option>
      </select>

 <input type="submit" value="Display" id="btn_calc">
 
 <div>Pool View for <?php echo $VIEW->label; ?> Season.</div> 
 
 <?php 
 for($i=0; $i < $VIEW->League->getNumOfTeams(); $i++){
	$team = $VIEW->League->getTeamByIndex($i);
	echo $team->getName(); 
	echo $team->getId();
	echo "->";
		for($j=0; $j < $team->getNumOfOpponents(); $j++){
		$opponent = $team->getOpponentByIndex($j);
		echo $opponent->getName();
		echo $opponent->getId();
		echo"->";
		}
	echo "</br>";
 } ?>
 
 
 </body>
 </html>
 