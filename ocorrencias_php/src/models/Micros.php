<?php

class Micros extends Sql {

    private $sql;

    public function __construct()
    {
        $this->sql = new Sql;
    }

    public function getMicros() {

        $result = $this->sql->select(
            "SELECT
                l.id as ID_LOJA,
                l.loja as LOJA,
                m.id as ID_MICRO,
                m.service_tag as SERVICE_TAG,
                m.asset_tag as ASSET_TAG,
                m.fabricante as FABRICANTE,
                m.modelo as MODELO,
                m.status as STATUS
            FROM tb_micro_loja ml
            INNER JOIN tb_lojas l ON (ml.id_loja = l.id)
            INNER JOIN tb_micros m ON (ml.id_micro = m.id)
            ORDER BY LOJA ASC;"
            // WHERE l.id = :id_loja;", [
            //     ":id_loja" => intval($id_loja)
            // ]
        );

        return $result;
    }

    public function adicionarMicro($serviceTag, $assetTag, $fabricante, $modelo, $idLoja) {
        $result = $this->sql->select(
            "CALL sp_adicionar_micro(
                :p_service_tag,
                :p_asset_tag,
                :p_fabricante,
                :P_modelo,
                :p_status,
                :p_id_loja
            );", [
                ":p_service_tag" => trim(strtoupper($serviceTag)),
                ":p_asset_tag"   => trim($assetTag),
                ":p_fabricante"  => trim(ucwords(strtolower($fabricante))),
                ":P_modelo"      => trim(ucwords(strtolower($modelo))),
                ":p_status"      => 'OK',
                ":p_id_loja"       => intval($idLoja)
            ]
        );

        return $result[0];
    }

    public function deletarMicro($id) {
        $result = $this->sql->select(
            "CALL sp_deletar_micro(
                :p_id_micro
            );", [
                ":p_id_micro" => intval($id)
            ]
        );

        return $result[0];
    }

    public function getHistoricoMicro($idMicro) {
        
        $result = $this->sql->select(
            "SELECT
                mm.id as ID,
                m.id as ID_MICRO,
                m.service_tag as SERVICE_TAG,
                m.asset_tag AS ASSET_TAG,
                m.fabricante AS FABRICANTE,
                m.modelo AS MODELO,
                m.status AS STATUS,
                l.id AS ID_LOJA,
                l.loja AS LOJA_ANTIGA,
                ld.id as ID_LOJA_NOVA,
                ld.loja as LOJA_NOVA,
                u.id AS ID_USUARIO,
                -- u.matricula AS MATRICULA,
                CONCAT(u.matricula, ' - ', u.nome) AS ANALISTA,
                mm.observacao AS OBSERVACAO,
                mm.data_movimentacao AS DATA
            FROM tb_movimentacao_micros as mm 
            INNER JOIN tb_micros as m ON(mm.id_micro = m.id)
            INNER JOIN tb_lojas  AS l ON(mm.id_loja = l.id)
            INNER JOIN tb_lojas as ld ON(mm.id_new_loja = ld.id)
            INNER JOIN tb_usuarios AS u ON(mm.id_usuario = u.id)
            WHERE mm.id_micro = :id_micro
            ORDER BY ID DESC;", [
                ":id_micro" => intval($idMicro)
            ]
        );

        // print_r($result);

        return $result;
    }

    // public function atualizarInfoMicros($idLoja, $micros) {

    //     foreach($micros as $key => $value) {
    //         $this->sql->query(
    //             "INSERT INTO tb_micro_loja VALUES(
    //                 NULL,
    //                 :id_micro,
    //                 :id_loja
    //             );", [
    //                 ":id_micro"   => intval($value->ID_MICRO),
    //                 "id_loja"     => intval($idLoja)
    //             ]
    //         );
    //     }
        
    //     return [
    //         "sucesso" => "OK",
    //         "erro"    => ""
    //     ];

    // }

    public function movimentacaoMicro($idMicro, $lojaNew, $lojaOld, $idUsuario, $observacao, $data, $status) {

        $result = $this->sql->select(
            "CALL sp_movimentacao_micro(
                :idMicro,
                :idLojaNew,
                :idLojaOld,
                :idUsuario,
                :observacao,
                :data,
                :status
            );", [
                ":idMicro"    => intval($idMicro),
                ":idLojaNew"  => intval($lojaNew),
                ":idLojaOld"  => intval($lojaOld),
                ":idUsuario"  => intval($idUsuario),
                ":observacao" => trim($observacao),
                ":data"       => date('Y-m-d', strtotime("{$data}")),
                ":status"     => trim(strtoupper($status))
            ]
        );

        return $result[0];
    }

    public function editarMicro($idMicro, $service_tag, $asset_tag, $status) {

        $result = $this->sql->select(
            "CALL sp_editar_micro(
                :ID_MICRO,
                :SERVICE_TAG,
                :ASSET_TAG,
                :STATUS
            );", [
                ":ID_MICRO"    => intval($idMicro),
                ":SERVICE_TAG" => trim(strtoupper($service_tag)),
                ":ASSET_TAG"   => trim($asset_tag),
                ":STATUS"      => trim($status)
            ]
        );

        return $result[0];
    }

    public function getMicrosLoja($idLoja) {

        $id = intval($idLoja);

        $qtdMirosLj = $this->sql->select(
            "SELECT
                l.loja,
                count(*) AS QTD
            FROM tb_micro_loja as ml
            INNER JOIN tb_micros as m ON(ml.id_micro = m.id)
            INNER JOIN tb_lojas as l ON(ml.id_loja = l.id)
            WHERE m.status <> 'FURTO' AND ml.id_loja = $id;"
        );

        $historicoOcLj = $this->sql->select(
            "SELECT
                O.ID,
                O.OCORRENCIA,
                O.DESCRICAO,
                O.TRIAGEM,
                O.DATA,
                O.DATA_FINAL AS DATA_ATEND,
                O.STATUS,
                L.loja AS LOJA,
                M.motivo AS MOTIVO,
                S.submotivo AS SUBMOTIVO,
                CONCAT(A.matricula, ' - ', A.nome) AS ANALISTA

                -- T.nome AS TECNICO,
                -- T.matricula as MATRICULA_TEC,
                -- AF.nome AS ANALISTA_FINA,
                -- AF.matricula AS MATRICULA_FINA

            FROM tb_ocorrencias AS O
            INNER JOIN tb_lojas      AS L  ON (L.id = O.LOJA_ID)
            INNER JOIN tb_motivos    AS M  ON (M.id = O.MOTIVO_ID)
            INNER JOIN tb_submotivos AS S  ON (S.id = O.SUBMOTIVO_ID)
            INNER JOIN tb_usuarios   AS A  ON (A.id = O.ANALISTA_ID)

            -- INNER JOIN tb_usuarios   AS T  ON (T.id = O.TECNICO_ID)
            -- INNER JOIN tb_usuarios   AS AF ON (AF.id = O.ANALISTA_FINAL_ID);
            
            WHERE O.LOJA_ID = :ID_LOJA
            ORDER BY O.DATA DESC
            LIMIT 5;
            -- INNER JOIN tb_usuarios   AS T  ON (T.id = O.TECNICO_ID)
            -- INNER JOIN tb_usuarios   AS AF ON (AF.id = O.ANALISTA_FINAL_ID);
            ", [
                ":ID_LOJA" => $idLoja
            ]
        );

        return [
            "QTD_MICROS_LJ" => $qtdMirosLj[0],
            "HISTORICO_OC_LJ" => $historicoOcLj
        ];
    }

}

?>