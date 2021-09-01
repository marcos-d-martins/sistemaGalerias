<?php
/**
 * Classe responsável por manipular e validar dados do sistema.
 * @copyright (c) 2020, Marcos.
 * @author Marco Daniel
 */
class Verificacao {
    private static $dados;
    private static $formato;
    
    private $erro;
    
    public function getErro(){
        return $this->erro;
    }
    
    public static function validadorDeEmail($email) {
        self::$dados = (string) $email;
        self::$formato = '/[a-z0-9_\.\-]+@[a-z0-9_\.\-]*[a-z0-9_\.\-]+\.[a-z]{2,4}$/';
        
        if(preg_match(self::$formato, self::$dados)):
            return self::$dados;
        else:
            echo "Formato do email inválido.";
        endif;
    }
    
    public static function validadorDeTelefone($telefone) {
        self::$dados = (string) $telefone;
        self::$formato = '/\([0-9]{2}\)9.[0-9]{4}\-[0-9]{4}$/';
        
        if(preg_match(self::$formato, self::$dados)):
            return self::$dados;
        else:
            echo "Telefone no formato inválido.";
        endif;
    }
        
    //'���������������������������������������������������������������Rr"!@#$%&*()_-+={[}]/?;:.,\\\'<>���'
    //'aaaaaaaceeeeiiiidnoooooouuuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr                                 '
    
    public static function tratamentoDeStrings($texto) {
        self::$formato = array();
        self::$formato['a'] = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜüÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿRr"!@#$%&*()_-+={[}]/?;:.,\\\'<>°ºª';
        self::$formato['b'] = 'aaaaaaaceeeeiiiidnoooooouuuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr';
    
        self::$dados = strtr(utf8_decode($texto), utf8_decode(self::$formato['a']), self::$formato['b']);
        self::$dados = strip_tags(trim(self::$dados));
        self::$dados = str_replace(' ', '-', self::$dados);
        self::$dados = str_replace(array('-----','----','---','--'), '-', self::$dados);
        
        return strtolower(utf8_encode(self::$dados));
    }
    public static function datas($datas) {
        //função nativa explode(): transforma uma STRING num ARRAY.
        self::$formato = explode(' ', $datas);        
        //DISSECANDO O MÉTODO explode()  :
        //Exemplo:
        /*  variável $texto = "vai dar tudo certo. Vou superar esses malditos problemas.";
        explode(' ',$texto);
        explode => $texto[0] = "vai";
        explode => $texto[1] = "dar";
        explode => $texto[2] = "tudo";
        explode => $texto[3] = "certo.";
        explode => $texto[4] = "Vou";
        explode => $texto[5] = "superar";
        explode => $texto[6] = "esses";
        explode => $texto[7] = "malditos";
        explode => $texto[8] = "problemas";*/
        //var_dump(self::$formato);
        self::$dados = explode('/', self::$formato[0]);
        //echo "<hr>"; echo "<pre>"; var_dump(self::$formato);exit;
        if(empty(self::$formato[1])):
            self::$formato[1] = date('H:i:s');
        endif;
        
        self::$dados = self::$dados[2] . '-' . self::$dados[1] . '-' . self::$dados[0] . ' ' . self::$formato[1];
        //var_dump(self::$dados);
        return self::$dados;
    }
    
    public static function formataDados($frase,$limite,$posicionador=null) {
        self::$dados = strip_tags(trim($frase));
        self::$formato = (int) $limite;
        
        $conjuntoPalavras = explode(' ',self::$dados);
        $qtdPalavras = count($conjuntoPalavras);
        $palavrasCortadas = implode(' ', array_slice($conjuntoPalavras, 0, self::$formato));
        
        
        $posicionador = ( empty($posicionador) ? '...' : ' ' . $posicionador );
        $resultado = ( self::$formato < $qtdPalavras ? $palavrasCortadas . $posicionador : $frase );
        
        return $resultado;
        //echo "<pre>";var_dump($conjuntoPalavras, $qtdPalavras, $palavrasCortadas);
    }
    
    /*Método que, alimentado pela classe Read, filtra as categorias  por nome.   */
    public static function categPorNome($categoria) {
        $ler = new Ler();
        $ler->exeRead('categorias', 'WHERE titulo = :titulo', "titulo={$categoria}");
        if($ler->getRowCount()):
            return $ler->resultado()[0]['id'];
        else:
            echo "Categoria <small style='color:green'>{$categoria}</small> não encontrada.";
            die;
        endif;
    }
    
    public static function usuariosAtivos() {
        $horarioAgora = date('Y-m-d H:i:s');
        $excluirUsuario = new Excluir();
        $excluirUsuario->exclusao('ws_siteviews_online', 'WHERE online_endview < :horarioAgora', "horarioAgora={$horarioAgora}");
        //echo "<pre>"; var_dump($excluirUsuario);exit;
        
        $usuariosAtivos = new Ler();
        $usuariosAtivos->executarLeitura('ws_siteviews_online');
        return $usuariosAtivos->getRowCount();
    }
    
    public static function imagens($diretorioFoto,$nome_foto=null,$largura=null,$altura=null) {
        
        self::$dados = 'uploads/' . $diretorioFoto;
        
        if(  file_exists(self::$dados) && !is_dir(self::$dados)  || $diretorioFoto == 'null'):
            $caminhoFoto = PAGINA_INICIAL;
            $imagem = self::$dados;
            //return $caminhoFoto . $imagem;
            return "<img src='{$caminhoFoto}/tim.php?src={$caminhoFoto}/{$imagem}&w={$largura}&h={$altura} alt='{$nome_foto}' title='{$nome_foto}'>";
        else:
            var_dump($diretorioFoto);
            echo "Erro na foto ou <b>post sem foto cadastrada.</b>";
            //var_dump($diretorioFoto);
        endif;
    }
    
}