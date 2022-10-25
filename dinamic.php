<?php 
    header('Content-type: application/json; Access-Control-Allow-Origin: *');
    setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
    date_default_timezone_set('America/Sao_Paulo');
    ini_set('display_errors',1); ini_set('display_startup_erros',1); 
    error_reporting(0); 
    ini_set('memory_limit', '-1'); date_default_timezone_set('America/Sao_Paulo');

    $data = file_get_contents ("config.json");
    $json = json_decode($data, true);

    $token = $json["token"];

    if(!isset($_GET['token']) || ($token != $_GET['token'])){
		echo 'Acesso não autorizado';
		exit;
	}

    include_once('./classes/Database.class.php');

    $db = new Database();

    $q_origem = "SELECT DISTINCT origem FROM consultas ORDER BY origem ASC";
    $db->query = $q_origem;
    $db->content = NULL;
    $rows_origem = ($db->select());
    $rows["origem"] = (array)$rows_origem;

    $q_uf = "SELECT DISTINCT uf FROM consultas ORDER BY uf ASC";
    $db->query = $q_uf;
    $db->content = NULL;
    $rows_uf = ($db->select());
    $rows["uf"] = (array)$rows_uf;

    $q_consulta = "SELECT DISTINCT tipoConsulta, nomeConsulta FROM painel_controle ORDER BY nomeConsulta ASC";
    $db->query = $q_consulta;
    $db->content = NULL;
    $rows_consulta = ($db->select());
    $rows["consulta"] = (array)$rows_consulta;

    echo json_encode($rows);

?>