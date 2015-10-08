RPI
===

Ratings Percentage Index Algorithm for College Dodgeball

2014-01-19
===

***See Change-log.txt and how-to-add-calculators.txt!!

NOTES:
  ***To change the current season go to libs/RPI.php and change the $startYear
    to the begging year of the current season. Right now the current season is 
    set to 2012/2013.
  
  I wasn't sure when to start and end the season, so i picked 08-01, and 07-01.
  To change the start and end date go to libs/RPI.php and in the populateLeague()
    function you will see $startDate and $endDate varables to change.
    
  I left the RPIDisplay.php page really bare, I wasn't sure if you wanted to leave it
  as a stand alone page, or integrate it into another page. I tried to make it easy to 
  be able to integrate it into another. Its just a few lines of PHP to calculate, 
  look at the top few lines RPIDisplay.php to see how to use it. And the rest of 
  my app is in the RPI_holder div. Plus the css file.
  
  I know this isn't perfect, any comments or criticism feel free to facebook me or email me lieblic2@msu.edu .
  
Classes:
  DBConn:
    Simple DB access class to make it easier for other classes.
    I plan on adding more to this in the future.
    **Right now I have the info for my local SQL server, so
      when i give you my files ill leave them blank and you will to fill in 
      the info for the production server.
  TeamClasses:
    All the team classes are in the libs/teamClasses.php file.
    I tried to write them so you can extend future rating systems off the 
      teamRecord class, like i did for the teamForRPI class.
    All of the classes are pretty barebone with just setters and getters,
    feel free to add to them.

  League:
    Basic class to mostly just hold an array of teamBase objects with getters 
      and setters for class variables.
      
   RPI:
    handles RPI stuff...
    
    I commented each class and its methods, see those for more details.
    I was trying to keep this short, cause who likes readme files :)