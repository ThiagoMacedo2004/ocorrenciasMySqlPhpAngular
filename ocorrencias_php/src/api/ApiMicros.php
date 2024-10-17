<?php

require_once '../config/config.php';
$data = json_decode(file_get_contents("php://input"));

if(!$data) {
    $data = $_REQUEST;
}

// $arquivo = fopen('../../micros_v3.csv', "r");

// $lojas = [];

// $sql = new Sql;

// while(!feof($arquivo)) {
//     $linha = fgetcsv($arquivo, 2000, ';');

//     foreach($linha as $key => $value) {
//         $micro[$key] = $value;
//     }

//     $sql->select(
//         "INSERT INTO tb_micros VALUES(
//             NULL,
//             :service_tag,
//             :asset_tag,
//             :fabricante,
//             :modelo,
//             :status
//         );",[
//             ":service_tag" => trim($micro[0]),
//             ":asset_tag"   => trim($micro[1]),
//             ":fabricante"  => trim('Dell Inc.'),
//             ":modelo"      => trim('Optiplex 3080'),
//             ":status"      => $micro[4] ? trim($micro[4]) : 'OK' 
//         ]
//     );

//     $id_micro = $sql->select(
//         "SELECT id
//          FROM tb_micros
//          WHERE service_tag = :service_tag;",[
//             ":service_tag" => trim($micro[0])
//         ]
//     );

//     $id_loja = $sql->select(
//         "SELECT id
//          FROM tb_lojas
//          WHERE loja = :loja;",[
//             ":loja" => trim($micro[3])
//          ]
//     );

//     echo "{$id_micro[0]['id']} - {$id_loja[0]['id']}" ;

//     $sql->query(
//         "INSERT INTO tb_micro_loja
//          VALUES(
//             NULL,
//             :id_micro,
//             :id_loja
//         );", [
//             ":id_micro" => intval($id_micro[0]['id']),
//             ":id_loja"  => intval($id_loja[0]['id'])
//         ]
//     );

//     echo $micro[4] . "Gravada no banco!! ----\n";
    
// }




// print_r($data);


$micros = new ApiMicros($data);

// foreach($data->objMicros as $key => $value) {
//     print_r($value->ID);
// }

class ApiMicros {

    private $utils;
    private $micros;
    private $variaveis;

    public function __construct($data)
    {
        
        $this->micros = new Micros;
        $this->utils = new Utils($data);
        $this->variaveis = $this->utils->getVariaveis($data);
        $this->getAcao($this->variaveis['acao']);
        
    }

    private function getAcao($acao) {
       
        
        if(method_exists($this, $acao)) {
            $this->$acao();
        } else {
            echo json_encode([
                "erro" => 'Ação não encontrada.' 
            ]);
        }
    }
    

    public function getMicros() {
        $result = $this->micros->getMicros();
        echo json_encode($result);
    }

    public function adicionarMicro() {
        $result = $this->micros->adicionarMicro(
            $this->variaveis['SERVICE_TAG'],
            $this->variaveis['ASSET_TAG'],
            $this->variaveis['FABRICANTE'],
            $this->variaveis['MODELO'],
            $this->variaveis['ID_LOJA']
        );

        echo json_encode($result);
    }

    public function deletarMicro() {
        $result =$this->micros->deletarMicro($this->variaveis['ID_MICRO']);

        echo json_encode($result);
    }

    public function editarMicro() {
        $result = $this->micros->editarMicro(
            $this->variaveis['ID_MICRO'],
            $this->variaveis['SERVICE_TAG'],
            $this->variaveis['ASSET_TAG'],
            $this->variaveis['STATUS'],
        );

        echo json_encode($result);
    }

    public function getHistoricoMicro() {
        $result = $this->micros->getHistoricoMicro($this->variaveis['idMicro']);

        echo json_encode($result);
    }
    

    public function movimentacaoMicro() {
        $result = $this->micros->movimentacaoMicro(
            $this->variaveis['idMicro'],
            $this->variaveis['idLojaNew'],
            $this->variaveis['idLojaOld'],
            $this->variaveis['idUsuario'],
            $this->variaveis['observacao'],
            $this->variaveis['data'],
            $this->variaveis['status']
        );

        echo json_encode($result);
    }

    public function getMicrosLoja() {

        $result = $this->micros->getMicrosLoja($this->variaveis['idLoja']);

        echo json_encode($result);
    }

}

?>