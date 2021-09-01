<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Excluir
 *
 * @author Marcos Daniel
 */
class Excluir extends Conexao{
    private $tabela;
    private $termos;
    private $campos;
    private $resultado;
    private $primeiroid;
    private $ultimoId;
    /** @var PDOStatement */
    private $objetoExclusao;
    /** @var PDO */
    private $objConexao;
    
    public function exclusao($tabela,$termos=null,$transformaEmString=null) {
        $this->tabela = (string) $tabela;
        $this->termos = (string) $termos;
        
        parse_str($transformaEmString, $this->campos);
                
        $this->consultaDeExclusao();
        $this->executa();
    }
    
    public function excluirSelecionando($tabela,$primeiroId, $ultimoId) {
        $this->tabela =  (string) $tabela;
        
        $this->primeiroid = $primeiroId;
        $this->ultimoId = $ultimoId;

        $this->excluirVarias();
        $this->executa();
    }
    
    public function getResultado() {
        return $this->resultado;
    }
    
    private function conectar() {
        $this->objConexao = parent::pegarConexao();
        $this->objetoExclusao = $this->objConexao->prepare($this->objetoExclusao);
    }
    private function consultaDeExclusao() {
        $this->objetoExclusao = "DELETE FROM {$this->tabela} {$this->termos}";
    }
    
    private function excluirVarias(){
        $this->objetoExclusao = " DELETE FROM {$this->tabela} WHERE id IN({$this->primeiroid}) AND {$this->ultimoId}";
    }
    
    public function getRowCount() {
        return $this->objetoExclusao->rowCount();
    }
    
    private function executa() {
        $this->conectar();
        try{
            $this->objetoExclusao->execute($this->campos);
            $this->resultado = true;
        } catch (PDOException $erro) {
            $this->resultado = null;
            errosDoUsuarioCustomizados( "Erro ao excluir registro:{$erro->getMessage()}", $erro->getCode() );
        }
    }
}
