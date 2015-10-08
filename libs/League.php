<?php
//League class
//contains array of teamsBase, but can be any of child classes
//TODO add destructors?


class League{
  private $name;
  //array of team pointers
  private $teams = array();
  private $numOfTeams = 0;
  
  function __construct(){
    //do nothing as of now
  }
  
  public function getNumOfTeams(){
    return $this->numOfTeams;
  }
  
  public function addTeam(&$newTeam){
    //TODO check the tyep to make sure its of type teamsBase
    array_push($this->teams, $newTeam);
    $this->numOfTeams++;
    //return the index of the tame added, 
    //its usefull i swear!
    return $this->numOfTeams-1;
  }
  
  public function getTeamByIndex($index){
  //get a team by the index
    if($this->numOfTeams != 0 && $index >-1){
      return $this->teams[$index];
    }else 
	$error =  $this->teams[0];
	$error->setName("This is not a team");
	return  $error;
  } 
  
  public function findTeamIndex($id){
    //check to see if a team is already in the league
    //by checking for id
    //if found, returns the index so we don't have to 
    //look for it again
    //if not found, returns -1
    $index = 0;
    foreach ($this->teams as $team) {
      if($team->getId() == $id){
	return $index;
      } else {
	$index++;
      }
    }
    unset($team);
    //if made it thru checking all the teams
    //and isnt in league yet
    return -1;
  }
  
  public function swap($i1, $i2){
    $temp = $this->teams[$i1];
    $this->teams[$i1] = $this->teams[$i2];
    $this->teams[$i2] = $temp;
  }
}

?>