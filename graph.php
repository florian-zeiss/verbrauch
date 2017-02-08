<?php
#includes zum malen
include ("./jpgraph/src/jpgraph.php");
include ("./jpgraph/src/jpgraph_line.php");
require_once 'functions_verbrauch.php';
#Was wollen wir malen
$what = $_GET[name];;


$db = mysql_connect("localhost","odm","SecretPassw0rd!") or die ("failed to connect");
mysql_select_db("verbrauch");
#Arrayindex festlegen
switch ($what) {
  case "gas":
    $row = 0;
    break;
  case "strom":
    $row = 1;
    break;
  case "wasser":
    $row = 2;
    break;
  default:
    break;
 }
                                                                        
$sql = "select * from verbrauch";
$answer = readFromDB($sql);
$datay = calcVerbrauch($answer,$row);
#$datay = array(615.026,617.729,624.663,634.23,642,652,663);
print_r($datay);
$dates = getDates($answer);

// Setup the graph
//$graph = new Graph(300,250);
//$graph->SetScale("textlin");
//
//$theme_class=new UniversalTheme;
//
//$graph->SetTheme($theme_class);
//$graph->img->SetAntiAliasing(false);
//$graph->title->Set('Filled Y-grid');
//$graph->SetBox(false);
//
//$graph->img->SetAntiAliasing();
//$graph->yaxis->HideZeroLabel();
//$graph->yaxis->HideLine(false);
//$graph->yaxis->HideTicks(false,false);
//$graph->xgrid->Show();
//$graph->xgrid->SetLineStyle("solid");
//$graph->xaxis->SetTickLabels(array('A','B','C','D'));
//$graph->xgrid->SetColor('#E3E3E3');
//$p1 = new LinePlot($datay);
//$graph->Add($p1);
//$p1->SetColor("#6495ED");
//$p1->SetLegend('Line 1');
//
//
//$graph->legend->SetFrameWeight(1);

// Output line
#$graph->Stroke();


// Setup the graph
$graph = new Graph(800,250);
$graph->SetScale("intlin",0,$aYMax=5);
$theme_class=new UniversalTheme;
$graph->SetTheme($theme_class);

$graph->SetBox(false);

$graph->title->Set($what);
$graph->ygrid->Show(true);
$graph->xgrid->Show(false);
$graph->yaxis->HideZeroLabel();
$graph->ygrid->SetFill(true,'#FFFFFF@0.5','#FFFFFF@0.5');
$graph->SetBackgroundGradient('blue', '#55eeff', GRAD_HOR, BGRAD_PLOT);
#$graph->xaxis->SetTickLabels(array('2014-12-01','2014-12-02','2014-12-10','2014-12-11','2014-12-13','2014-12-15','2014-12-16'));
$graph->xaxis->SetTickLabels($dates);

// Create the line
$p1 = new LinePlot($datay);
$graph->Add($p1);

$p1->SetFillGradient('yellow','red');
$p1->SetStepStyle();
$p1->SetColor('#808000');

// Output line
$graph->Stroke();

?>
