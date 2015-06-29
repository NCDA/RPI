<?php  
  require "libs/WinLoss.php";
  $WinLoss = new WinLoss();  
  if($_POST){
    $WinLoss->setStartingSeason($_POST["year"]);
  } else {
    $WinLoss->setStartingSeason("Current Season");
  }
  $WinLoss->calculate();
?>

<!doctype html>
<html>
<head>
  <title>NCDA WinLoss Ratings: <?php echo $WinLoss->label; ?> season</title>
   <LINK href="css/andys_sweet_styles.css" rel="stylesheet" type="text/css">
</head>
<body>
  <div id="RPI_Holder">
  <form actiom="WinLossDisplay.php" method="post" id="WL_form">
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
    <div id="label">WinLoss ratings for the <?php echo $WinLoss->label; ?> season.</div>
	<table>
      <?php
	for($i=0;$i<$WinLoss->League->getNumOfTeams();$i++){
	  $team =  $WinLoss->League->getTeamByIndex($i) ?>
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