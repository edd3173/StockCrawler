<html>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>MarketWatch - Trending Ticker</title>
    <link rel="stylesheet" href="../main.css">
</head>


<body>

<?php

include('simplehtmldom_1_9_1/simple_html_dom.php');

// Create DOM from URL or file

//$url = $_POST["url"];
//echo $url."<br>" ;

//$tag = $_POST["tag"];
//echo $tag."<br>" ;


//$url="https://www.marketwatch.com/tools/screener/market?exchange=nyse&subreport=largestpercentgainreport";
$url="https://www.marketwatch.com/tools/screener/market?exchange=nyse&subreport=mostactive";
$html  =  file_get_html($url);

//echo $html;
$Plain = "";

$Count=0;

foreach($html->find('tr class="table__row"') as $element){
    //echo $element . "<br>";
    //echo $element->plaintext. "<br>";
    if($Count==0){
        setField();
    }
    else{
        getInfo($element->plaintext);
    }
    $Count++;
}

function setField(){
    // we already know teh fields:
    // Symbol, CompanyName, Last Chg, %Chg, Vol, $Traded
    // so we can just set DB here.

    
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
        echo"MySQL Server Connect Failed!" . "<br>";
    }
    //Creating Table
    // Symbol, CompanyName, Last, Chg, %Chg, Vol, $Traded
    $sql = "CREATE TABLE MW_Trend(
            TICKER_NAME VARCHAR(30) PRIMARY KEY,
            COMPANY_NAME VARCHAR(100) NOT NULL,
            LAST_PRICE VARCHAR(30) NOT NULL,
            TICKER_CHANGE VARCHAR(20) NOT NULL,
            TICKER_CHANGE_PER FLOAT(10) NOT NULL,
            VOLUME VARCHAR(20) NOT NULL,
            TRADED VARCHAR(20) NOT NULL
            )";

    if( $connect -> query($sql) === TRUE){
            //echo "Table Created Successfully". "<br>";
    }
    else{
            //echo "Error creating table:" . $connect->error. "<br>";
    }
    $connect->close();
}


function getInfo($text){
    $Parsed = preg_replace('/\s+/', ' ', $text);
    //echo "|".$Parsed."|\n\n";
    // we need to get 7 info.
    // Symbol, Name, Last, Chg, %Chg, Vol, $Traded
    $a=1; $b=0; $c=0; $d=0; $e=0; $f=0; $g=0; $h=0;
    
    // walk through $text
    $Pos=1;
    $Length=strlen($Parsed);
    //echo "Length : ". $Length. "<br>";

    
    while($Parsed[$Pos]!=' '){
        $Pos++;
    } // try get Symbol
    
    $b=$Pos-1;
    //echo "Pos : ". $Pos. "<br>";
    //echo "b : ". $b. "<br>";
    
    // Symbol : from $a to $b
    $SymLen = $b-$a+1;
    $Symbol=substr($Parsed,$a,$SymLen);
    //echo "Symbol : ". $Symbol. "<br>";

    $Pos++; // start of Company name

    while($Parsed[$Pos]!='$'){
        $Pos++;
    }
    $c=$Pos-1;
    //echo "Pos : ". $Pos. "<br>";
    //echo "c : ". $c. "<br>";
    $b+=2;
    //Name : from $b to $c
    $NameLen = $c-$b+1;
    $Name=substr($Parsed,$b,$NameLen);
    //echo "Name : ". $Name. "<br>";

    $Pos++; // start of Last Price
    while($Parsed[$Pos]!=' '){
        $Pos++;
    }
    $d=$Pos-1;
    //echo "Pos : ". $Pos. "<br>";
    //echo "d : ". $d. "<br>";

    //Last_Price
    $PriceLen = $d-$c+1;
    $Price=substr($Parsed,$c,$PriceLen);
    //echo "Price : ". $Price. "<br>";


    $Pos++; // start of ticker change
    while($Parsed[$Pos]!=' '){
        $Pos++;
    }
    $e=$Pos-1;
    //echo "Pos : ". $Pos. "<br>";
    //echo "d : ". $d. "<br>";
    $d+=2;
    //Chg
    $ChgLen = $e-$d+1;
    $Chg=substr($Parsed,$d,$ChgLen);
    //echo "Chg : ". $Chg. "<br>";

    $Pos++; // start of change per
    while($Parsed[$Pos]!=' '){
        $Pos++;
    }
    $f=$Pos-1;
    //echo "Pos : ". $Pos. "<br>";
    //echo "f : ". $d. "<br>";
    $e+=2;
    //Chg %
    $ChgPerLen = $f-$e+1;
    $ChgPer=substr($Parsed,$e,$ChgPerLen);
    //echo "ChgPer : ". $ChgPer. "<br>";

    $Pos++; // start of Vol
    while($Parsed[$Pos]!=' '){
        $Pos++;
    }
    $g=$Pos-1;
    //echo "Pos : ". $Pos. "<br>";
    //echo "f : ". $d. "<br>";
    $f+=2;
    //Chg %
    $VolLen = $g-$f+1;
    $Vol=substr($Parsed,$f,$VolLen);
    //echo "Volume : ". $Vol. "<br>";

    $Pos++; // start of Vol
    while($Parsed[$Pos]!=' '){
        $Pos++;
    }
    $h=$Pos-1;
    //echo "Pos : ". $Pos. "<br>";
    //echo "f : ". $d. "<br>";
    $g+=2;
    //Chg %
    $TradedLen = $h-$g+1;
    $Traded=substr($Parsed,$g,$TradedLen);
    //echo "Traded : ". $Traded. "<br>";


    // Now Insert Infos in DB
    // $Name, $Price, $Chg, $ChgPer, $Volume, $Traded

    //TODO : Need to deal with Numbers:
    // $Price, $Chg, $ChgPer, $Volume, $Traded
    // But let's stick to $ChgPer

    $NUM_ChgPer = floatval($ChgPer);



    $hostname = "localhost";
    $username = "cse20161614";
    $password = "stargazer";
    $dbname = "db_cse20161614";

    $connect = new mysqli($hostname, $username, $password, $dbname)
    or die("DB Connection Failed.");

    $sql = "INSERT INTO MW_Trend (TICKER_NAME,COMPANY_NAME,LAST_PRICE,TICKER_CHANGE,TICKER_CHANGE_PER,VOLUME,TRADED)
            VALUES ('$Symbol', '$Name', '$Price', '$Chg', ' $NUM_ChgPer', '$Vol', '$Traded')";
    
    if($connect->query($sql)===TRUE){
            //echo "Insertion Done". "<br>";
    }
    else{
            //echo ("Error: " . $sql . "<br>" . $connect->error);
    }

    //mysql -u cse20151537 -p
}


//echo $Plain;
//print_r($Plain);

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
    echo"MySQL Server Connect Failed!" . "<br>";
}

//Printing Datas
echo "<h2><font color=WHITE> Trending Tickers in MarketWatch </font></h2>";

$sql = "SELECT * FROM MW_Trend ORDER BY TICKER_CHANGE_PER DESC;";

$result = $connect->query($sql);

if ($result->num_rows > 0) {
    // output data of each row
    
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
  } else {
    echo "0 results";
  }
  $connect->close();
?>

</body>
</html>