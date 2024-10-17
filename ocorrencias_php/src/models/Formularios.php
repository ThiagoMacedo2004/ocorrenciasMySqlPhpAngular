<?php



class Formularios extends Sql {

    private $sql;

    public function __construct()
    {
        $this->sql = new Sql;
    }

    public function getMotivos() {
        $result = $this->sql->select(
            "SELECT * FROM tb_motivos;"
        );

        return $result;
    }

    public function getSubMotivos($id) {
        $result = $this->sql->select(
            "SELECT
                s.id AS id_submotivo,
                s.submotivo
            FROM tb_submotivos AS s
            INNER JOIN tb_motivos as m ON (m.id = s.id_motivo)
            WHERE s.id_motivo = :id
            ORDER BY s.submotivo ASC;", [
                "id" => intval($id)
            ]
        );

        return $result;
    }

    public function getVeiculos() {
        $result = $this->sql->select(
            "SELECT CONCAT(modelo, ' - ', placa) as veiculo, id FROM tb_veiculos
            ORDER BY
                veiculo ASC;
            "
        );

        return $result;
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
                l.status as STATUS,
                ll.numero_lp as NUMERO_LP,
                ll.operadora AS OPERADORA,
                bl.nome_servico as NOME_SERVICO,
                bl.designacao as DESIGNACAO,
                bl.velocidade as VELOCIDADE
            FROM tb_lojas l
            INNER JOIN tb_links_lojas ll ON(ll.id_loja = l.id)
            INNER JOIN tb_banda_larga_lojas bl ON(l.id = bl.id_loja)
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
        $nomeServico,
        $designacao,
        $velocidade,
        $operadora,
        $numero_lp
    ) {
        $result = $this->sql->select(
            "CALL sp_adicionar_loja(
                :p_loja,
                :p_local,
                :p_endereco,
                :p_bairro,
                :p_cep,
                :p_horario_funcionamento,
                :p_status,
                :p_nome_servico,
                :p_designacao,
                :p_velocidade,
                :p_operadora,
                :p_numero_lp
            );",[
                ":p_loja"                  => trim(ucwords($loja)),
                ":p_local"                 => trim($local),
                ":p_endereco"              => trim(ucwords($endereco)),
                ":p_bairro"                => trim(ucwords($bairro)),
                ":p_cep"                   => trim($cep),
                ":p_horario_funcionamento" => trim($horario),
                ":p_status"                => trim(strtoupper("ABERTA")),
                ":p_nome_servico"          => trim($nomeServico),
                ":p_designacao"            => trim($designacao),
                ":p_velocidade"            => trim(strtoupper($velocidade)),
                ":p_operadora"             => trim($operadora),
                ":p_numero_lp"             => trim($numero_lp)
            ]
        );

        return $result[0];
    }

}

?>