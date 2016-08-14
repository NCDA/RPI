<?php

class DBConn{
  private $dbHost = "dbHost";
  private $dbUsername = "dbUsername";
  private $dbPass = "dbPass";
  private $dbName = "dbName";
  private $db;
  
  function __construct(){
   // @mysql_connect("$this->db_host","$this->db_username","$this->db_pass") or die ("Whoops! Could not connect to the NCDA MySQL Database");
   // @mysql_select_db("$this->db_name") or die ("No database");
   
   //aparently in php5 you should use the mysqli class...
    //maybe move this to a connect() method that returns true or falase?
    $this->db = new mysqli($this->dbHost, $this->dbUsername, $this->dbPass, $this->dbName) or die("can't connect to DB: " . mysqli_error());

  }
  
  //TODO add the query methods
  
  public function executeSelectQuery($query){
    //put this in a try catch...
      $results = $this->db->query($query);
      //returns a result set
      return $results;
      
  }
  
  public function closeConnection(){
    mysqli_close($this->db);
  }
}


