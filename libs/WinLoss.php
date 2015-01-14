<?php


//class to for Win Loss calculator

require "libs/calculatorBase.php";
require "libs/DBConn.php";
require "libs/teamClasses.php";
require "libs/League.php";

class WinLoss extends calculatorBase{

	function __construct(){
		parent::__construct();
	}

	//difference is using teamRecord team class 
	protected function addToLeague($name, $id){
    //check for an empty name and disregard the team completely :(
    if(empty($name)){
      return -1;
    } else {
      $index = $this->League->findTeamIndex($id);
      if($index == -1){ // if it is -1 the team doesnt exist, see libs/League.php
      	//make teamForRPI object
      	$team = new teamRecord($name, $id);
      	//push into League while also getting its index
	      return $this->League->addTeam($team);
      } else {
	       //is in league return the index
	       return $index;
      }
    }
  }

//difference between this funciton and the updateTAem function in RPI.php
	//is not adding team played
  protected function updateTeam($params){
    $teamIndex = $params[0];
    $opponentIndex = $params[1];
    $win = $params[2];
		if($teamIndex != $opponentIndex){
      $team = $this->League->getTeamByIndex($teamIndex);
      //$opponent = $this->League->getTeamByIndex($opponentIndex);
      if($win){
        $team->addWin();
      } else {
        $team->addLoss();
      }
    }
	}

}

?>