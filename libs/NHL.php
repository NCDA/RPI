<?php
//01-15-2014 Andy Lieblich
//class to for NHL style calculator

require "libs/calculatorBase.php";
require "libs/DBConn.php";
require "libs/teamClasses.php";
require "libs/League.php";

class NHL extends calculatorBase{
  function __construct(){
    parent::__construct();
  }

  public function setStartingSeason($year){
		if($year <> "Select season..."){
			$this->startYear = $year;
		} else{
			$this->startYear = date("Y") - 1;
		}
		$this->endYear = $this->startYear + 1;
		$this->label = "$this->startYear/" . "$this->endYear";
		$this->populateLeague();
	
	}
	
	//need to overload this function b/c this class needs an additonal 
	//atribute from the results query (ot_id)
  protected function populateLeague(){
    $startDate = $this->startYear . "-08-01";
    $this->endDate = $this->endYear ."-07-01";
    $query = "SELECT date, event, event_id, w_team, l_team, w_id, l_id, ot_id FROM results WHERE event <> 'Scrimmage' and date> '$startDate' and date< '$this->endDate'";
    $teamQuery = "SELECT join_id, acronym FROM teams";
	
    $results = $this->conn->executeSelectQuery($query);
	$teamResults = $this->conn->executeSelectQuery($teamQuery);
	//store team data into array 
	$teamArray[0] = "skipped";
	while($teamData = $teamResults->fetch_assoc()){
			$teamArray[$teamData["join_id"]] = $teamData["acronym"];
	}
	
    while($row = $results->fetch_assoc()){  
      if($row["event_id"]){ //if there is an event id there was a game, im sure there is a better way to check...	
    	//add the 2 teams into League
    	//check if there was a jv team that played
      	if(strpos($teamArray[$row["w_id"]],'-JV') === false && strpos($teamArray[$row["l_id"]],'-JV') === false) {
					if($row["w_id"] == 0 || $row["l_id"] == 0){
						// skip matches that do not count i.e having a win or loss id of 0
						continue;
					}
      	  $winIndex = $this->addToLeague($teamArray[$row["w_id"]], $row["w_id"]);
      	  $loseIndex = $this->addToLeague($teamArray[$row["l_id"]], $row["l_id"]);
      	  //update the 2 teams win or lose, and add the team played
      	  $win_params = array($winIndex, $loseIndex, true, $row["ot_id"]);
      	  $loss_params = array($loseIndex, $winIndex, false, $row["ot_id"]);
      	  $this->updateTeam($win_params);
      	  $this->updateTeam($loss_params);
      	}
      }
    }   
    $this->conn->closeConnection();//done with the DB    
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
      	$team = new teamForNHL($name, $id);
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
    $ot = $params[3];
    //check if team is playing itself
    if($teamIndex != $opponentIndex){
      $team = $this->League->getTeamByIndex($teamIndex);
      $opponent = $this->League->getTeamByIndex($opponentIndex);
      if($win){
      	//add 2 points for win, with or without ovetime
        $team->addPoints(2);
      } else {
      	if($ot){
      		//add 1 point for overtime loss
      		$team->addPoints(1);
      	}
      }
    }
  }
}
?>