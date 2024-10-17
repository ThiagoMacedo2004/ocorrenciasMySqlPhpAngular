<?php

require_once '../config/config.php';
$data = json_decode(file_get_contents('php://input'));

header("Content-type: charset=UTF-8");
header("Content-type: application/vnd.ms-excel;");
header('Content-Type: text/csv; charset=utf-8');
header("Content-type: application/force-download");
header('Content-Disposition: attachment; filename=relatorio.xls');
header("Pragma: no-cache");

// ob_start();
if($data->acao === 'excelMicros') {
    echo require_once 'relatorio.php';
    
} elseif($data->acao === 'excelOcorrencias') {
    echo require_once 'excelOcorrencias.php';
}


?>