<?php 
    header('Content-type: application/json; Access-Control-Allow-Origin: *');
    setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
    date_default_timezone_set('America/Sao_Paulo');
    ini_set('display_errors',1); ini_set('display_startup_erros',1); 
    error_reporting(0); 
    ini_set('memory_limit', '-1'); date_default_timezone_set('America/Sao_Paulo');


    include_once('./classes/Database.class.php');
    include_once('./classes/Functions.class.php');
    
    $db = new Database();
    $functions = new Functions();

    $like = "";

    if (@$_GET["nome"]) {
        $like = $_GET["nome"];
    } 

    $midle = "";

    if (@$_GET["status"]) {
        $like = "representante = ? AND";
    } 

    $query = "SELECT * FROM trepresentante WHERE ".$midle." representante LIKE '%".$like."%' LIMIT 1,10";

    // echo $query;
    $db->query = $query;
    $content = array();

    if (@$_GET["status"]) {
        $content[] = @$_GET["status"];
    } else {
        $content[] = "NOT NULL";
    }

    $db->content = $content;
    $vendedores = ($db->select());

    echo json_encode(array( "vendedores" => (array)$vendedores ));
?>