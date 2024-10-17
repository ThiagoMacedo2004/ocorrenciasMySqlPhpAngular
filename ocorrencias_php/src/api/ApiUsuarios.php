<?php

require_once '../config/config.php';
$data = json_decode(file_get_contents("php://input"));


// echo realpath();

if(!$data) {
    $data = $_REQUEST;
}

$ApiUsuarios = new ApiUsuarios($data);

class ApiUsuarios {
    
    private $utils;
    private $usuarios;
    private $variaveis;

    public function __construct($data)
    {
       
        $this->usuarios = new Usuarios;
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

    public function autenticacao() {
        $result = $this->usuarios->autenticacao($this->variaveis['matricula'], $this->variaveis['senha']);

        echo json_encode($result);
    }

    public function getUsuarios() {
        echo json_encode(
            $this->usuarios->getUsuarios()
        );
    }

    public function cadastrarNovoUsuario() {
        $result = $this->usuarios->cadastrarNovoUsuario(
            $this->variaveis['nome'],
            $this->variaveis['matricula'],
            $this->variaveis['senha'],
            $this->variaveis['email'],
            ''
        );

        echo json_encode($result);
    }

    public function alterarStatusUsuario() {
        $result = $this->usuarios->alterarStatusUsuario($this->variaveis['id'], $this->variaveis['status']);

        echo json_encode($result);
    }

    public function editarUsuario(){

        $result = $this->usuarios->editarUsuario(
            $this->variaveis['id'],
            $this->variaveis['matricula'],
            $this->variaveis['nome'],
            $this->variaveis['senha'],
            $this->variaveis['email']
        );

        echo json_encode($result);
    }

    public function getMenus() {
        echo json_encode($this->usuarios->getMenus($this->variaveis['id_user']));
    }
}