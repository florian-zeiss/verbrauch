<?php 
$time = strtotime('2014-11-30 22:05:10');
echo $time;
echo "<br>";
$start = 615.026;
$ende = 624.663;
$startDate = "2014-11-30 22:05:10";
$endeDate = "2014-12-01 22:05:00";
$valueDiff = $ende - $start;
$dateDiff = strtotime($endeDate) - strtotime($startDate);
$dateDiff = $dateDiff / 86400;
$value = $valueDiff / $dateDiff;
echo $value;
echo "<br>";
echo $dateDiff;
echo "<br>";
echo $valueDiff;
$time = explode(" ",$startDate)[1];
$time = explode(":",$time)[0];
echo "<br>";
echo $time;
if((18 < $time) || ($time < 23)){echo "viertes Viertel<br>";}
#if(12 < $time < 18){echo "drittes Viertel<br>";}
#if(6 < $time < 12){echo "zweites Viertel<br>";}
#if(0 < $time < 6){echo "erstes Viertel<br>";}


