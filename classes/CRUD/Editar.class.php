<?php

class Editar extends Conexao{
    private $tabela;
    private $dados;
    private $termos;
    private $campos;
    private $resultado;
    /** @var PDOStatement  */
    private $objEdicao;
    /** @var PDO */
    private $objConexao;
    
    public function  executarEdicao($tabela, array $dados, $termos, $transformaEmString) {
        $this->tabela = (string) $tabela;
        $this->termos = (string) $termos;
        $this->dados = $dados;
        parse_str($transformaEmString, $this->campos);
        
        $this->consulta();
        $this->execucao();
    }
    
    public function Resultados() {
        return $this->resultado;
    }
    
    //PRIVADOS    
    private function conectaBanco() {
        $this->objConexao = parent::pegarConexao();
        $this->objEdicao = $this->objConexao->prepare($this->objEdicao);
    }
    
    private function consulta() { /*
            É preciso usar um laço de repetição FOREACH() porque se não o programa só irá alterar UM campo.
        Para alterar TODOS os campos duma tabela, é preciso usar o laço FOREACH().
         */
        foreach($this->dados AS $campos => $valorDoCampo):
            $cadaCampoDaTabela[] = $campos . ' = :' . $campos;
            //echo "{$campos} => {$valorDoCampo}<br>";
        endforeach;
        $cadaCampoDaTabela = implode(', ', $cadaCampoDaTabela);
        $this->objEdicao = "UPDATE {$this->tabela} SET {$cadaCampoDaTabela} {$this->termos}";
        /*
            É importante ressaltar que $campos foi denominado $valorDoCampo para que
            o nome das colunas sejam iguais aos nomes dos campos do formulário, 
            e não iguais AOS VALORES DOS CAMPOS DO FORMULÁRIO.
         */
    }
    
    public function getRowCount() {
        return $this->objEdicao->rowCount();
    }
    private function execucao() {
        $this->conectaBanco();
        try{
            $this->resultado = true;            
            $this->objEdicao->execute(array_merge($this->dados, $this->campos));
        } catch (PDOException $erro) {
            $this->resultado = null;
            errosDoUsuarioCustomizados($erro->getMessage(), $erro->getCode());
        }
    }
}
