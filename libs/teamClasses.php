<?php
//TODO need to check for variables not set int he getters*********

//team classes:
//base class with jus name and id, figured it be usefull down the line, not so much in calcs
//teamRecord class is the base for the various rating systems
//teamForRPI team class to be used for the RPI calculator

//base for just a team, if there was no need for records
class teamBase{
  protected $name;
  protected $id;

  function __construct($n, $i){
    //takes in a name and id
    $this->name = $n;
    $this->id = $i;
  }
  
  public function getName(){
    if($this->name != null){
      return $this->name;
    } else {
      return null;
    }
  }
   public function setName($r){
   $this->name = $r;
   }
   public function getId(){
    if($this->id != null){
      return $this->id;
    } else {
      return null;
    }
  }
}

//base for rating systems to extend off of
class teamRecord extends teamBase{
  protected $wins = 0;
  protected $losses = 0;
  protected $overtime = 0;
  protected $gamesPlayed;
  protected $rating;
  
  function __construct($n, $i){  
    parent::__construct($n, $i);
  }

  public function calcRating(){
    if($this->wins == 0 || $this->gamesPlayed == 0){
      $this->rating = 0;
    } else {
      $this->rating = $this->wins/$this->gamesPlayed;
    }
  }
  
  public function getWP(){
    if($this->wins == 0 || $this->gamesPlayed == 0){
      return 0;
    } else {
      return $this->wins/$this->gamesPlayed;
    }
  }
  
  public function addWin(){
    $this->wins++;
    $this->gamesPlayed++;
  }
  
  public function addLoss(){
    $this->losses++;
    $this->gamesPlayed++;
  }
  
  public function addOT(){
  	$this->overtime++;
  }
  
  public function getWins(){
    return $this->wins;
  }
  
  public function getLosses(){
    return $this->losses;
  }
  public function getOT(){
  	return $this->overtime;
  	
  }
  
  public function getGamesPlayed(){
    return $this->gamesPlayed;
  }

  public function getRating(){
    return $this->rating;
  }
}

//team class built for the RPI system
class teamForRPI extends teamRecord{
  // array of team pointers
  private $teamsPlayed = array(); 
  private $numOfOpponents = 0;
  
  function __construct($n, $i){    
    parent::__construct($n, $i);
  }
  
  public function calcRating(){
    //RPI = (WP*0.25) + (OWP*0.50) + (OOWP*0.25)
     //opponents winning percentage
    $owp = 0.0;
    //oppponents opponents winning percentage
    $oowp = 0.0;
    //total number of opponents opponents
    $numOfOppOpps = 0;
    
    for($i=0; $i<$this->numOfOpponents; $i++){
      $opp = $this->teamsPlayed[$i];
      $owp += $opp->getWP();
      for($j=0; $j<$opp->numOfOpponents; $j++){
      	$oppOpp = $opp->getOpponentByIndex($j);
      	$oowp += $oppOpp->getWP();
      	$numOfOppOpps++;
      } 
    }
    if($owp == 0 || $this->numOfOpponents == 0){
      $owp = 0;
    } else {
      $owp = $owp/$this->numOfOpponents;
    }
    if($oowp == 0 || $numOfOppOpps == 0){
      $oowp == 0;
    } else {
      $oowp = $oowp/$numOfOppOpps;
    }
    $this->rating = ($this->getWP() *0.25) +
                    		    ($owp * 0.50) +
                    		    ($oowp *0.25);
  }
  
  public function getOpponentByIndex($index){
    if($this->numOfOpponents != 0){
      return $this->teamsPlayed[$index];
    }
  }	
  
  public function addTeamPlayed(&$team){
    //keep track of teams played by id
    array_push($this->teamsPlayed, $team);
    $this->numOfOpponents++;
  }
  
  public function getNumOfOpponents(){
    return $this->numOfOpponents;
  }
}

class teamForNHL extends teamRecord{
  //total points :: wins = 2pts, Overtime loss = 1pt
  private $points;

  function __construct($n, $i){    
    parent::__construct($n, $i);
    $this->points = 0;
  }

  public function addPoints($pts){
    $this->points += $pts;
  }

  public function getRating(){
    return $this->points;
  }
}

class teamForGonzalez extends teamRecord{

	function __construct($n, $i){
		parent::__construct($n, $i);
	//rating exchange, every teams starts with 40 points before exchanging ratings in match.
	$this->rating = 40;
}

  public function exchangeWin($rate){
   $this->rating += $rate;
}

  public function exchangeLoss($rate){
   $this->rating -= $rate;
}

}
class teamForPerrone extends teamRecord{
	private  $wlp;
	
	function __construct($n, $i){
	parent::__construct($n, $i);
		
	$this->wlP = 0;
	
}
	public function getWLP(){
	$this->wlp = ((($this->getWins() + $this->getOT()) - ($this->getLosses()))/ $this->getGamesPlayed());
	return $this->wlp;
}
	public function addPoints($pts){
	$this->rating += $pts;
	
	}
}

class teamForView{

	protected $teamName;
	protected $date;
	protected $win;
	protected $ot;
	protected $id;
	
function __construct($pTeam, $pDate, $pWin, $pOt, $pId){
		$this->teamName = $pTeam;
		$this->date = $pDate;
		$this->win = $pWin;
		$this->ot = $pOt;
		$this->id = $pId;
		
	}
	

	
	public function getName(){
		return $this->teamName;
	}
	
	public function getDate(){
		return $this->date;
	}
	
	public function getStatus(){
		return $this->win;
	}
	
	public function getOTStatus(){
		return  $this->ot;
	}
	
	public function getId(){
		return $this->id;
	}
}



