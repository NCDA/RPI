<?php

require "DBConn.php";
require "libs/teamClasses.php";
require "libs/League.php";

 class view {
	protected $conn;
	protected $startYear = 2012;
	protected $endYear = 2013;
	public $label;
	
	function __construct(){
		$this->League = new League();
		$this->conn = new DBConn();
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
	
	protected function addToLeague($name, $id){
		//check for an empty name. If empty it returns -1 and does not add the team
		if(empty($name)){
			return -1;
		} else {
			$index = $this->League->findTeamIndex($id);
			if($index == -1){ // if it is -1 the team doesn't exist
				//using teamForRPI because it already utilizes an array tracking teams played.
				$team = new teamForRPI($name, $id);
				//push into League while also getting its index
				return $this->League->addTeam($team);
			} else {
				//is in league return the index
				return $index;
			}
		}
	}
	
	// add other functions to allow for specifying teams, scores, and other parameters.
	protected function populateLeague(){
		$startDate = $this->startYear . "-08-01";
		$this->endDate = $this->endYear ."-07-01";
		$query = "SELECT date, event, event_id, w_team, l_team, w_id, l_id, ot,note, venue FROM results WHERE note not in ('S','JV','JV TIE', 'Ladies\'','Alumni','ASG') and date> '$startDate'  and date<'$this->endDate'";
	
		$results = $this->conn->executeSelectQuery($query);
	
		while($row = $results->fetch_assoc()){
			if($row["event_id"]){ 
				if(strpos($row["w_team"],'-JV') === false && strpos($row["l_team"],'-JV') === false) {
					$winIndex = $this->addToLeague($row["w_team"], $row["w_id"]);
					$loseIndex = $this->addToLeague($row["l_team"], $row["l_id"]);
					//create teamForView to store information about match to parse later and display
					$wteam = new teamForView($row["w_team"],$row["date"],false,$row["ot"], $row["w_id"]);
					$lteam = new teamForView($row["l_team"],$row["date"],true,$row["ot"], $row["l_id"]);
					
					$this->League->getTeamByIndex($this->League->findTeamIndex($row["w_id"]))->addTeamPlayed($lteam);
					$this->League->getTeamByIndex($this->League->findTeamIndex($row["l_id"]))->addTeamPlayed($wteam);
					
					
					
				}
			}
		}
		$this->conn->closeConnection();//done with the DB
	}
	
	//display results in order by team 
	
	
}




?>