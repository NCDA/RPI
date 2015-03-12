<?php  
  require "libs/NHL.php";
  $NHL = new NHL();  
  if($_POST){
    $NHL->setStartingSeason($_POST["year"]);
  } else {
    $NHL->setStartingSeason("Current Season");
  }
  $NHL->calculate();
?>

<!doctype html>
<html>
<head>
  <title>NCDA NHL Ratings: <?php echo $NHL->label; ?> season</title>
   <LINK href="css/andys_sweet_styles.css" rel="stylesheet" type="text/css">
</head>
<body>
  <div id="RPI_Holder">
  <form actiom="NHLDisplay.php" method="post" id="NHL_form">
    <select name="year" value="options">
		<option value="Select season...">Select season...</option>
		<option value="2014">2014/2015 Season</option>
		<option value="2013">2013/2014 Season</option>
		<option value="2012">2012/2013 Season</option>
		<option value="2011">2011/2012 Season</option>
		<option value="2010">2010/2011 Season</option>
    </select>
      <input type="submit" value="Calculate" id="btn_calc">
    </form>
    <div id="label">NHL ratings for the <?php echo $NHL->label; ?> season.</div>
    <ul>
      <?php
	for($i=0;$i<$NHL->League->getNumOfTeams();$i++){
	  $team =  $NHL->League->getTeamByIndex($i) ?>
	  <li>
	    <span class="li_name"><?php echo $i+1 . ". " . $team->getName()?></span>
	    <span class="li_rating"><?php echo number_format($team->getRating(), 5)?></span>
	  </li>
      <?php  } ?>  
    </ul>
  </div>
</body>
</html>