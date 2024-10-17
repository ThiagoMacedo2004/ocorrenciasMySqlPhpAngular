<?php

require_once '../config/config.php';
$data = json_decode(file_get_contents("php://input"));

if(!$data) {
    $data = $_REQUEST;
}

// print_r($data);


$ocorrencias = new ApiOcorrencia($data);

class ApiOcorrencia {
    private $utils;
    private $ocorrencia;
    private $variaveis;

    public function __construct($data)
    {   
               
        $this->ocorrencia = new Ocorrencias;
        $this->utils = new Utils($data);
        $this->variaveis = $this->utils->getVariaveis($data);

        if(isset($this->variaveis['acao'])) {
            $this->getAcao($this->variaveis['acao']);
        } else {
            $this->getAcao($this->variaveis['informacoes']->acao);
        }

        
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

    public function getMotivos() {
        $result = $this->ocorrencia->getMotivos();
        echo json_encode($result);
    }

    public function getSubMotivos() {
        $result = $this->ocorrencia->getSubMotivos($this->variaveis['idMotivo']);
        echo json_encode($result);
    }

    public function gravarOcorrencia() {

        $result = $this->ocorrencia->gravarOcorrencia(
            $this->variaveis['ocorrencia'],
            $this->variaveis['descricao'],
            $this->variaveis['triagem'],
            $this->variaveis['idLoja'],
            $this->variaveis['idMotivo'],
            $this->variaveis['idSubmotivo'],
            $this->variaveis['idAnalista'],
        );

        echo json_encode($result);
    }

    public function finalizarOc() {
        // print_r($this->variaveis['informacoes']);

        $result = $this->ocorrencia->finalizarOs(
            $this->variaveis['informacoes']->idOcorrencia,
            $this->variaveis['informacoes']->dataAtendimento,
            $this->variaveis['informacoes']->idTecnico,
            $this->variaveis['informacoes']->idAnalista,
            $this->variaveis['informacoes']->idVeiculo,
            $this->variaveis['materiais']
        );

        echo json_encode($result);
    }

    public function getOcorrencias() {

        $result = $this->ocorrencia->getOcorrencias();

        echo json_encode($result);
    }

    public function detalheOcorrencia() {
        $detalheOc = $this->ocorrencia->detalheOcorrencia($this->variaveis['ocorrencia']);
        $detalhe_atencimento_oc = '';
        $materiaisUtilizados = [];

        if($this->variaveis['status'] === 'Finalizada') {
            $detalhe_atencimento_oc = $this->ocorrencia->detalheAtendimentoOc($this->variaveis['ocorrencia']);
            $materiaisUtilizados = $this->ocorrencia->materiaisUtilizados($this->variaveis['id']);
        }

        $result = [
            "DETALHE_OC"             => $detalheOc,
            "DETALHE_ATENDIMENTO_OC" => $detalhe_atencimento_oc,
            "MATERIAIS_UTILIZADOS"   => $materiaisUtilizados
        ];

        // print_r($result);

        echo json_encode($result);
    }

    public function getVeiculos() {
        
        $result = $this->ocorrencia->getVeiculos();

        echo json_encode($result);
    }

    public function getMateriais() {

        $result = $this->ocorrencia->getMateriais();

        foreach($result as $row => $value) {
            $materiais[] = [
                "ID" => $value['ID'],
                "MATERIAL" => mb_convert_case($value['MATERIAL'], MB_CASE_TITLE, 'UTF-8')
            ];
        }

        // print_r($materiais);

        echo json_encode($materiais);
    }

    public function pesquisaOcorrencia() {

        $result = $this->ocorrencia->pesquisaOcorrencia(
            $this->variaveis['idLoja'],
            $this->variaveis['ocorrencia'],
            $this->variaveis['idMotivo'],
            $this->variaveis['idSubmotivo'],
            $this->variaveis['idMaterial'],

        );

        echo json_encode($result);
    }

}

?>