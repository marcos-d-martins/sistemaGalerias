<?php
class Inserir extends Conexao{
    
    private $dados;
    private $tabela;
    private $resultados;    
    /* @var PDOStatement 
     * Esse objeto receberá a consulta do Banco de Dados e os dados já tratados.
     */
    /**
     *
     * @var PDOStatement
     */
    private $objInsercao;
    private $conexaoPDO;
    /** 
     * <b>executarInsercao</b>
     * @param STRING $tabela = tabela do Banco de Dados.
     * @param array $dados = informe um array atribuitivo. ( Nome da coluna => valor ) 
     */
    public function executarInsercao($tabela, array $dados) {
        $this->tabela = (string)$tabela;
        $this->dados = $dados;        
        //$this->conectaBanco();
        $this->consulta();
        $this->execucao();
        
    }
    
    /* Retornará o resultado da consulta no Banco. Ou retornará falso ou retornará o último ID inserido na tabela. */
    public function getResult() {
        return $this->resultados;
    }
    
    public function tuplasInseridas() {
        return $this->objInsercao->rowCount();
    }
    
   
    /* O objeto de conexão DESTA classe recebe a conexão com o Banco de Dados da CLASSE PAI. 
        $objetoDeConexao = recebe BANCO DE DADOS.
        SELECT * FROM tabela = recebe BANCO DE DADOS->prepare(SELECT * FROM tabela, APONTANDO O BANCO)
    */
    private function conectaBanco(){
        $this->conexaoPDO = parent::pegarConexao();
        $this->objInsercao = $this->conexaoPDO->prepare($this->objInsercao);
    }
    private function consulta() {
        //INSERT INTO nome_tabela(campo1, campo2, campo3etc.) VALUES(:valor1,:valor2,:valor3);
        $campos = implode(', ', array_keys($this->dados));
        $valorInserido = ':'. implode(', :', array_keys($this->dados));
        $this->objInsercao = "INSERT INTO {$this->tabela} ({$campos}) VALUES ({$valorInserido})";
    }
    public function consultaManual($ManualInsert){
        $this->objInsercao = (string) $ManualInsert;
        $this->execucao();
    }
    private function execucao() {
        $this->conectaBanco();
        try{
            $this->objInsercao->execute($this->dados);
            //var_dump($this->objInsercao);
            $this->resultados = $this->conexaoPDO->lastInsertId();
        } catch (Exception $erro) {
            $this->resultados = null;
            errosDoUsuarioCustomizados("<b>Erro ao cadastrar:</b>{$erro->getMessage()}", $erro->getLine());
        }
    }
    
}