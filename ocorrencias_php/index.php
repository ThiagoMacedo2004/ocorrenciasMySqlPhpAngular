<?php

require_once __DIR__ . '/src/config/config.php';
$data = json_decode(file_get_contents("php://input"));

if(!$data) {
    $data = $_REQUEST;
}

$arquivo = fopen('micros_vendas.csv', "r");

$lojas = [];

$sql = new Sql;

while(!feof($arquivo)) {
    $linha = fgetcsv($arquivo, 2000, ';');

    foreach($linha as $key => $value) {
        $micro[$key] = $value;
    }

    $sql->query(
        "INSERT INTO tb_micros
         VALUES(
            NULL,
            :service_tag,
            :asset_tag,
            :fabricante,
            :modelo,
            :loja
        );", [
            ":service_tag" => trim($micro[0]),
            ":asset_tag" => trim($micro[1]),
            ":fabricante" => trim($micro[2]),
            ":modelo" => trim($micro[3]),
            ":loja"   => trim($micro[4])
        ]
    );

    echo $micro[4] . "Gravada no banco!!\n";
    
}


$api = new Api($data);
class Api {
    
    private $acao;
    private $ocorrencias;
    private $formularios;
    private $usuarios;
    private $pdf;

    public function __construct($data)
    {
        $this->ocorrencias = new Ocorrencias;
        $this->formularios = new Formularios;
        $this->usuarios    = new Usuarios;

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
            echo json_encode([
                "erro" => 'Ação não encontrada.' 
            ]);
        }
    }

    public function autenticacao() {
        $result = $this->usuarios->autenticacao($this->matricula, $this->senha);

        echo json_encode($result);
    }

    public function getUsuarios() {
        echo json_encode(
            $this->usuarios->getUsuarios()
        );
    }


    public function getMenus() {
        echo json_encode($this->usuarios->getMenus($this->id_user));
    }

    public function cadastrarNovoUsuario() {
        $result = $this->usuarios->cadastrarNovoUsuario($this->nome, $this->matricula, $this->senha, $this->email, $this->userAdm);

        echo json_encode($result);
    }

    public function alterarStatusUsuario() {
        $result = $this->usuarios->alterarStatusUsuario($this->id, $this->status);

        echo json_encode($result);
    }

    public function editarUsuario(){
        $result = $this->usuarios->editarUsuario($this->id, $this->matricula, $this->nome, $this->senha, $this->email);

        echo json_encode($result);
    }


    public function getLojas() {
        $result = $this->formularios->getLojas();

        echo json_encode($result);
    }

    public function adicionarLoja() {
        $result = $this->formularios->adicionarLoja(
            $this->loja,
            $this->local,
            $this->endereco,
            $this->bairro,
            $this->cep,
            $this->horario,
            $this->nomeServico,
            $this->designacao,
            $this->velocidade,
            $this->operadora,
            $this->numero_lp
        );

        echo json_encode($result);
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

    public function gerarExcel() {
       
        $dados = $this->data;

        $relatorio = fopen('relatorio.csv', 'w');

        shell_exec('chmod 777 relatorio.csv');

        $cabecalho = [
            'LOJA',
            'OCORRENCIA',
            'MOTIVO',
            'SUBMOTIVO',
            'DATA',
            'DATA ATENDIMENTO',
            'STATUS'
        ];

        fputcsv($relatorio, $cabecalho, ';');
        
        foreach($dados as $key => $value) {
            $row = [
                iconv("UTF-8", "ISO-8859-1", $value->loja),
                iconv("UTF-8", "ISO-8859-1", $value->ocorrencia),
                iconv("UTF-8", "ISO-8859-1", $value->motivo),
                iconv("UTF-8", "ISO-8859-1", $value->submotivo),
                iconv("UTF-8", "ISO-8859-1", $value->date_create ? date('d/m/Y', strtotime($value->date_create)): '-' ),
                iconv("UTF-8", "ISO-8859-1", $value->date_final ? date('d/m/Y', strtotime($value->date_final)): '-' ),
                strtoupper(iconv("UTF-8", "ISO-8859-1", $value->status))
            ];
            fputcsv($relatorio, $row, ';');
          
        }
        
        echo json_encode([
            "sucesso" => "Relatorio gerado com sucesso!!"
        ]);
             
    }

}

?>