<?php
    
    define('PAGINA_INICIAL','https://localhost/SiteGalerias/index/painel/escolha/');

    define('HOST', 'localhost');
    define('USUARIO', 'root');
    define('SENHA', '');
    define('BANCO','sistemagalerias');
    
    
    function carregar_automatico($classe){
        
        $pasta = ['../index/classes','../index/classes/CRUD'];
        $incluir_diretorio = null;
        foreach($pasta as $d):
            if(!$incluir_diretorio && file_exists(__DIR__ . "\\{$d}\\{$classe}.class.php") && !is_dir( __DIR__ . "\\{$d}\\{$classe}.class.php")):
                include (__DIR__ .  "\\{$d}\\{$classe}.class.php");
                $incluir_diretorio = true;
            endif;
        endforeach;
        
        if($incluir_diretorio == false){
            //var_dump($pastas);exit;
            echo "<br><br>A classe <i>{$d}</i>\\<small style='color:blue'>{$classe}.class.php</small> não pode ser inclusa. " . E_USER_ERROR;
        }        
    }    
    spl_autoload_register('carregar_automatico');
    //CONSTANTES DE TRATAMENTO DE ERROS.
    define('CORPF_AMARELO','amarelo');
    define('CORPF_LARANJADO','laranjado');
    define('CORPF_VERMELHO','vermelho');
    define('CORPF_VERDE','verde');
    
    function errosDoUsuarioCustomizados($msg,$numeroErro,$travarCodigo = null){
        /*$css = ( $numeroErro == E_USER_NOTICE ? CORPF_AMARELO:
                   ($numeroErro == E_USER_WARNING ? CORPF_LARANJADO : 
                      ($numeroErro == E_USER_ERROR ? CORPF_VERMELHO :
                           $numeroErro = CORPF_VERDE)));*/
        $css = $numeroErro;
        switch ($css):
            
            case $numeroErro == E_USER_NOTICE:
                $css = CORPF_AMARELO;
                break;
            case $numeroErro == E_USER_WARNING:
                $css = CORPF_LARANJADO;
                break;
            case $numeroErro == E_USER_ERROR:
                $css = CORPF_VERMELHO;
                break;
        endswitch;
        /*  trigger = lanca_erro
             A classe TRIGGER do Robson equivale à minha LANÇA_ERRO. */
        
    //Aqui vai a classe  \trigger\                  Aqui vai \ajax_close\
        echo "<p class=\"lanca_erro {$css}\"><span class=\"erro_customizado\">{$msg}</span></p>";
        if($travarCodigo):
            die;
        endif;
    }
    
    function errosNativosCustomiz($numeroErro,$msg,$arquivo,$linha){
        $estilo = "";
        switch ($estilo):
            case $numeroErro == E_USER_NOTICE:
                $estilo = CORPF_AMARELO;
                break;
            case $numeroErro == E_USER_WARNING:
                $estilo = CORPF_LARANJADO;
                break;
            case $numeroErro == E_USER_ERROR:
                $estilo = CORPF_VERMELHO;
                break;
            default:
                $estilo = $numeroErro;
        endswitch;
        
        echo "<p class='trigger {$estilo}'>";
        echo "<b>Erro na linha: #{$linha} :: </b> {$msg} <br>";
        echo "<small> {$arquivo} </small>";
        echo "<span class='ajax_close'></span></p>";
        
        if($numeroErro == E_USER_ERROR):
            die;
        endif;
    }
    
    
    set_error_handler('errosNativosCustomiz');