<?php  
  require "libs/gonzalez.php";
  $GONZALEZ = new GONZALEZ();
  if($_POST){
    $GONZALEZ->setStartingSeason($_POST["year"]);
  } else {
    $GONZALEZ->setStartingSeason("Select season...");
  }
  $GONZALEZ->sort();
?>

<!doctype html>
<html>
<head>

  <title>NCDA Gonzalez Ratings: <?php echo $GONZALEZ->label; ?> season</title>
   <LINK href="css/andys_sweet_styles.css" rel="stylesheet" type="text/css">
   
</head>
<body >

  <div id="RPI_Holder">
  <form action="gonzalezDisplay.php" method="post" id="rpi_form">
    <select name="year" value="options">
		<option value="Select season...">Select season...</option>
		<option value="2015">2015/2016 Season</option>
		<option value="2014">2014/2015 Season</option>
		<option value="2013">2013/2014 Season</option>
		<option value="2012">2012/2013 Season</option>
		<option value="2011">2011/2012 Season</option>
		<option value="2010">2010/2011 Season</option>
    </select>
      <input type="submit" value="Calculate" id="btn_calc">
    </form>
    <div id="label">Gonzalez RPI ratings for the <?php echo $GONZALEZ->label; ?> season.</div>
	<table>
      <?php
	for($i=0;$i<$GONZALEZ->League->getNumOfTeams();$i++){
	  $team =  $GONZALEZ->League->getTeamByIndex($i) ?>
	  <tr>
	    <td> <?php echo $i+1 ?></td>
		<td class="li_name"><?php echo $team->getName()?></td>
	    <td class="li_rating"><?php echo number_format($team->getRating(), 5)?></td>
	  </tr>
      <?php  } ?>  
    </table>
  </div>
</body>
</html>
