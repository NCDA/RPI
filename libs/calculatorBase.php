<?php 
//01-10-2014 Andy Lieblich
//base class for the rating calculators to extend off of

abstract class calculatorBase{
	public 		$League;// made public to easily print out in html, not sure how to do it better
	protected $conn;
	protected $startYear = 2012;
	protected $endYear = 2013;
	//label to easily output the season being claculated
  public $label;
	
	function __construct(){
	   $this->League = new League();
	   $this->conn = new DBConn();
	}

	//updates team wins/losses and or team played depending on current calcuator
	//see child classes for implementation
	//changed to take in an array as a parmeter b/c different subclasses may 
	//				need differnt amount of arguments
	protected abstract function updateTeam($params);
	//adds team to league if not already in it
  //returns the index
  //see child classes for implementation
	protected abstract function addToLeague($name, $id);

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
  protected function populateLeague(){
    $startDate = $this->startYear . "-08-01";
    $this->endDate = $this->endYear ."-07-01";
    $query = "SELECT date, event, event_id, w_team, l_team, w_id, l_id, ot,note, venue FROM results WHERE note not in ('S','JV','JV TIE', 'Ladies\'','Alumni','ASG') and date> '$startDate'  and date<'$this->endDate'";
    
    $results = $this->conn->executeSelectQuery($query);
    
    while($row = $results->fetch_assoc()){  
      if($row["event_id"]){ //if there is an event id there was a game, im sure there is a better way to check...	
    	//add the 2 teams into League
    	//check if there was a jv team that played
      	if(strpos($row["w_team"],'-JV') === false && strpos($row["l_team"],'-JV') === false) {
      	  $winIndex = $this->addToLeague($row["w_team"], $row["w_id"]);
      	  $loseIndex = $this->addToLeague($row["l_team"], $row["l_id"]);
      	  //update the 2 teams win or lose, and add the team played
      	  $win_params = array($winIndex, $loseIndex, true);
      	  $loss_params = array($loseIndex, $winIndex, false);
      	  $this->updateTeam($win_params);
      	  $this->updateTeam($loss_params);
      	}
      }
    }   
    $this->conn->closeConnection();//done with the DB    
  }

  //calls method on each team in #this->League to calc its rating depending 
  //	on which rating calculator is calling it
  //then calls method to sort based on rating
  public function calculate(){
    for($i=0; $i<$this->League->getNumOfTeams(); $i++){
     $this->League->getTeamByIndex($i)->calcRating();
    }
    $this->sort();
  }

  //sorts based off of getRating() from various different rating calculators
  //different ratings may be RPI, NHL, Win/Loss, etc...
  public function sort(){
    for($numOfPasses=0;$numOfPasses<$this->League->getNumOfTeams();$numOfPasses++){
      //skip last iteration on the loop b/c there is nothing left to compare to
      for($i=0; $i<$this->League->getNumOfTeams() -1; $i++){
      	if($this->League->getTeamByIndex($i)->getRating() < $this->League->getTeamByIndex($i+1)->getRating()){
      	  $this->League->swap($i, $i+1);
      	}
      }
    }
  }

}