<?php

require "libs/DBConn.php";
require "libs/teamClasses.php";
require "libs/League.php";

 class view {
	protected $conn;
	protected $startYear = 2014;
	protected $endYear = 2015;
	public $label;
	protected $teamArray = array();
	
	
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
		$teamQuery = "SELECT join_id, acronym FROM teams";
		$results = $this->conn->executeSelectQuery($query);
		 $teamResults = $this->conn->executeSelectQuery($teamQuery);
		//store team data into array 
		$teamArray[0] = "skipped";
		while($teamData = $teamResults->fetch_assoc()){
			$teamArray[$teamData["join_id"]] = $teamData["acronym"];
		}
	
		while($row = $results->fetch_assoc()){
			if($row["event_id"]){ 
<<<<<<< HEAD
			//skip any team that has jv in their name as it denotes a junior varsity game that did not count. 
				if(strpos($teamArray[$row["w_id"]],'-JV') === false && strpos($teamArray[$row["l_id"]],'-JV') === false) {
					if($row["w_id"] == 0 || $row["l_id"] == 0){
						// skip matches that do not count i.e having a win or loss id of 0
						continue;
					}
					$winIndex = $this->addToLeague($teamArray[$row["w_id"]], $row["w_id"]);
					$loseIndex = $this->addToLeague($teamArray[$row["l_id"]], $row["l_id"]);    
=======
				if(strpos($teamArray[$row["w_id"]],'-JV') === false && strpos($teamArray[$row["l_id"]],'-JV') === false) {
					$winIndex = $this->addToLeague($teamArray[$row["w_id"]], $row["w_id"]);
					$loseIndex = $this->addToLeague($teamArray[$row["w_id"]], $row["l_id"]);
>>>>>>> origin/vonstuben
					//create teamForView to store information about match to parse later and display
					$wteam = new teamForView($teamArray[$row["w_id"]],$row["date"],false,$row["ot"], $row["w_id"]);
					$lteam = new teamForView($teamArray[$row["l_id"]],$row["date"],true,$row["ot"], $row["l_id"]);
					
					$this->League->getTeamByIndex($winIndex)->addTeamPlayed($lteam);
					$this->League->getTeamByIndex($loseIndex)->addTeamPlayed($wteam);
					
					
					
				}
			}
		}
		$this->conn->closeConnection();//done with the DB
	}
	
	//display results in order by team 
	
	
}




?>