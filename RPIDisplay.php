<?php  
  require "libs/Rpi.php";
  $RPI = new RPI();  
  if($_POST){
    $RPI->setStartingSeason($_POST["year"]);
  } else {
    $RPI->setStartingSeason("Current Season");
  }
  $RPI->calculate();
?>

<!doctype html>
<html>
<head>
  <title>NCDA RPI Ratings: <?php echo $RPI->label; ?> season</title>
   <LINK href="css/andys_sweet_styles.css" rel="stylesheet" type="text/css">
</head>
<body>
  <div id="RPI_Holder">
  <form actiom="RPIDisplay.php" method="post" id="rpi_form">
      <select name="year" value="options">
	<option value>Select season...</option>
	<option value="2010">2010/2011 Season</option>
	<option value="2011">2011/2012 Season</option>
	<option value="2012">2012/2013 Season</option>
	<option value="2013">2013/2014 Season</option>
      </select>
      <input type="submit" value="Calculate" id="btn_calc">
    </form>
    <div id="label">RPI ratings for the <?php echo $RPI->label; ?> season.</div>
    <ul>
      <?php
	for($i=0;$i<$RPI->League->getNumOfTeams();$i++){
	  $team =  $RPI->League->getTeamByIndex($i) ?>
	  <li>
	    <span class="li_name"><?php echo $i+1 . ". " . $team->getName()?></span>
	    <span class="li_rating"><?php echo number_format($team->getRating(), 5)?></span>
	  </li>
      <?php  } ?>  
    </ul>
  </div>
</body>
</html>
