<?php
/**
 * Description of Paginacao
 *
 * @author Marcos Daniel
    Un Septembre, deux mille vingt
 */
class Paginacao{
    /**  paginação    */
    private $paginaAtual;
    private $limiteDeDados;
    
    /* Esse é o OFFSET da consulta no banco */
    private $aPartirDeQual;
    /** Realiza a leitura */
    private $tabela;
    private $termos;
    private $colunasDoBanco;
    /** Refina a paginação */
    private $tuplas;
    private $enderecoURL;
    private $linksPorPagina;
    private $primeiraPagina;
    private $ultimaPagina;
    
    /** Renderiza o objeto de paginação */
    private $objDePaginacao;
    
    function __construct($enderecoURL, $primeiraPagina=null, $ultimaPagina=null, $linksPorPagina=null) {
        $this->enderecoURL = (string)$enderecoURL;
        $this->primeiraPagina = ( (string)$primeiraPagina ? $primeiraPagina : 'Primeira página' );
        $this->ultimaPagina = ( (string)$ultimaPagina ? $ultimaPagina : 'Última página' );
        $this->linksPorPagina = ( (int)$linksPorPagina ? $linksPorPagina : 5 );
    }
    
    public function definePaginacao($qualPagina,$limite) {
        $this->paginaAtual = ( (int)$qualPagina ? $qualPagina : 1 );
        $this->limiteDeDados = (int) $limite;
        $this->aPartirDeQual = ($this->paginaAtual * $this->limiteDeDados) - $this->limiteDeDados;
    }
    
    /** Verifica a inexistência de resultados naquela página. */
    public function voltaAPagina() {
        if($this->paginaAtual > 1):
            $numeroPagina = $this->paginaAtual - 1;
            header("Location:{$this->enderecoURL}{$numeroPagina}");
        endif;
    }
    
    public function getPaginaAtual() {
        return $this->paginaAtual;
    }

    public function getLimiteDeDados() {
        return $this->limiteDeDados;
    }

    public function getAPartirDeQual() {
        return $this->aPartirDeQual;
    }

    public function executarPaginacao($tabela,$termos=null,$transformaEmArray=null) {
        $this->tabela = (string)$tabela;
        $this->termos = (string)$termos;
        $this->colunasDoBanco = (string)$transformaEmArray;
        $this->execucao();
    }
    
    public function getPaginacao() {
        return $this->objDePaginacao;
    }
    
    private function execucao() {
        $leitura = new Ler();
        $leitura->executarLeitura($this->tabela, $this->termos, $this->colunasDoBanco);
        $this->tuplas = $leitura->getRowCount();
        
        if($this->tuplas   >   $this->limiteDeDados):
            $paginas = ceil($this->tuplas / $this->limiteDeDados);
            $qtdMaximaDeLinks = $this->linksPorPagina;
            
            $this->objDePaginacao = "<ul class=\"paginacao\">";
            $this->objDePaginacao .= "<li><a title=\"{$this->primeiraPagina}\" class=\"paginas\" href=\"{$this->enderecoURL} 1\">{$this->primeiraPagina}</a></li>";
                /*Pega a página atual menos o máximo de links e incrementa 
                    para poder criar da primeira página até a página atual da paginação */
                for($estaPag = $this->paginaAtual - $qtdMaximaDeLinks; $estaPag <= $this->paginaAtual - 1; $estaPag++):
                    if($estaPag >= 1):
                        $this->objDePaginacao .= "<li><a class='paginas' title=\"Página {$estaPag}\" href=\"{$this->enderecoURL}{$estaPag}\">{$estaPag}</a></li>";
                    endif;
                endfor;
                
                $this->objDePaginacao .= "<li><span class='pagina_atual'>{$this->paginaAtual}</span></li>";
                
                for($proximaPag = $this->paginaAtual + 1; $proximaPag <= $this->paginaAtual + $qtdMaximaDeLinks; $proximaPag++):
                    if($proximaPag >= 1):
                        $this->objDePaginacao .= "<li><a class='paginas' title=\"Página {$proximaPag}\" href=\"{$this->enderecoURL}{$proximaPag}\">{$proximaPag}</a></li>";
                    endif;
                endfor;
            $this->objDePaginacao .= "<li><a class='paginas' title=\"{$this->ultimaPagina}\" href=\"{$this->enderecoURL}{$paginas}\">{$this->ultimaPagina}</a></li>";
            $this->objDePaginacao .= "</ul>";
        endif;
    }
}