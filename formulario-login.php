<!DOCTYPE html>
<html>
    <?php 
    session_start();
    require('../Config/Conf.inc.php');
?>
    <head>
        <meta name="viewport" content="width=device-width initial-scale=1">
        <meta charset="UTF-8">
        <title>Entre no painel Administrativo</title>
        <link href="painel/escolha/css/estiloDoLogin.css" rel="stylesheet">
        <link href="painel/escolha/css/fonticon.css" rel="stylesheet" type="text/css"/>
        <link href="painel/escolha/css/painel.css" rel="stylesheet" type="text/css"/>
        <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@400;500;700&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
        <link rel="shortcut icon" href="../img/favicon.ico">
        <style>
            .espaco{
                max-width: 1300px;
                width: 100%;
                flex-wrap: wrap;
                margin-bottom: 3em;
                margin-left: auto;
                margin-right: auto;
                display: flex;
                justify-content: center;
            }
            
            .voltar_ao_site{
                text-decoration: none;
                margin: 1em;
                color: #fff;
                //border: 0.2px solid #ccc;
                padding: 1em 6.8em;
            }
            .voltar_ao_site:hover{
                color: #259b40;
                transition: 0.3s;
            }
            header .h1{
                font-family: 'Poppins', sans-serif;
            }
        </style>
    </head>
    <body>
        <header>
            <h1>Gerenciar</h1>
        </header>
        <div class="espaco">
            <a href="../index.php" class="voltar_ao_site">Voltar à página inicial </a>            
        </div>
        <form action="" method="post" class="box">
            <?php
                $autenticar = new Autenticacao();
                
                $campos = filter_input_array(INPUT_POST, FILTER_DEFAULT);
                
                if(  isset($campos['formulario'])  ):
                    $autenticar->botao_login($campos);
                
                    //SE O USUÁRIO ESTIVER AUTENTICADO, COLOCO ELE PARA DENTRO DO PAINEL. 
                    if(  $autenticar->getResultado() && $campos['tipoDeUsuario'] == 3  ):
                        header('Location:painel/pagInicial.php');
                    elseif(  $autenticar->getResultado() && $campos['tipoDeUsuario'] == 1  ):
                        header('Location:painel/pagInicialAutor.php');
                    elseif(  !preg_match('/^[a-z0-9_\.\-]+@[a-z0-9]*\.[a-z0-9_\.\-]+\.[a-z]{2,4}$/', $campos['email'])  ):
                        errosDoUsuarioCustomizados("O email digitado *{$campos['email']}* não é válido.", CORPF_LARANJADO);
                    elseif(  !isset( $_POST['tipoDeUsuario'] )  ):
                        errosDoUsuarioCustomizados("Você deve escolher um tipo de usuário: autor ou administrador.", CORPF_LARANJADO);
                    elseif(  strtolower($campos['email']) != $campos['email'] || strtolower($campos['senha_login']) != $campos['senha_login']  ):
                        
                        errosDoUsuarioCustomizados('Erro: email ou senha com letras maiúsculas.', CORPF_LARANJADO);
                        $this->resultados = false;                    
                    else:
                        errosDoUsuarioCustomizados( $autenticar->getErro()[0], $autenticar->getErro()[1] );
                    endif;
                endif;
            
                $semAcesso = filter_input(INPUT_GET, 'acao', FILTER_DEFAULT);

                if( isset($semAcesso) ):
                    if( $semAcesso == 'restrito' ):
                        errosDoUsuarioCustomizados( "Informe usuário e senha para entrar!", CORPF_VERMELHO );
                    elseif( $semAcesso == 'sair' ):
                        errosDoUsuarioCustomizados("Você saiu! Volte logo.", CORPF_VERDE);
                    endif;
                endif;
            ?>
                
            <h1>Entre</h1>
            <label class="radio"> <input type="radio" name="tipoDeUsuario" value=3 class="radio_button">Administrador</label>
            <label class="radio"> <input type="radio" name="tipoDeUsuario" value=1 class="radio_button">Autor </label>
            
            <input type="text" name="email" placeholder="E-Mail"  autofocus>
            <input type="password" name="senha" placeholder="Senha">
            
            <input type="submit" name="formulario" value="Entrar">
        </form>
    </body>
</html>