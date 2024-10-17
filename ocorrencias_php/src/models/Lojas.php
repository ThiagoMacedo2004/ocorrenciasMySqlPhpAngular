<?php

class Lojas extends Sql {

    private $sql;

    public function __construct()
    {
        $this->sql = new Sql;
    }


    public function getLojas() {
        $result = $this->sql->select(
            "SELECT
                l.id as ID,
                l.loja as LOJA,
                l.local as LOCAL,
                l.endereco as ENDERECO,
                l.bairro as BAIRRO,
                l.cep as CEP,
                l.horario_funcionamento as HORARIO_FUNC,
                l.status as STATUS
            FROM tb_lojas l
            ORDER BY l.loja ASC;"
        );

        return $result;
    }

    public function adicionarLoja(
        $loja,
        $local,
        $endereco,
        $bairro,
        $cep,
        $horario,
        $status
    ) {
        $result = $this->sql->select(
            "CALL sp_adicionar_loja(
                :p_loja,
                :p_local,
                :p_endereco,
                :p_bairro,
                :p_cep,
                :p_horario_funcionamento,
                :p_status
            );",[
                ":p_loja"                  => trim(ucwords($loja)),
                ":p_local"                 => trim($local),
                ":p_endereco"              => trim(ucwords($endereco)),
                ":p_bairro"                => trim(ucwords($bairro)),
                ":p_cep"                   => trim($cep),
                ":p_horario_funcionamento" => trim($horario),
                ":p_status"                => trim(strtoupper($status))
            ]
        );

        return $result[0];
    }

    public function editarLoja(
        $id,
        $loja,
        $local,
        $endereco,
        $bairro,
        $cep,
        $horario,
        $status
    ){
        $result = $this->sql->select(
            "CALL sp_editar_loja(
                :p_id,
                :p_loja,
                :p_local,
                :p_endereco,
                :p_bairro,
                :p_cep,
                :p_horario_funcionamento,
                :p_status
            );", [
                ":p_id"                    => intval($id),
                ":p_loja"                  => trim(ucwords($loja)),
                ":p_local"                 => trim($local),
                ":p_endereco"              => trim(ucwords($endereco)),
                ":p_bairro"                => trim(ucwords($bairro)),
                ":p_cep"                   => trim($cep),
                ":p_horario_funcionamento" => trim($horario),
                ":p_status"                => trim(strtoupper($status))
            ]
        );
        return $result[0];
    }

    public function adicionarServicoLoja(
        $id_loja,
        $id_cnpj,
        $operadora,
        $tipo_servico,
        $designacao,
        $velocidade,
        $observacao
    ) {
        $result = $this->sql->select(
            "CALL sp_adicionar_servico_loja(
                :id_loja,
                :id_cnpj,
                :operadora,
                :tipo_servico,
                :designacao,
                :velocidade,
                :observacao
            );", [
                ":id_loja"      => intval($id_loja),
                ":id_cnpj"      => intval($id_cnpj),
                ":operadora"    => trim(ucwords(strtolower($operadora))),
                ":tipo_servico" => trim(ucwords(strtolower($tipo_servico))),
                ":designacao"   => trim(ucwords(strtolower($designacao))),
                ":velocidade"   => trim(strval("{$velocidade}MB")),
                ":observacao"   => trim($observacao)
            ]
        );

        return $result[0];
    }

    public function getServicosLoja($idLoja = 0) {
        // $result = $this->sql->select(
        //     "SELECT
        //         c.id            as ID_CNPJ,
        //         c.cnpj          as CNPJ,
        //         c.razao_social  as RAZAO,
        //         sl.id           as ID_SERVICO,
        //         sl.operadora    as OPERADORA,
        //         sl.tipo_servico as TIPO_SERVICO,
        //         sl.designacao   as DESIGNACAO,
        //         sl.velocidade   as VELOCIDADE,
        //         sl.observacao   as OBSERVACAO
        //     FROM tb_servicos_lojas as sl
        //     INNER JOIN tb_cnpj  as c ON (sl.id_cnpj = c.id)
        //     INNER JOIN tb_lojas as l ON (sl.id_loja = l.id)
        //     WHERE sl.id_loja = :id_loja;",[
        //         ":id_loja" => intval($idLoja)
        //     ]
        // );

        $result = $this->sql->select(
            "SELECT
                sl.id           as ID_SERVICO,
                sl.operadora    as OPERADORA,
                sl.tipo_servico as TIPO_SERVICO,
                sl.designacao   as DESIGNACAO,
                sl.velocidade   as VELOCIDADE,
                sl.observacao   as OBSERVACAO,
                l.id            as ID_LOJA,
                l.loja          as LOJA,
                c.id            as ID_CNPJ,
                c.cnpj          as CNPJ,
                c.razao_social  as RAZAO
            FROM tb_servicos_lojas as sl
            INNER JOIN tb_cnpj  as c ON (sl.id_cnpj = c.id)
            INNER JOIN tb_lojas as l ON (sl.id_loja = l.id);"
        );

        return $result;
    }
    public function editarServicoLoja($id_servico, $id_loja, $id_cnpj, $operadora, $tipo_servico, $designacao, $velocidade, $observacao = NULL) {
        $result = $this->sql->select(
            "CALL sp_editar_servico_loja(
                :p_id_servico,
                :p_id_loja,
                :p_id_cnpj,
                :p_operadora,
                :p_tipo_servico,
                :p_designacao,
                :p_velocidade,
                :p_observacao
            );",[
                ":p_id_servico" => intval($id_servico),
                ":p_id_loja"      => intval($id_loja),
                ":p_id_cnpj"      => intval($id_cnpj),
                ":p_operadora"    => trim(ucwords(strtolower($operadora))),
                ":p_tipo_servico" => trim(ucwords(strtolower($tipo_servico))),
                ":p_designacao"   => trim(ucwords(strtolower($designacao))),
                ":p_velocidade"   => trim(strval("{$velocidade}MB")),
                ":p_observacao"   => trim($observacao)
            ]
        );

        return $result[0];
    }

    public function getCnpjs() {
        $result = $this->sql->select(
            "SELECT * FROM tb_cnpj;"
        );

        return $result;
    }

}

?>