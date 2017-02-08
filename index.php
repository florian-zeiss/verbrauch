<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        require_once 'functions_verbrauch.php';
        if(isset($_GET["Gas"]) or isset($_GET["Wasser"]) or isset($_GET["Strom"]) or isset($_GET["Solar"])){
            insertData();    
            echo '<img src="./graph.php?name=gas" alt="Gasverbrauch">';
            echo '<img src="./graph.php?name=strom" alt="Stromverbrauch">';
            echo '<img src="./graph.php?name=wasser" alt="Wasserverbrauch">';
                            
            echo showStats();
        }
        else echo formTypeStats();
        echo '<img src="./graph.php?name=gas" alt="Gasverbrauch">';
        echo '<img src="./graph.php?name=strom" alt="Stromverbrauch">';
        echo '<img src="./graph.php?name=wasser" alt="Wasserverbrauch">';
        echo showStats();
        ?>
    </body>
</html>
