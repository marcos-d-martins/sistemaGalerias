<?php
class Ler extends Conexao{
    
    private $consulta;
    private $colunasBancoEmString;
    //private $campos;
    //private $valorInserido;
    private $resultados;    
    /* Esse objeto receberá a consulta do Banco de Dados e os dados já tratados.
     * @var PDOStatement
     */
    private $objLeitura;
    private $conexaoPDO;
    
    public function executarLeitura($tabela,$termos=null, $transformaEmArray=null) {
        if(  !empty($transformaEmArray)  ):
            parse_str($transformaEmArray, $this->colunasBancoEmString);
        endif;
        if(  isset($termos)  ):
            $this->consulta = "SELECT * FROM {$tabela} {$termos}";
        else:
            $this->consulta = "SELECT * FROM {$tabela}";
        endif;        
        $this->execucao();
    }
    
    /* Retornará o resultado da consulta no Banco. Ou retornará falso ou retornará o último ID inserido na tabela. */
    public function resultado() {
        return $this->resultados;
    }
    public function getRowCount() {
        return $this->objLeitura->rowCount();
    }
    
    public function consultaManual($consulta,$transformaEmArray = null) {
        if(  !empty($transformaEmArray)  ):
            parse_str( $transformaEmArray, $this->colunasBancoEmString );
        endif;
        
        $this->consulta = (string) $consulta;
        $this->execucao();
    }
    
    /* O objeto de conexão DESTA classe recebe a conexão com o Banco de Dados da CLASSE PAI. 
        $objetoDeConexao = recebe BANCO DE DADOS.
        SELECT * FROM tabela = recebe BANCO DE DADOS->prepare(SELECT * FROM tabela, APONTANDO QUAL O BANCO)
    */
    private function conectaBanco(){
        $this->conexaoPDO = parent::pegarConexao();
        $this->objLeitura = $this->conexaoPDO->prepare($this->consulta);
        $this->objLeitura->setFetchMode(PDO::FETCH_ASSOC);
            //var_dump($this->objLeitura);
    }
    
    private function consulta() {
        if(  $this->colunasBancoEmString  ):
            foreach( $this->colunasBancoEmString as $vinculo => $valorDosCampos ):
                if(  $vinculo == 'limit' || $vinculo == 'offset'  ):
                    $valorDosCampos = (int) $valorDosCampos;
                endif;
                $this->objLeitura->bindValue( ":{$vinculo}", $valorDosCampos,( is_int($valorDosCampos) ? PDO::PARAM_INT : PDO::PARAM_STR ) );
                //var_dump($this->colunasBancoEmString);
            endforeach;
        endif;
    }
    private function execucao(){
        $this->conectaBanco();
        try{
            $this->consulta();
            $this->objLeitura->execute();
            $this->resultados = $this->objLeitura->fetchAll();
        } catch (PDOException $erro) {
            $this->resultados = null;
            errosDoUsuarioCustomizados("<b>Erro ao ler:</b> {$erro->getMessage()}", $erro->getCode());
        }
    }
    
}