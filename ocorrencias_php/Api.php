<?php

require_once __DIR__ . '/src/config/config.php';
$data = json_decode(file_get_contents("php://input"));

if(!$data) {
    $data = $_REQUEST;
}

$api = new Api($data);
class Api {
    
    private $acao;
    private $ocorrencias;
    private $formularios;
    private $pdf;

    public function __construct($data)
    {
        $this->ocorrencias = new Ocorrencias;
        $this->formularios = new Formularios;
       

        $this->getVariaveis($data);

        $this->getAcao($this->acao);

        
    }

    private function getVariaveis($data) {

        foreach($data as $key => $value) {
            $this->$key = $value;
        }
    }

    private function getAcao($acao) {
       
        if(method_exists($this, $this->acao)) {
            $this->$acao();
        } else {
            echo json_encode('Ação não encontrada.');
        }
    }

    public function getOcorrencias() {
       $result = $this->ocorrencias->getOcorrencias($this->status);

       echo json_encode($result);
    }

    public function getMotivos() {
        echo json_encode(
            $this->formularios->getMotivos()
        );
    }

    public function getSubMotivos() {
        echo json_encode(
            $this->formularios->getSubMotivos($this->id)
        );
    }

    public function getAnalistas() {
        echo json_encode(
            $this->formularios->getAnalistas()
        );
    }

    public function getVeiculos() {
        echo json_encode(
            $this->formularios->getVeiculos()
        );
    }

    public function salvarOs() {
        echo json_encode(
            $this->ocorrencias->salvarOs($this->ocorrencia, $this->loja, $this->idMotivo, $this->idSubmotivo, $this->descricao, $this->triagem)
        );
    }

    public function finalizarOs() {
        echo json_encode(
            $this->ocorrencias->finalizarOs(
                $this->user,
                $this->data,
                $this->tecnico,
                $this->veiculo,
                $this->id_oc,
                $this->mouse,
                $this->teclado,
                $this->monitor,
                $this->fonte,
                $this->telefone,
                $this->cpu,
                $this->impTef,
                $this->hd,
                $this->cooler
            )
        );
    }

    public function detalheOcFinalizada() {
        echo json_encode(
            $this->ocorrencias->detalheOcFinalizada($this->id)
        );
    }

    public function deletarOs() {
        echo json_encode(
            $this->ocorrencias->deletarOs($this->id_oc, $this->ocorrencia)
        );
    }

}

?>