<?php
require "libs/view.php";
$VIEW = new VIEW();
if($_POST){
    $VIEW->setStartingSeason($_POST["year"]);
  } else {
    $VIEW->setStartingSeason("Select season...");
  }
	$VIEW->sort();
?>

<!doctype html>
<html>
<head>

<title>NCDA Team Opponent Viewer: <?php echo $VIEW->label; ?> Season</title>
<style>
body {
	font-size: 1em;
	font-family: monospace;
}
.row-team {
	display: block;
} 
.opponent {
	
} 
</style>

</head>

<body>

<form action="poolDisplay.php" method="post" id="view_form">
      <select name="year" value="options">
	<option value="Select season...">Select season...</option>
	<option value="2015">2015/2016 Season</option>
	<option value="2014">2014/2015 Season</option>
	<option value="2013">2013/2014 Season</option>
	<option value="2012">2012/2013 Season</option>
	<option value="2011">2011/2012 Season</option>
	<option value="2010">2010/2011 Season</option>
      </select>

 <input type="submit" value="Display" id="btn_calc">
 
 <p>What opponents has a team faced in the <?php echo $VIEW->label; ?> Season?</p> 
 
	<table> 
		<tr>
			<th>ID</th>
			<th>Team</th>
		</tr>
 <?php
 for($i=0; $i < $VIEW->League->getNumOfTeams(); $i++){
	$team = $VIEW->League->getTeamByIndex($i);
	echo "<tr><td class='team-id'><em>" . $team->getId() . "</em></td>" ;
	echo "<td class='team-name'><strong>" . $team->getName() . "</strong></td>" ;	
		for($j=0; $j < $team->getNumOfOpponents(); $j++){
		$opponent = $team->getOpponentByIndex($j);
		echo "<td class='opponent'>" . $opponent->getName() . "</td>";
		}
	echo "</tr>";
 } ?>
 	</table>
	
<p>*Sorted by team ID. Opponents are chronological.</p>
 
 </body>
 </html>
 