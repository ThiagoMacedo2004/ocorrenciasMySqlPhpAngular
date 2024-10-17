<?php

class Utils {


    private $acao;
    private $variaveis = [];
    
    public function __construct($data)
    {
        $this->getVariaveis($data);

        // $this->getAcao($this->acao);
    }
    
    public function getVariaveis($data) {

        foreach($data as $key => $value) {
            $this->variaveis[$key] = $value;
        }

        return $this->variaveis;
    }

}

?>