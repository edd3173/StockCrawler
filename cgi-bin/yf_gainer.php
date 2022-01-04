
<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Yahoo Finance - Most Gainer</title>
    <link rel="stylesheet" href="../main.css">
</head>


<body>
<?php


//echo "<h2>Initializing DB:</h2>";
// intializing DB

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
 //echo "MySQL Server Connect Success!". "<br>";
}
else{
 //echo"MySQL Server Connect Failed!" . "<br>";
}
//Creating Table

$sql = "CREATE TABLE YF_Gain(
        TICKER_NAME VARCHAR(20) PRIMARY KEY,
        LAST_PRICE VARCHAR(30) NOT NULL,
        TICKER_CHANGE VARCHAR(30) NOT NULL,
        TICKER_CHANGE_PER FLOAT(10) NOT NULL,
        VOLUME VARCHAR(20) NOT NULL,
        MARKET_CAP VARCHAR(20) NOT NULL
        )";

if( $connect -> query($sql) === TRUE){
        //echo "Table Created Successfully". "<br>";
}
else{
        //echo "Error creating table:" . $connect->error. "<br>";
}
$connect->close();


include('simplehtmldom_1_9_1/simple_html_dom.php');

// Create DOM from URL or file

//$url = $_POST["url"];
//echo $url."<br>" ;

//$tag = $_POST["tag"];
//echo $tag."<br>" ;
$tag='tbody data-reactid="40"';

//$url="https://finance.yahoo.com/trending-tickers";
$url="https://finance.yahoo.com/gainers";
$html  =  file_get_html($url);
$element= $html->find($tag);


$RealElement=$element[0];

// TODO : Need to get Symbol Datas

$NameDatas=array();
$NumberDatas=array();
$ResultDatas=array();

$NameCount=0;
$NumberCount=0;

// TODO : Get Number Datas
foreach($RealElement->find("fin-streamer") as $SYM){
        //echo $SYM . "<br>". "<br>". "<br>";
        //echo $SYM->innertext . "<br>";
        getName($SYM,$NameDatas);
        getNumbers($SYM->plaintext,$NumberDatas);
        //$NumberDatas .= $SYM->plaintext;
        //$NumberDatas .="\n";
}
//echo $NumberDatas;

//showArray($NameDatas);
//echo "<br>";
//echo "<br>";
//showArray($NumberDatas);
//echo "<br>";
//echo "<br>";
//echo "NameCount : " . count($NameDatas). "<br>";
//echo "NumCount : " . count($NumberDatas). "<br>";

// TODO : Cook them to Associative arrays

cookArrays($NameDatas,$NumberDatas,$ResultDatas);



function getName($buf, &$arr){
        $str="";
        $str=$buf->save();
        //echo $str;
        //echo "Trying to enter here! <br>";
        $StartPos = strpos($str, "data-symbol");
        $StartPos+=10; $StartPos+=3;
        //echo "<br>";
        //echo "StartPos : ". $StartPos. "<br>";
        //echo "StartChar : ". $str[$StartPos]. "<br>";
        $EndPos=$StartPos;
        while($str[$EndPos]!=' ')
                $EndPos++;
        //echo "Endpos : ". $EndPos. "<br>";;
        $Length = $EndPos - $StartPos - 1;
        //echo "Length : ". $Length. "<br>";;
        $Chunk = substr($str,$StartPos,$Length);
        //echo "Ticker : ". $Chunk. "<br>";
        //echo "Typeof(Ticker) : ". gettype($Chunk). "<br>";
        //echo "Typeof(Array) : ". gettype($arr). "<br>";
        $val=array_push($arr,$Chunk);
        //echo "result of push : ". $val. "<br>";
        //echo "Pushed Chunk in namedata". "<br>";
        
}

function showArray($arr){
        $i=0;
        //echo count($arr);
        for($i=0;$i<count($arr);$i++){
                echo $arr[$i]. "<br>";
        }
}

function getNumbers($buf,&$arr){
        //echo $buf;
        array_push($arr,$buf);
        
}

function cookArrays(&$Names, &$Numbers, &$Merged){



        
        //We insert them into DB
        $hostname = "localhost";
        $username = "cse20161614";
        $password = "stargazer";
        $dbname = "db_cse20161614";

        $connect = new mysqli($hostname, $username, $password, $dbname)
        or die("DB Connection Failed.");
                


        //TODO : merge array datas
        // 1. we count how many elements are same in NameArray
        // 2. with that element, we create associative array
        // 3. for that count, we assign values of NumbersArray

        // Walk and Process once? or with middle-phase?
        // TempArr['string']= N; like this.
        // or [pos, dupNum]; like this

        // Thanks to this, we got TempArr['string']= N;
        $ACV = array_count_values($Names);
        //print_r($ACV);
        //echo "<br>";

        $POS=0;

        foreach($ACV as $Key => $Value){
                // $Key = "tickername"
                // $Value = number

                //echo "Key: ". $Key. "<br>";
                //echo "Value: ". $Value. "<br>";

                $TickerArray=array();
                for($i=0;$i<$Value;$i++){
                        array_push($TickerArray,$Numbers[$i+$POS]);
                }
                $POS+=$Value;
                //echo "Pos: ". $POS. "<br>";


                //Ticker's Name
                //echo $Key. "'s Number Data Array: ". "<br>";
                
                //Ticker's Number Value
                //showArray($TickerArray);

                // Inserting Datas
                //echo "<h2>Inserting Datas...</h2>";

                //Datas need to be inserted
                $Ticker_Name=$Key;
                $Last_Price=$TickerArray[0];
                $Ticker_Change=$TickerArray[1];
                $Ticker_Change_Per=$TickerArray[2];
                $Volume=$TickerArray[3];
                if($Value ==4)
                        $Market_Cap="N/A";
                else
                        $Market_Cap=$TickerArray[4];
                
                //showArray($TickerArray);
                
                //TODO : Need to deal with TICKER_PER!
                // It's Currently Char, so convert with Number
                
                $NUM_Ticker_Change_Per = floatval($Ticker_Change_Per);

                $sql = "INSERT INTO YF_Gain (TICKER_NAME,LAST_PRICE,TICKER_CHANGE,TICKER_CHANGE_PER,VOLUME,MARKET_CAP)
                        VALUES ('$Ticker_Name', '$Last_Price', '$Ticker_Change', '$NUM_Ticker_Change_Per', '$Volume', '$Market_Cap')";
                
                if($connect->query($sql)===TRUE){
                        //echo "Insertion Done". "<br>";
                }
                else{
                        //echo ("Error: " . $sql . "<br>" . $connect->error);
                }
                
        }

}
//mysql -u cse20151537 -p

?>



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



<?php

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
    //echo "MySQL Server Connect Success!". "<br>";
}
else{
    //echo"MySQL Server Connect Failed!" . "<br>";
}

//Printing Datas
echo "<h2><font color=WHITE> Most Gained in Yahoo </font></h2>";

$sql = "SELECT * FROM YF_Gain ORDER BY TICKER_CHANGE_PER DESC;";

$result = $connect->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
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
        "<font color=WHITE>". $row["TICKER_CHANGE_PER"]."% </font>".  "</td><td>".
        "<font color=WHITE>". $row["VOLUME"]."</font>".  "</td><td>".
        "<font color=WHITE>". $row["MARKET_CAP"]. "</font>";
    }
  } else {
    echo "0 results";
  }
  $connect->close();
?>





</body>
</html>

