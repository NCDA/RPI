<?php
// RPI class used to genderate RPI ratings
//contains a league variable to hold the teams
//contains db connection to get teams for league
//contains a startDate for the beginning of the season
//	that can change based on input from form

//updated 01-13-2014 Andy Lieblich

require "libs/calculatorBase.php";
require "libs/DBConn.php";
require "libs/teamClasses.php";
require "libs/League.php";

class Rpi extends calculatorBase{
  
  function __construct(){
    parent::__construct();
  }
   
  //adds team to league if not already in it
  //returns the index
  protected function addToLeague($name, $id){
    //check for an empty name and disregard the team completely :(
    if(empty($name)){
      return -1;
    } else {
      $index = $this->League->findTeamIndex($id);
      if($index == -1){ // if it is -1 the team doesnt exist, see libs/League.php
      	//make teamForRPI object
      	$team = new teamForRPI($name, $id);
      	//push into League while also getting its index
	      return $this->League->addTeam($team);
      } else {
	       //is in league return the index
	       return $index;
      }
    }
  }
  
  //takes in 2 team indexes to get the correct team from $this->Leauge
  //and also bool if the team who is being updated won or loss
  protected function updateTeam($params){
    $teamIndex = $params[0];
    $opponentIndex = $params[1];
    $win = $params[2];
    //check if team is playing itself
    if($teamIndex != $opponentIndex){
      $team = $this->League->getTeamByIndex($teamIndex);
      $opponent = $this->League->getTeamByIndex($opponentIndex);
      if($win){
        $team->addWin();
      } else {
        $team->addLoss();
      }
        $team->addTeamPlayed($opponent);
    }
  }

}
?>