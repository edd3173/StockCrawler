<!DOCTYPE html>

<html>
  <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Welcome To K2J.COM</title>
    
    <link rel="stylesheet" href="../main.css">
    
  </head>
  <body>
    <script>
      function rain(){
          let amount = 10;
          let body=document.querySelector('body');
          let i = 0;

          while(i < amount){
              let drop = document.createElement('i_Up');
              let size = Math.random() * 5;
              let posX = Math.floor(Math.random() * window.innerWidth);
              let delay = Math.random() * -20;
              let duration = Math.random() * 5;

              drop.style.width = 0.2 + size + 'px';
              drop.style.left = posX + 'px';
              drop.style.animationDelay = delay + 's';
              drop.style.animationDuration = 1 + duration + 's';
              body.appendChild(drop);
              i++;
          }
          i=0;
          while(i < amount){
              let drop = document.createElement('i_Down');
              let size = Math.random() * 5;
              let posX = Math.floor(Math.random() * window.innerWidth);
              let delay = Math.random() * -20;
              let duration = Math.random() * 5;

              drop.style.width = 0.2 + size + 'px';
              drop.style.left = posX + 'px';
              drop.style.animationDelay = delay + 's';
              drop.style.animationDuration = 1 + duration + 's';
              body.appendChild(drop);
              i++;
          }
      }
  rain();
  </script>
  </body>
</html>

<?php
    
//Let's Do some Pre-Processes :
// Get Infos by $[Post]
// And we make queries
// And we connect with DBs
// A LOT of things to do...

$DB_CHOICE = $_POST["DB_Choice"];
$TICKER_CHOICE = $_POST["Ticker_Choice"];
$PERCENT_RANGE = $_POST["Percent_Range"];


//echo "DB-Choice : ". $DB_CHOICE. "<br>";
//echo "Ticker-Choice : ". $TICKER_CHOICE. "<br>";
//echo "Percent-Range : ". $PERCENT_RANGE. "<br>";


$DB_NAME="";
if($DB_CHOICE=="YF"){
  if($TICKER_CHOICE=="TT")
    $DB_NAME="YF_Trend";
  else if($TICKER_CHOICE=="MG")
    $DB_NAME="YF_Gain";
}
else if($DB_CHOICE=="MW"){
  if($TICKER_CHOICE=="TT")
    $DB_NAME="MW_Trend";
  else if($TICKER_CHOICE=="MG")
    $DB_NAME="MW_Gain";
}

$RANGE_MAX; $RANGE_MIN;
if($PERCENT_RANGE=="MINUSTEN_TO_MINUSFIVE"){
  $RANGE_MIN=-10;
  $RANGE_MAX=-5;
}
if($PERCENT_RANGE=="MINUSFIVE_TO_ZERO"){
  $RANGE_MIN=-5;
  $RANGE_MAX=0;
}
if($PERCENT_RANGE=="ZERO_TO_FIVE"){
  $RANGE_MIN=0;
  $RANGE_MAX=5;
}
else if($PERCENT_RANGE=="FIVE_TO_TEN"){
  $RANGE_MIN=5;
  $RANGE_MAX=10;
}
else if($PERCENT_RANGE=="TEN_TO_FIFTEEN"){
  $RANGE_MIN=10;
  $RANGE_MAX=15;
}
else if($PERCENT_RANGE=="FIFTEEN_TO_TWENTY"){
  $RANGE_MIN=15;
  $RANGE_MAX=20;
}
else if($PERCENT_RANGE=="TWENTY_TO_TWENTYFIVE"){
  $RANGE_MIN=20;
  $RANGE_MAX=25;
}
else if($PERCENT_RANGE=="OVER_TWENTY"){
  $RANGE_MIN=25;
  $RANGE_MAX=100;
}


// Let's Make Connection With DB.

error_reporting(E_ALL);
ini_set("display_errors", 1);

//echo ("MySQL - PHP Connect Test <br/>");
$hostname = "localhost";
$username = "cse20161614";
$password = "stargazer";
$dbname = "db_cse20161614";

$connect = new mysqli($hostname, $username, $password, $dbname)
        or die("DB Connection Failed.");
//$result = mysql_select_db($dbname, $connect);

if($connect){
    echo "MySQL Server Connect Success!". "<br>";
}
else{
    echo "MySQL Server Connect Failed!" . "<br>";
}

//Printing Datas
echo "<h2><font color=WHITE> Here You Are! </font></h2>";

$sql = "SELECT * FROM $DB_NAME
        WHERE TICKER_CHANGE_PER BETWEEN $RANGE_MIN AND $RANGE_MAX
        ORDER BY TICKER_CHANGE_PER DESC;";

$result = $connect->query($sql);

if ($result->num_rows > 0) {
  // output data of each row

  if($DB_CHOICE=="MW"){

    //  MW
    echo "<table border='2'><tr>
    <th> <font color=WHITE> TICKER_NAME </font> </th>
    <th> <font color=WHITE> COMPANY_NAME </font> </th>
    <th> <font color=WHITE> LAST_PRICE </font> </th>
    <th> <font color=WHITE> TICKER_CHANGE </font> </th>
    <th> <font color=WHITE> TICKER_CHANGE_PER </font> </th>
    <th> <font color=WHITE> VOLUME </font> </th>
    <th> <font color=WHITE> TRADED </font> </th></tr>";
    
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>" . 
        "<font color=WHITE>". $row["TICKER_NAME"]."</font>". "</td><td>" . 
        "<font color=WHITE>". $row["COMPANY_NAME"]."</font>". "</td><td>" . 
        "<font color=WHITE>". $row["LAST_PRICE"]."</font>". "</td><td>". 
        "<font color=WHITE>". $row["TICKER_CHANGE"]."</font>".  "</td><td>".
        "<font color=WHITE>". $row["TICKER_CHANGE_PER"]."%</font>".  "</td><td>".
        "<font color=WHITE>". $row["VOLUME"]."</font>". "</td><td>".
        "<font color=WHITE>". $row["TRADED"]. "</font>";
    }
  }

  else if ($DB_CHOICE=="YF"){
    echo "<table border='2'><tr>
    <th> <font color=WHITE> TICKER_NAME </font></th>
    <th> <font color=WHITE> LAST_PRICE </font></th>
    <th> <font color=WHITE> TICKER_CHANGE </font></th>
    <th> <font color=WHITE> TICKER_CHANGE_PER </font></th>
    <th> <font color=WHITE> VOLUME </font></th>
    <th> <font color=WHITE> MARKET_CAP </font></th></tr>";
    
    while($row = $result->fetch_assoc()) {
        echo "<tr><td>" . 
        "<font color=WHITE>". $row["TICKER_NAME"]."</font>". "</td><td>" . 
        "<font color=WHITE>$". $row["LAST_PRICE"]."</font>". "</td><td>" . 
        "<font color=WHITE>". $row["TICKER_CHANGE"]."</font>".  "</td><td>".
        "<font color=WHITE>". $row["TICKER_CHANGE_PER"]."%</font>".  "</td><td>".
        "<font color=WHITE>". $row["VOLUME"]."</font>".  "</td><td>".
        "<font color=WHITE>". $row["MARKET_CAP"]. "</font>";
    }
  }
  
} else {
  echo "<font color=WHITE> No Results Found! Please Change Your Condition</font>";
}

$connect->close();

?>