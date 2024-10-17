<?php

class Ocorrencias extends Sql {

    private $sql;
    private $pdf;

    public function __construct()
    {
        $this->sql = new Sql;
    }

    public function getOcorrencias($status = 'Aberta') {
        $result = $this->sql->select(
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
            WHERE O.STATUS = :STATUS
            ORDER BY O.ID DESC;
            -- INNER JOIN tb_usuarios   AS T  ON (T.id = O.TECNICO_ID)
            -- INNER JOIN tb_usuarios   AS AF ON (AF.id = O.ANALISTA_FINAL_ID);
            ", [
                ":STATUS" => $status
            ]
        );

        return $result;
    }

    public function detalheOcorrencia($ocorrencia)
    {
        
        $result = $this->sql->select(
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
            
            WHERE O.OCORRENCIA = :OCORRENCIA
            ORDER BY O.DATA DESC;
            -- INNER JOIN tb_usuarios   AS T  ON (T.id = O.TECNICO_ID)
            -- INNER JOIN tb_usuarios   AS AF ON (AF.id = O.ANALISTA_FINAL_ID);
            ", [
                ":OCORRENCIA" => $ocorrencia
            ]
        );

        return $result[0];

    }

    public function detalheAtendimentoOc($ocorrencia) {

        $result = $this->sql->select(
            "SELECT
                O.DATA_FINAL AS DATA_ATEND,
                CONCAT(TF.matricula, ' - ', TF.nome) AS TECNICO,
                CONCAT(AF.matricula, ' - ', AF.nome) AS ANALISTA_FINAL,
                CONCAT(V.marca, ' - ', V.modelo, ' - ', V.placa) AS VEICULO
            FROM tb_ocorrencias AS O
            INNER JOIN tb_usuarios as TF ON (TF.id = O.TECNICO_ID)
            INNER JOIN tb_usuarios as AF ON (AF.id = O.ANALISTA_FINAL_ID)
            INNER JOIN tb_veiculos as V ON (V.id = O.VEICULO_ID)
            WHERE O.OCORRENCIA = '$ocorrencia';"
        );

        return $result[0];

    }

    public function materiaisUtilizados($idOc) {

        $result = $this->sql->select(
            "SELECT
                M.MATERIAL,
                MU.QUANTIDADE
            FROM tb_materiais_utilizados as MU
            INNER JOIN tb_materiais as M on (M.ID = MU.MATERIAL_ID)
            WHERE MU.OC_ID = :idOc;", [
                ":idOc" => intval($idOc)
            ]
        );

        return $result;
    }

    public function pesquisaOcorrencia($idLoja, $ocorrencia, $idMotivo, $idSubmotivo) {

        $result = $this->sql->select(
            "CALL sp_pesquisa_ocorrencia(
                :p_loja_id,
                :p_ocorrencia,
                :p_motivo_id,
                :p_submotivo_id
            );", [
                ":p_loja_id"      => $idLoja ? intval($idLoja): null,
                ":p_ocorrencia"   => $ocorrencia ? trim(strtoupper($ocorrencia)) : null,
                ":p_motivo_id"    => $idMotivo ? intval($idMotivo) : null,
                ":p_submotivo_id" => $idSubmotivo ? intval($idSubmotivo) : null            ]
        );

        return $result;
    }

    public function finalizarOs($idOcorrencia, $dataFInal, $idTecnicoFinal, $idAnalistaFinal, $idVeiculo, $materiais) {

        $result = $this->sql->select(
            "CALL sp_finalizar_ocorrencia(
                :p_ocorrencia_id,
                :p_data_final,
                :p_tecnico_id,
                :p_analista_final_id,
                :p_veiculo_id
            );", [
                ":p_ocorrencia_id"     => intval($idOcorrencia),
                ":p_data_final"        => date('Y-m-d', strtotime("{$dataFInal}")),
                ":p_tecnico_id"        => intval($idTecnicoFinal),
                ":p_analista_final_id" => intval($idAnalistaFinal),
                ":p_veiculo_id"        => intval($idVeiculo)
            ]);

            $this->salvarMateriaisUtilizados($idOcorrencia, $materiais);


        return $result[0];  
    }

    public function salvarMateriaisUtilizados($idOcorrencia, $materiais) {
        
        if(count($materiais) > 0) {
            foreach($materiais as $row => $value) {
                // echo "ID_MATERIAL: $value->ID\nID_OC: $idOcorrencia\n";
                $this->sql->query(
                    "INSERT INTO tb_materiais_utilizados
                     VALUES(NULL, $idOcorrencia, $value->ID, $value->QTD); 
                    "
                );
            }
        }
    }

    public function detalheOcFinalizada($id) {
        $result = $this->sql->select(
            "SELECT
                o.id,
                o.loja,
                o.ocorrencia,
                m.motivo,
                s.submotivo,
                o.descricao,
                o.triagem,
                concat(u.matricula, ' - ', u.nome) as analista,
                concat(us.matricula, ' - ', us.nome) as analistaf,
                concat(ut.matricula, ' - ', ut.nome) as tecnico,
                o.date_create,
                o.date_final,
                o.status,
                o.selected,
                concat(v.modelo, ' - ', v.placa) as veiculo,
                ma.*
            FROM tb_ocorrencias o
            INNER JOIN users u         ON (u.id = o.id_user_create)
            INNER JOIN users us        ON (us.id = o.id_user_final)
            INNER JOIN users ut        ON (ut.id = o.id_user_tecnico)
            INNER JOIN tb_motivos m    ON (m.id = o.id_motivo)
            INNER JOIN tb_submotivos s ON (s.id = o.id_submotivo)
            INNER JOIN tb_veiculos v   ON (v.id = o.id_veiculo)
            INNER JOIN tb_materiais ma ON (ma.id_ocorrencia = o.id)
            WHERE o.id = :id", [
                ":id" => intval($id)
            ]
        );

        return $result;
    }

    public function gravarOcorrencia($ocorrencia, $descricao, $triagem, $idLoja, $idMotivo, $idSubmotivo, $idAnalista )
    {   
        $ocorrencia = trim(strtoupper($ocorrencia));

        $result = $this->sql->select("CALL sp_ocorrencia_save(
            :p_ocorrencia,
            :p_descricao,
            :p_triagem,
            :p_status,
            :p_loja_id,
            :p_motivo_id,
            :p_submotivo_id,
            :p_analista_id
            )", [
            ":p_ocorrencia"    => trim(strtoupper($ocorrencia)),
            ":p_descricao"     => trim($descricao),
            ":p_triagem"       => trim($triagem),
            ":p_status"        => 'Aberta',
            ":p_loja_id"       => intval($idLoja),
            ":p_motivo_id"     => intval($idMotivo),
            ":p_submotivo_id"  => intval($idSubmotivo),
            ":p_analista_id"   => intval($idAnalista)
            
        ]);

        // $this->pdf = new Pdf($ocorrencia);
        return $result[0];
         
    }

    public function deletarOs($id_oc, $ocorrencia)
   {    
        $ocorrencia = trim(strtoupper($ocorrencia));

        $result = $this->sql->query("CALL sp_detelar_os(:p_id_oc)",[
            ":p_id_oc" => $id_oc
        ]);

        if($result[0]['sucesso']) {
            shell_exec("cd pdfs/ ; rm -rf {$ocorrencia}.pdf");
        }

        return $result;
   }

   public function getMotivos() {
    $result = $this->sql->select(
        "SELECT * FROM tb_motivos;"
    );

    return $result;
   }

   public function getSubMotivos($idMotivo) {
    $result = $this->sql->select(
        "SELECT * 
        FROM tb_submotivos
        WHERE MOTIVO_ID = :idMotivo", [
            ":idMotivo" => intval($idMotivo)
        ]
    );

    return $result;

   }

   public function getVeiculos() {

    $result = $this->sql->select(
        "SELECT
            id as ID,
            marca as MARCA,
            modelo as MODELO,
            placa as PLACA
        FROM tb_veiculos;"
    );

    return $result;
    
   }

   public function getMateriais() {

    $result = $this->sql->select(
        "SELECT * FROM tb_materiais;"
    );

    return $result;
   }

}

?>