<?php 

header("Content-type: charset=UTF-8");
header("Content-type: application/vnd.ms-excel;");
header("Content-type: application/force-download");
header('Content-Disposition: attachment; filename=relatorio.xls');
header("Pragma: no-cache");
// print_r($_POST['php://input'])
?>