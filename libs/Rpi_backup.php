<?php
// RPI class used to genderate RPI ratings
//contains a league variable to hold the teams
//contains db connection to get teams for league
//contains a startDate for the beginning of the season
//	that can change based on input from form

require "libs/DBConn.php";
require "libs/teamClasses.php";
require "libs/League.php";

//looking back i should have just made this class
//extend off of the libs/League.php class...
class Rpi{
  public $League;// made public to easily print out in html, not sure how to do it better
  private $conn;
  Private $startYear = 2012;
  //label to easily output the season being claculated
  public $label;
  
  function __construct(){
    $this->League = new League();
    $this->conn = new DBConn();
  }
  
  //sets the starting year of the season to calculate,
  //then calls the method to populate the league
  //check if year is setm if not use default year
  public function setStartingSeason($year){  
    if($year != "Select season..."){
      $this->startYear = $year;     
    }   
    $this->endYear = $this->startYear + 1;
    $this->label = "$this->startYear/" . "$this->endYear";
    $this->populateLeague();
  }	
  
  //populate the league by querying the conn variable and
  //creating teams to put into $this->League 
  //update the wins/losses and team played against
  private function populateLeague(){
    $startDate = $this->startYear . "-08-01";
    $this->endDate = $this->endYear ."-07-01";
    $query = "SELECT date, event, event_id, w_team, l_team, w_id, l_id FROM results WHERE event <> 'Scrimmage' and date> '$startDate' and date< '$this->endDate'";
    
    $results = $this->conn->executeSelectQuery($query);
    
    echo "<br/>";
    while($row = $results->fetch_assoc()){  
      if($row["event_id"]){ //if there is an event id there was a game, im sure there is a better way to check...	
    	//add the 2 teams into League
    	//check if there was a jv team that played
      	if(strpos($row["w_team"],'-JV') === false && strpos($row["l_team"],'-JV') === false) {
      	  $winIndex = $this->addtoLeague($row["w_team"], $row["w_id"]);
      	  $loseIndex = $this->addtoLeague($row["l_team"], $row["l_id"]);
      	  //update the 2 teams win or lose, and add the team played
      	  $this->updateTeam($winIndex, $loseIndex, true);
      	  $this->updateTeam($loseIndex, $winIndex, false);
      	}
      }
    }   
    $this->conn->closeConnection();//done with the DB    
  }
  
  //adds team to league if not already in it
  //returns the index
  private function addToLeague($name, $id){
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
  private function updateTeam($teamIndex, $opponentIndex, $win){
    //check if team is playing itself, tis a silly thing...
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
  
  //calls method on each team in #this->League to calc its RPI
  //then calls method to sort based on rating
  public function calculate(){
    //RPI = (WP*0.25) + (OWP*0.50) + (OOWP*0.25)
    for($i=0; $i<$this->League->getNumOfTeams(); $i++){
     $this->League->getTeamByIndex($i)->calcRPI();
    }
    $this->sort();
  }
  
  //move this to base class for other calculators to use
  //sorts nuff said
  public function sort(){
    for($numOfPasses=0;$numOfPasses<$this->League->getNumOfTeams();$numOfPasses++){
      //skip last iteration on the loop b/c there is nothing left to compare to
      for($i=0; $i<$this->League->getNumOfTeams() -1; $i++){
      	if($this->League->getTeamByIndex($i)->getRating() < $this->League->getTeamByIndex($i+1)->getRating() ){
      	  $this->League->swap($i, $i+1);
      	}
      }
    }
  }

}

?>
