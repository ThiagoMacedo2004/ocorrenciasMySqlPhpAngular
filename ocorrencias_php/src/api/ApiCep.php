<?php
require_once '../config/config.php';
$data = json_decode(file_get_contents("php://input"));

if(!$data) {
    $data = $_REQUEST;
}

$cep = str_replace("-", "", $data['cep']);

echo $cep;

$url = "https://opencep.com/v1/$cep";
$endereco = json_decode(file_get_contents($url));

echo json_encode($endereco);

?>