
<?php
    class Autenticacao extends Conexao{
        private $tabela = 'usuario';
        /** @var PDO */
        private $conexao;
        
        /** @var PDOStatement  */
        private $consulta;
        private $resultados;
        private $email;
        private $senha_login;
        private $erro;
        private $nivel;
        
        
        //Esse método vai alimentar a variável com o valor inserido pelo usuário, já puxando os outros métodos.
        public function botao_login(array $campos) {
            $this->email = strip_tags( trim($campos['email']) );
            $this->senha_login = strip_tags(  trim($campos['senha'])  );
            $this->nivel = (int) $campos['tipoDeUsuario'];
                        
            $this->autentica();
            $this->usuario();
            if(  $campos['tipoDeUsuario'] != $this->getResultado()  ):
                errosDoUsuarioCustomizados("Você é usuário autor e tentou entrar como adm do sistema! Ou vice-versa", CORPF_VERMELHO);
            endif;
        }
        public function getResultado() {
            return $this->resultados;
        }
        public function getErro() {
            return $this->erro;
        }
        
        public function getNivel() {
            return $this->nivel;
        }
        
        public function verificaLogin() {
            if(  empty($_SESSION['autenticado'])  ):
                unset( $_SESSION['autenticado'] );
                return false;
            else:
                return true;
            endif;
        }
        //MÉTODO QUE VERIFICA SE EMAIL E SENHA FORAM PREENCHIDOS.
        private function autentica() {
            if(  !$this->email || !$this->senha_login || !$this->nivel  ):
                $this->erro = [ 'Os campos não podem ser vazios', CORPF_LARANJADO ];
                $this->resultados = false;  
                if(  $this->nivel == ''  ) :
                    $this->erro = ["Escolha o tipo de login: administrador ou usuário-autor!", CORPF_VERMELHO];
                    $this->resultados = false;
                endif;
            elseif(  !$this->usuario()  ):
                $this->erro = ["Senha incorreta ou usuário <b>{$this->email}</b> não existe no sistema.", CORPF_VERMELHO];
                $this->resultados = false;
            elseif(  ctype_upper($this->email) || ctype_upper($this->senha_login)  ):
                $this->erro = ["usuário / senha não podem ser maiúsculos.", CORPF_VERMELHO];
                $this->resultados = false;
            
            else:
                $this->executar();
            endif;
        }
        
        //Esse método verifica a existência do usuário no Banco de Dados.
        private function usuario() {
            $this->conexao = parent::pegarConexao();
            
            $this->consulta = $this->conexao->prepare("SELECT *,id FROM {$this->tabela} WHERE email = :email
             AND nivel = :nivel AND senha = :senha");
            $this->consulta->setFetchMode(PDO::FETCH_ASSOC);
            $this->consulta->bindValue(":email", $this->email,  PDO::PARAM_STR);
            $this->consulta->bindValue(":senha", $this->senha_login, PDO::PARAM_STR);
            $this->consulta->bindValue(":nivel", $this->nivel, PDO::PARAM_INT);
            
            
            $this->consulta->execute();
            //$this->consulta->fetchAll();
            $contagem = $this->consulta->rowCount();
            
            if(  $contagem >= 1  ):
                /*CASO EU NÃO PASSE ESSE ÍNDICE ZERO (fetchAll()[0]), o programa não irá 
                            reconhecer o índice 'usuario' da variável $_SESSION['autenticado'] */
                $this->resultados = $this->consulta->fetchAll()[0];
                return true;
                
            else:
                return false;
            endif;
            //var_dump($this->consulta);            
        }
        private function executar() {
            if(  !session_id()  ):
                session_start();
            endif;
            
            $_SESSION['autenticado'] = $this->resultados;
            $_SESSION['autenticado']['nivel'] = $this->nivel;
            $_SESSION['autenticado']['email'] = $this->email;
            $_SESSION['autenticado']['id'] = $this->resultados['id'];
            $this->erro = ["Olá {$_SESSION['autenticado']['nome']}. Seja bem-vindo!", CORPF_VERDE];
            $this->resultados = true;
        }
    }