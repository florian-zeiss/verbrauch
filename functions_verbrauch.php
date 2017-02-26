<?php

#liefert das Formular als String zur Eingabe neuer Werte.
function formTypeStats () 
{$retval = "<p> <h2> Trage Verbauch ein oder gehe direkt zu den Statistiken...</h2></p>";
 $retval = $retval . "<form action=\"index.php\" method=\"get\" >";
 $retval = $retval . "<p>Gas:";
 $retval = $retval . "<input type=\"text\" name=\"Gas\" />";
 $retval = $retval . "</p>";
 $retval = $retval . "<p>Strom:";
 $retval = $retval . "<input type=\"text\" name=\"Strom\" />";
 $retval = $retval . "</p>";
 $retval = $retval . "<p>Wasser:";
 $retval = $retval . "<input type=\"text\" name=\"Wasser\" />";
 $retval = $retval . "</p>";
 $retval = $retval . "<p>Solar:";
 $retval = $retval . "<input type=\"text\" name=\"Solar\" />";
 $retval = $retval . "</p>";
 $retval = $retval . "<p>Datum:";
 $timestamp = date("Y-m-d H:i:s");
 $retval = $retval . "<input type=\"text\" name=\"timestamp\" value=\"$timestamp\" />";
 $retval = $retval . "</p>";
 $retval = $retval . "<p><input type=\"submit\" value=\"absenden\" />";
 $retval = $retval . "</p> </form>";
 return $retval;
    
}
//Bereitet die uebergebenen Werte auf und loest einen Insert auf der Datenbank aus
function insertData(){
    $gas = $_GET{"Gas"};
    $wasser = $_GET{"Wasser"};
    $strom = $_GET{"Strom"};
    $solar = $_GET{"Solar"};
    $timestamp = $_GET{"timestamp"};
    $sql = "insert into verbrauch (`gas`, `wasser`, `strom`, `solar`, `datum`) values ('" . $gas . "','" . $wasser . "','" . $strom . "','" . $solar . "'," . '"' . $timestamp . '")';
    print $sql . "<br>";
    insertToDB($sql);
    }
    
    
//Holt die Daten aus der Datenbank und bereitet sie auf zur Ausgabe
function showStats(){   //Holt die Daten
    $sql = "select * from verbrauch";
    $answer = readFromDB($sql);
    $retval = makeTable($answer, "verbrauch");
    //$retval = $retval . print_r(calcVerbrauch($answer,0)) . print_r(getDates($answer));
    return $retval;
    
}
//erzeugt aus einem Array eine Tabelle
//ruft dazu getRow auf jeder Zeile auf
//und unterscheidet nach Typ der Tabelle
function makeTable ($array,$type) {   
   
    if ($type == "contracts")
    {
        $retval = '<table border="1">'; 
        $retval = $retval . "<tr><th>Name</th><th>Vertragsnummer</th><th>Beginn</th><th>Ende</th><th>K&uuml;ndigungsfrist</th><th>Monatliche Kosten</th>></tr>";         //Hier kommt der tableheader
    }
    elseif($type == "books"){
        $retval = '<table border="1">'; 
        $retval = $retval . "<tr><th>Titel</th><th>Author</th><th>Buch</th>></tr>";
    }
    elseif($type == "verbrauch"){
         $retval = '<table border="1">';
         $retval = $retval . "<tr><th>Gas</th><th>Strom</th><th>Wasser</th><th>Solar</th><th>Datum</th></tr>";
    }   
    $anzahl = count($array);
    //print "anzahl = $anzahl<br>";
    for ($x = 0; $x < $anzahl; $x++){
        $retval = $retval . getRow($array[$x],"td")."</tr>";
        if($x >= 1000) break;
    }
    $retval = $retval . "</table>";
    if($anzahl >= 1000){
    print "Anzahl: $anzahl<br>Es werden nur die ersten 1000 Treffer angezeigt!<br>";}
    return $retval;
}


//erzeugt aus einer DBZeile eine Tabellenzeile des gewuenschten Typs (th oder td)
function getRow ($DBRow,$typ) {     
    $retval ="<tr>";                    
    $count = count($DBRow);
    for ($i = 0; $i < $count/2 ; $i++){
      
        $retval = $retval . "<" . $typ . ">" . htmlentities($DBRow[$i]) . "</" . $typ . ">";
    }
    $retval = $retval . "</tr>";
    return $retval;
}

function calcVerbrauch ($answer, $row) {
    
    $count = count($answer);
    $basic = $answer[0][$row];
    $basicDate = $answer[0][4];
    $retval = array();
    for ($i = 1;$i < $count/2 ; $i++){
        $value = $answer[$i][$row];
        $date = $answer[$i][4];
        $valueDiff = $value - $basic;
        $dateDiff = strtotime($date) - strtotime($basicDate);
        $result = ($valueDiff / $dateDiff)*21600;
        array_push($retval, $result);
        }
    return $retval;
    $count = count($retval);
    echo "calcVerbrauch called, $count values returned<br>" >> verbrauch.log;
                                                         
}                                                        

function getDates ($answer) {
$basicDate = $answer[0][4];
$count = count($answer);
$retval = array(explode(" ",  $basicDate)[0]);
for ($i = 1;$i < $count ; $i++){
  $date = $answer[$i][4];
  $date = explode(" ", $date)[0];
  array_push($retval , $date);
}
return $retval;

}

function getMedia($cname, $CDir){
    $retval = array();
    #print $contractsDir . $cname;
    #print $CDir;
    if ($handle = opendir($CDir)) {
    while (false !== ($file = readdir($handle))) {
        if ($file != "." && $file != "..") {
            $retval[] = $file;
        }
    }
    closedir($handle);
}
   # $retval = $MList;
    return $retval;
}

//Erstellt eine Verbindung zur Datenbank verbrauch
function getDBCon ()
{
    //hier koennen alle Datenbankverbindungsdaten eingetragen werden
    $host = "loccalhost";
    $username = "username";
    $password = "password";
    $database = "verbrauch";
    $con = mysql_connect($host, $username, $password);
    if (!$con)
        {
        die('Could not connect: ' . mysql_error());
        }

    mysql_select_db($database, $con);
    return $con;
}

function readFromDB ($sqlStmt)  //fuehrt ein select auf der DB aus und liefert 
{                              //das Ergebnis als Array
    $con = getDBCon();

    $result = mysql_query($sqlStmt);
    $i = 0;
    $array[0] = "";
    while($row = mysql_fetch_array($result))
    {
        $array[$i] = $row;  
        $i = $i + 1;
    }
    mysql_close($con);
    #print_r($array);    
    return $array;
    
    
}

function updateDB ($sqlStmt)   //fuehrt ein update auf der DB aus
{
    $con = getDBCon();
    if (!mysql_query($sqlStmt,$con))
  {
  die('Error: ' . mysql_error());
  }
mysql_close($con); 
}

function insertToDB ($sqlStmt)   //fuehrt ein insert auf der DB aus
{
    $con = getDBCon();
    if (!mysql_query($sqlStmt,$con))
  {
  die('Error: ' . mysql_error());
  }
mysql_close($con);
}

//function makeDownload($file, $dir, $type) {
    #if($type = "image/tiff") $name = "scan.tif";
    #elseif ($type = "application/pdf") $name = "scan.pdf";
 //   $name = $file;
 //   header("Content-Type: $type");

 //   header('Content-Disposition: attachment; filename="'. $name . '"');

 //   readfile($dir."/".$file);
//} 
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
