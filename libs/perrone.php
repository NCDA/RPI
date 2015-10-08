<?php
// BCS class used to generate ratings Perrone method
//contains a league variable to hold the teams
//contains DB connection to get teams for league


//created 06-23-2014 Vanswa Garbutt

require "libs/calculatorBase.php";
require "libs/DBConn.php";
require "libs/teamClasses.php";
require "libs/League.php";

class perrone extends calculatorBase{
  protected $teamArray = array();
  function __construct(){
    parent::__construct();
  }
   
  //adds team to league if not already in it
  //returns the index
  protected function addToLeague($name, $id){
    //check for an empty name. If empty it returns -1 and does not add the team
    if(empty($name)){
      return -1;
    } else {
      $index = $this->League->findTeamIndex($id);
      if($index == -1){ // if it is -1 the team doesn't exist
      	//make teamForPerrone object
      	$team = new teamForPerrone($name, $id);
      	//push into League while also getting its index
	      return $this->League->addTeam($team);
      } else {
	       //is in league return the index
	       return $index;
      }
    }
  }
  
  //I am overloading this function because I have to calculate mine as a 
  //running update per game instead of at the end of all win/loss of each team
  //the 
    protected function populateLeague(){
    $startDate = $this->startYear . "-08-01";
    $this->endDate = $this->endYear ."-07-01";
    $query =  "SELECT date, event, event_id, w_team, l_team, w_id, l_id, ot,note, venue FROM results WHERE note not in ('S','JV', 'Ladies\'','Alumni','ASG') and date> '$startDate'  and date<'$this->endDate'";
    $teamQuery = "SELECT join_id, acronym FROM teams";
    $results = $this->conn->executeSelectQuery($query);
	 $teamResults = $this->conn->executeSelectQuery($teamQuery);
	//skip first entry
	$teamArray[0] = "skipped";
	//store team data into array 
	while($teamData = $teamResults->fetch_assoc()){
		$teamArray[$teamData["join_id"]] = $teamData["acronym"];
	}
    
    while($row = $results->fetch_assoc()){  
      if($row["event_id"]){ //if there is an event id there was a game, I'm sure there is a better way to check...	
    	//add the 2 teams into League
    	//check if there was a jv team that played
      	if(strpos($teamArray[$row["w_id"]],'-JV') === false && strpos($teamArray[$row["l_id"]],'-JV') === false) {
					if($row["w_id"] == 0 || $row["l_id"] == 0){
						// skip matches that do not count i.e having a win or loss id of 0
						continue;
					}
					$winIndex = $this->addToLeague($teamArray[$row["w_id"]], $row["w_id"]);
					$loseIndex = $this->addToLeague($teamArray[$row["l_id"]], $row["l_id"]);    
		 
		  $record = array($row["venue"],$winIndex,$loseIndex, $row["ot"]);
		 
		  $this->updateTeam($record);
      	}
      }
    }   
    $this->conn->closeConnection();//done with the DB    
  }

  
  protected function updateTeam($params){
  
    $venue = $params[0]; 
    $winIndex = $params[1];
    $lossIndex = $params[2];
	$ot = $params[3];

	
    //check if team is playing itself
    if($winIndex != $lossIndex){
      $winner = $this->League->getTeamByIndex($winIndex);
      $loser = $this->League->getTeamByIndex($lossIndex);
      
      $winner->addWin();
      $loser->addLoss();
      
      if($ot){
      	$winner->addOT();
      	$loser->addOT();
      }
      
	  $winnerWLP = $winner->getWLP(); 
	  $loserWLP = $loser->getWLP();
	 
       //winner score exchange
       
		if ($loserWLP == 0) {
			$winner->addPoints(2.5);
		}elseif ($loserWLP > 0 && $loserWLP < .25){
			$winner->addPoints(2.75);
		}elseif ($loserWLP >= .25 && $loserWLP <= .499){
			$winner->addPoints(3);
		}elseif ($loserWLP >= .5 && $loserWLP < .75){
			$winner->addPoints(3.25);
		}elseif ($loserWLP >= .75 && $loserWLP < 1){
			$winner->addPoints(3.5);
		}else{
			$winner->addPoints(4);
		}
		
		// loser points awarded
		if ($winnerWLP == 0) {
				$loser->addPoints(-2);
			}elseif ($winnerWLP > 0 && $winnerWLP < .25){
				$loser->addPoints(-1.75);
			}elseif ($winnerWLP >= .25 && $winnerWLP <= .499){
				$loser->addPoints(-1.625);
			}elseif ($winnerWLP >= .5 && $winnerWLP < .75){
				$loser->addPoints(-1.5);
			}elseif ($winnerWLP >= .75 && $winnerWLP < 1){
				$loser->addPoints(-1.375);
			}else{
				$loser->addPoints(-1.25);
			}
		
		//loser in over time exchange 
		if ($ot){
			if ($winnerWLP == 0) {
				$loser->addPoints(.875);
			}elseif ($winnerWLP > 0 && $winnerWLP < .25){
				$loser->addPoints(.9);
			}elseif ($winnerWLP >= .25 && $winnerWLP <= .499){
				$loser->addPoints(.925);
			}elseif ($winnerWLP >= .5 && $winnerWLP < .75){
				$loser->addPoints(.95);
			}elseif ($winnerWLP >= .75 && $winnerWLP < 1){
				$loser->addPoints(.975);
			}else{
				$loser->addPoints(1.05);
			}	
		}
     }	
  }

}
?>


