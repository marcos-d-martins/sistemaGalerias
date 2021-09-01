<?php
    class Conexao{
        private static $host = HOST;
        private static $usuario = USUARIO;
        private static $senha = SENHA;
        private static $banco = BANCO;
        
        /** @var PDO  */
        private static $conexao = null;
        
        private function conectar(){
          try{
              if( self::$conexao == null ):
                $dsn = "mysql:host=" .self::$host. ";dbname=" .self::$banco;
                $options = [ PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8' ];                
                self::$conexao = new PDO($dsn, self::$usuario, self::$senha, $options);
              endif;
          } catch (PDOException $erro) {
              errosNativosCustomiz($erro->getCode(), $erro->getMessage(), __FILE__,__LINE__);
              die;
          }
          
          self::$conexao->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
          return self::$conexao;
        }
        
        
        public function pegarConexao(){
            return $this->conectar();
        }
        
    }