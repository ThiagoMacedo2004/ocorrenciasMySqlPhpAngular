<?php

require_once '../config/config.php';
$data = json_decode(file_get_contents("php://input"));

// print_r($data);

if(!$data) {
    $data = $_REQUEST;
}

$lojas = new ApiLojas($data);

class ApiLojas {
    
    private $utils;
    private $lojas;
    private $variaveis;

    public function __construct($data)
    {
       
        $this->lojas = new Lojas;
        $this->utils = new Utils($data);
        $this->variaveis = $this->utils->getVariaveis($data);
        $this->getAcao($this->variaveis['acao']);

    }

    private function getAcao($acao) {
       
        if(method_exists($this, $this->variaveis['acao'])) {
            $this->$acao();
        } else {
            echo json_encode([
                "erro" => 'Ação não encontrada.' 
            ]);
        }
    }

    public function getLojas() {
        $result = $this->lojas->getLojas();

        echo json_encode($result);
    }

    public function adicionarLoja() {
        $result = $this->lojas->adicionarLoja(
            $this->variaveis['loja'],
            $this->variaveis['local'],
            $this->variaveis['endereco'],
            $this->variaveis['bairro'],
            $this->variaveis['cep'],
            $this->variaveis['horario'],
            $this->variaveis['status']

        );

        echo json_encode($result);
    }  

    public function editarLoja() {
        $result = $this->lojas->editarLoja(
            $this->variaveis['id'],
            $this->variaveis['loja'],
            $this->variaveis['local'],
            $this->variaveis['endereco'],
            $this->variaveis['bairro'],
            $this->variaveis['cep'],
            $this->variaveis['horario'],
            $this->variaveis['status']
        );
        
        echo json_encode($result);
    }

    public function adicionarServicoLoja() {
        $result = $this->lojas->adicionarServicoLoja(
            $this->variaveis['idLoja'],
            $this->variaveis['idCnpj'],
            $this->variaveis['operadora'],
            $this->variaveis['tipoServico'],
            $this->variaveis['designacao'],
            $this->variaveis['velocidade'],
            $this->variaveis['observacao']
        );

        echo json_encode($result);
    }

    public function getServicosLoja() {
        $result = $this->lojas->getServicosLoja($this->variaveis['idLoja']);

        echo json_encode($result);
    }

    public function editarServicoLoja() {
        
        $result = $this->lojas->editarServicoLoja(
            $this->variaveis['idServico'],
            $this->variaveis['idLoja'],
            $this->variaveis['idCnpj'],
            $this->variaveis['operadora'],
            $this->variaveis['tipoServico'],
            $this->variaveis['designacao'],
            $this->variaveis['velocidade'],
            $this->variaveis['observacao']
            
        );

        echo json_encode($result);
    }

    public function getCnpjs() {
        $result = $this->lojas->getCnpjs();

        echo json_encode($result);
    }

}

?>