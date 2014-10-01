<?php
// IRB class used to generate ratings in Gonzalez method
//contains a league variable to hold the teams
//contains DB connection to get teams for league


//updated 03-06-2014 Vanswa Garbutt

require "libs/calculatorBase.php";
require "DBConn.php";
require "libs/teamClasses.php";
require "libs/League.php";

class gonzalez extends calculatorBase{
  
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
      	//make teamForGonzalez object
      	$team = new teamForGonzalez($name, $id);
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
    $query =  "SELECT date, event, event_id, w_team, l_team, w_id, l_id, ot,note, venue FROM results WHERE note not in ('S','JV','JV TIE', 'Ladies\'','Alumni','ASG') and date> '$startDate'  and date<'$this->endDate'";
   
    $results = $this->conn->executeSelectQuery($query);
    
    while($row = $results->fetch_assoc()){  
      if($row["event_id"]){ //if there is an event id there was a game, I'm sure there is a better way to check...	
    	//add the 2 teams into League
    	//check if there was a jv team that played
      
      	  $winIndex = $this->addToLeague($row["w_team"], $row["w_id"]);
      	  $loseIndex = $this->addToLeague($row["l_team"], $row["l_id"]);
		  //I calculate each game in order from the fetched data in the table
		 
		  $record = array($row["venue"],$winIndex,$loseIndex, $row["ot"]);
		 
		  $this->updateTeam($record);
      	
      }
    }   
    $this->conn->closeConnection();//done with the DB    
  }

  
  
  
  
  //I am changing this to work with the IRB method. I have to calculate ratings in real time based on matches in order
  // based on the International Rugby Board (IRB) .1 (.5 for overtime) * ((loserRating(+3 if home team)) - (winnerRating (+3 if home team))) + 1; 
  protected function updateTeam($params){
  
    $venue = $params[0]; 
    $winIndex = $params[1];
    $lossIndex = $params[2];
	$ot = $params[3];
	$otModifier = .5; 
	$homeAdv = 3;
	$gameResultIndex = 1;
	
    //check if team is playing itself
    if($winIndex != $lossIndex){
      $team = $this->League->getTeamByIndex($winIndex);
      $opponent = $this->League->getTeamByIndex($lossIndex);
	 		
	if($ot){
	
	//if the winning team had home advantage they get 3 points
		if($team->getName() == $venue){
			$ratingExchange = $otModifier *(.1 * ($opponent->getRating() - ($team->getRating() + $homeAdv)) + $gameResultIndex); 
			if($ratingExchange <= 0){
			$ratingExchange = .01;
			}
			if($ratingExchange > 2){
			$ratingExchange = 2;
			}
			$opponent->exchangeLoss($ratingExchange);
			$team->exchangeWin($ratingExchange);
			
			//if the winning team had home advantage they get 3 points
		}else if($opponent->getName() == $venue){
			$ratingExchange = $otModifier * (.1 * (($opponent->getRating()+ $homeAdv) - $team->getRating() ) + $gameResultIndex); 
			if($ratingExchange <= 0){
			$ratingExchange = .01;
			}
			if($ratingExchange > 2){
			$ratingExchange = 2;
			}
			$opponent->exchangeLoss($ratingExchange);
			$team->exchangeWin($ratingExchange);
			
			// if no team had home advantage they get no extra points
		}else{
			$ratingExchange = $otModifier * (.1 * ($opponent->getRating() - $team->getRating()) + $gameResultIndex);
			if($ratingExchange <= 0){
			$ratingExchange = .01;
			}
			if($ratingExchange > 2){
			$ratingExchange = 2;
			}
			$opponent->exchangeLoss($ratingExchange);
			$team->exchangeWin($ratingExchange);
			}
			
		}else{
		//no over time is presented so calculate without otModifier
		
			if($team->getName() == $venue){
		//if the winning team had home advantage they get 3 points
		
			$ratingExchange = .1 * ($opponent->getRating() - ($team->getRating() + $homeAdv)) + $gameResultIndex; 
			if($ratingExchange <= 0){
			$ratingExchange = .01;
			}
			if($ratingExchange > 2){
			$ratingExchange = 2;
			}
			$opponent->exchangeLoss($ratingExchange);
			$team->exchangeWin($ratingExchange);
			
		}else if($opponent->getName() == $venue){
		//if the winning team had home advantage they get 3 points
			$ratingExchange = .1 * (($opponent->getRating()+ $homeAdv) - $team->getRating() ) + $gameResultIndex; 
			if($ratingExchange <= 0){
			$ratingExchange = .01;
			}
			if($ratingExchange > 2){
			$ratingExchange = 2;
			}
			$opponent->exchangeLoss($ratingExchange);
			$team->exchangeWin($ratingExchange);
			
		}else{
		// if no team had home advantage they get no extra points
			$ratingExchange = .1 * ($opponent->getRating() - $team->getRating()) + $gameResultIndex;
			if($ratingExchange <= 0){
			$ratingExchange = .01;
			}
			if($ratingExchange > 2){
			$ratingExchange = 2;
			}
			$opponent->exchangeLoss($ratingExchange);
			$team->exchangeWin($ratingExchange);
			
			 }
		  }
		
		
		}		
	}

}
?>


