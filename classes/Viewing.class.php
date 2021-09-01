<?php
/**
 * Description of Viewing
 *
 * @author Marcos Daniel
 * @copyright September 6, 2020.
 */
class Viewing {
    private static $dados;
    private static $keys;
    private static $valor;
    public static $modelo;
    
    /**  Recebe o modelo como STRING e carrega ela no método nativo *file_get_contents()*. */
    public static function loadTemplate($modelo){
        self::$modelo = (string) $modelo;
        self::$modelo = file_get_contents(self::$modelo . '.tpl.html');
        
    }
    
    public static function showData(array $dados) {
        self::setKeys($dados);
        self::setValues();
        self::showView();
    }
    
    public static function requisicao($file, array $dados) {
        extract($dados);
        require("{$file}.inc.php");
    }
    
    //PRIVATES
    /** Esse método irá capturar os campos do Banco de Dados*/
    private static function setKeys($dados) {
        self::$dados = $dados;
        self::$keys = explode('&','#'.implode('#&#', array_keys(self::$dados)).'#');
        
        var_dump(self::$keys);
    }
    /** Esse método irá capturar os valores contidos em cada coluna do Banco de Dados*/
    private static function setValues() {
        self::$valor = array_values(self::$dados);
    }
    
    private static function showView() {
        echo str_replace(self::$keys, self::$valor, self::$dados);
    
    }
    
    
    
}
