<?php
//require "libs/DBConn.php";
//require "libs/teamClasses.php";
//require "libs/League.php";
require "libs/Rpi.php";
//$conn = new DBConn();

//$MSU = new teamForRPI();

//$CMU = new teamForRPI();

//$League = new League();

//$League->addTeam($MSU);
//$League->addTeam($MSU);

//echo $League->getTeam(0)->getName();
//echo $League->getTeam(1)->getName();

$RPI = new Rpi();

$RPI->calculate();

$RPI->display();



?>
