<?php


class Usuarios extends Sql {

    private $sql;

    public function __construct()
    {
        $this->sql = new Sql;
    }

    public function getUsuarios() {
        $result = $this->sql->select(
            "SELECT
                id as ID,
                matricula as MATRICULA,
                nome as NOME,
                senha as SENHA,
                email as EMAIL,
                date_create as DATE_CREATE,
                date_block as DATE_BLOCK ,
                status as STATUS
            FROM tb_usuarios
            ORDER BY nome"
        );

        return $result;
    }

    public function cadastrarNovoUsuario($nome, $matricula, $senha, $email, $perfil) {
        $result = $this->sql->select(
            "CALL sp_novo_usuario(
                :p_matricula,
                :p_nome,
                :p_senha,
                :p_email,
                :p_perfil
            )", [
                ":p_matricula" => trim(intval($matricula)),
                ":p_nome"      => trim(mb_convert_case( $nome, MB_CASE_TITLE , 'UTF-8' )),
                ":p_senha"     => $senha,
                ":p_email"     => trim($email),
                ":p_perfil"    => $perfil ? 'ADM' : 'USER'
            ]
        );

        // print_r($result[0]);
        return $result[0];
    }

    public function autenticacao($matricula, $senha) {
        $result = $this->sql->select(
            "SELECT
                id as ID,
                matricula as MATRICULA,
                nome as NOME,
                senha as SENHA,
                email as EMAIL,
                date_create as DATE_CREATE,
                date_block as DATE_BLOCK ,
                status as STATUS
             FROM tb_usuarios
             WHERE matricula = :matricula AND senha = BINARY :senha;",[
                ":matricula" => trim(intval($matricula)),
                // ":senha"     => password_verify(trim($senha), PASSWORD_DEFAULT)
                ":senha"    => trim($senha)
            ]
        );

        // if(count($result) == 0) {
        //     return [
        //         "sucesso" => "",
        //         "erro"    => "Usuário ou senha incorrentos."
        //     ];
        // } else {

        //     if($result[0]['status'] == 'BLOQUEADO') {
        //         return [
        //             "sucesso" => "",
        //             "erro"    => "Usuário Bloqueado. Verifique com o ADM do sistema."
        //         ];
        //     } else {
        //         return [
        //             "sucesso" => $result[0],
        //             "erro"    => ""
        //         ];
        //     }
        // }

        if($result[0]['STATUS'] == 'BLOQUEADO') {
            return [
                "sucesso" => "",
                "erro"    => "Usuário Bloqueado. Verifique com o ADM do sistema."
            ];
        } else {

            if(count($result) == 0) {
                return [
                    "sucesso" => "",
                    "erro"    => "Usuário ou senha incorrentos."
                ];
            } else {
                return [
                    "sucesso" => $result[0],
                    "erro"    => ""
                ];
            }
        }


    }

    public function getMenus($id) {
        
        $menus=[ ];

        $resultMenus = $this->sql->select(
            "SELECT m.*, u.nome
            FROM tb_menus as m
            INNER JOIN tb_acessos_menus as a ON(m.id = a.id_menu)
            INNER JOIN tb_usuarios      as u ON(a.id_usuario = u.id)
            WHERE a.id_usuario = :id;",[
                ":id" => intval($id)
            ]
        );

        // print_r($resultMenus);

        foreach($resultMenus as $key => $menu) {
            
            $menus[$key]['name']   = $menu['menu'];
            // $menus[$key]['router'] = $menu['router'];

            foreach($this->getAtividades($menu['id']) as $keyAti => $valueAti) {
                $menus[$key]['children'][$keyAti]['name'] = $valueAti['atividade'];
                $menus[$key]['children'][$keyAti]['router'] = $valueAti['router'];
            }
            
            foreach($this->getSubmenus($menu['id']) as $keySubm => $value){
                $menus[$key]['children'][$keySubm]['name'] = $value['sub_menu'];
                // $menus[$key]['children'][$keySubm]['router'] = $value['router'];

                foreach($this->getAtividadesSubMenus($value['id']) as $keyAtiSubMenu => $valueAtiSubMenu ){
                    $menus[$key]['children'][$keySubm]['children'][$keyAtiSubMenu]['name'] = $valueAtiSubMenu['atividade'];
                    $menus[$key]['children'][$keySubm]['children'][$keyAtiSubMenu]['router'] = $valueAtiSubMenu['router'];
                    
                }
            }    
        }

        return $menus;

    }

    public function getSubmenus($id) {
        $result = $this->sql->select(
            "SELECT sb.*
             FROM tb_sub_menus as sb
             INNER JOIN tb_menus as m on(sb.id_menu = m.id)
             WHERE sb.id_menu = :id;",[
                ":id" => intval($id)
             ]
        );
        // print_r($result);
        return $result;
    }

    public function getAtividades($id_menu) {
        $result = $this->sql->select(
            "SELECT a.*
             FROM tb_atividades as a
             INNER JOIN tb_menus as m on(a.id_menu = m.id)
             WHERE a.id_menu = :id_menu;",
             [
                ":id_menu" => intval($id_menu)
             ]
        );
        return $result;
    }

    public function getAtividadesSubMenus($id_subMenu) {
        $result = $this->sql->select(
            "SELECT 
                a.id as id_atividade,
                a.atividade,
                a.router
            FROM tb_atividades as a
            INNER JOIN tb_sub_menus as sm ON(a.id_sub_menu = sm.id)
            -- INNER JOIN tb_menus as m      ON(a.id_menu = m.id)
            WHERE a.id_sub_menu = :id_subMenu",[
                ":id_subMenu" => intval($id_subMenu)
            ]
        );

        return $result;
    }


    public function alterarStatusUsuario($id, $status) {
        $date_block = NULL;

        $status == 'BLOQUEADO' ? $date_block =  "CURDATE()" : $date_block = 'NULL';
        $result = $this->sql->query(
            "UPDATE tb_usuarios
            SET date_block = $date_block, status = :status
            WHERE id = :id;", [
                ":status"    => trim(strtoupper($status)),
                ":id"        => intval($id)
            ]
        );
        
        // print_r($result);
        return $result;
    }

    public function editarUsuario($id, $matricula, $nome, $senha, $email){
        $result = $this->sql->select(
            "CALL sp_editar_usuario(
                :p_id,
                :p_matricula,
                :p_nome,
                :p_senha,
                :p_email
            );", [
                ":p_id"        => intval($id),
                ":p_matricula" => intval($matricula),
                ":p_nome"      => trim($nome),
                ":p_senha"     => $senha,
                ":p_email"     => trim($email)
            ]
        );

        return $result[0];
    }
}

?>