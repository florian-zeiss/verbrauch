<?php

function formTypeStats () #liefert das Formular als String
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

function showStats(){   //Holt die Daten
    $sql = "select * from verbrauch";
    #print $sql . "<br>";
    $answer = readFromDB($sql);
    #print_r($answer);
    #print "making table for $bkid<br>";
    $retval = makeTable($answer, "verbrauch");
    $retval = $retval . print_r(calcVerbrauch($answer,0)) . print_r(getDates($answer));
    return $retval;
    
}

function makeTable ($array,$type) {   //erzeugt aus einem Array eine Tabelle
                                      //ruft dazu getRow auf jeder Zeile auf
                                      //und unterscheidet nach Typ der Tabelle
   
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
    print "anzahl = $anzahl<br>";
    for ($x = 0; $x < $anzahl; $x++){
        $retval = $retval . getRow($array[$x],"td")."</tr>";
        if($x >= 1000) break;
    }
    $retval = $retval . "</table>";
    if($anzahl >= 1000){
    print "Anzahl: $anzahl<br>Es werden nur die ersten 1000 Treffer angezeigt!<br>";}
    return $retval;
}

function getRow ($DBRow,$typ) {     //erzeugt aus einer DBZeile eine Tabellen-
    $retval ="<tr>";                    //zeile des gewuenschten Typs
    $count = count($DBRow);
   
    #print "count: $count<br>";
    #print_r($DBRow);
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
        #print $date . "<br>";
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

function readFromDB ($sqlStmt)  //fuehrt ein select auf der DB aus und liefert 
{                              //das Ergebnis als Array
    $con = mysql_connect("localhost","odm","SecretPassw0rd!");
    if (!$con)
        {
        die('Could not connect: ' . mysql_error());
        }

    mysql_select_db("verbrauch", $con);

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
    $con = mysql_connect("localhost","odm","SecretPassw0rd!");
    if (!$con)
        {
        die('Could not connect: ' . mysql_error());
        }

    mysql_select_db("verbrauch", $con);
    if (!mysql_query($sqlStmt,$con))
  {
  die('Error: ' . mysql_error());
  }
mysql_close($con); 
}

function insertToDB ($sqlStmt)   //fuehrt ein insert auf der DB aus
{
    $con = mysql_connect("localhost","odm","SecretPassw0rd!");
    if (!$con)
        {
        die('Could not connect: ' . mysql_error());
        }

    mysql_select_db("verbrauch", $con);
    if (!mysql_query($sqlStmt,$con))
  {
  die('Error: ' . mysql_error());
  }
mysql_close($con);
}

function makeDownload($file, $dir, $type) {
    #if($type = "image/tiff") $name = "scan.tif";
    #elseif ($type = "application/pdf") $name = "scan.pdf";
    $name = $file;
    header("Content-Type: $type");

    header('Content-Disposition: attachment; filename="'. $name . '"');

    readfile($dir."/".$file);
} 
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
