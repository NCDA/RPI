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

	### wlp needs to overload the sort method to attribute to tie breakers.
	#go through the league and check for equal ratings
	  ## break ties by most games played
	  ### then break by lowest team id
	  public function sort(){
    for($numOfPasses=0;$numOfPasses<$this->League->getNumOfTeams();$numOfPasses++){
      //skip last iteration on the loop b/c there is nothing left to compare to
      for($i=0; $i<$this->League->getNumOfTeams() -1; $i++){
      	if($this->League->getTeamByIndex($i)->getRating() == $this->League->getTeamByIndex($i+1)->getRating()){
      	  if($this->League->getTeamByIndex($i)->getGamesPlayed() < $this->League->getTeamByIndex($i+1)->getGamesPlayed()){
			  $this->League->swap($i, $i+1);
		  }elseif($this->League->getTeamByIndex($i)->getGamesPlayed() == $this->League->getTeamByIndex($i+1)->getGamesPlayed()){
			  if($this->League->getTeamByIndex($i)->getId() > $this->League->getTeamByIndex($i+1)->getId()){
				  $this->League->swap($i, $i+1);
			  }
		  }
			  
      	} elseif($this->League->getTeamByIndex($i)->getRating() < $this->League->getTeamByIndex($i+1)->getRating()){
			$this->League->swap($i, $i+1);
		}
      }
    }
  }
	
	
}

?>