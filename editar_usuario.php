<?php
    session_start();
    date_default_timezone_set('Brazil/East');
    require('../../../Config/Conf.inc.php');
?>
<!DOCTYPE html>
<html>
<head> 
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/bot.css" rel="stylesheet" type="text/css"/>
    <link href="css/painel.css" rel="stylesheet" type="text/css"/>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="../../../img/favicon.ico">

    <title>Editar usuário</title>
    
    <style>
        body{
            margin: 1em;
        }
        h1{
            margin: 0 auto;
        }
        h1.titulo_campo{
            margin: 1em 0.1em;
            font-size: 2em;
            font-weight: 500;
        }
        @media(max-width: 640px){
            input.btn_enviar{
                margin: 1em 3.8em;
            }
            
            h1.titulo_campo{
                margin: 1em 2em;
                font-size: 2em;
                letter-spacing: 0.5em;
                font-weight: 500;
            }
            #voltar{
                margin: 1em 3.8em;
                width: 24em;
                letter-spacing: 0.3em;
                font-weight: 600;
            }
        }
    </style>
</head>
<body>
    <header>
        <?php
            if(!class_exists('Autenticacao')):
                errosDoUsuarioCustomizados("Você não pode acessar à essa área por essa caminho.", CORPF_VERMELHO);
                header('Location:../index.php');
                die;
            endif;


            $autentica = new Autenticacao();
            if(!$autentica->verificaLogin()):
                unset($_SESSION['autenticado']);
                header('Location: ../../formulario-login.php?acao=restrito');
                //var_dump($autentica);
            else:
                $usuario = $_SESSION['autenticado'];
            endif;
            if(!class_exists('Autenticacao')):
                errosDoUsuarioCustomizados("Você não pode acessar à essa área por essa caminho.", CORPF_VERMELHO);
                header('Location:../pagInicial.php?acao=naoAutorizado');
                die;
            endif;
            $usuario = filter_input_array(INPUT_POST, FILTER_DEFAULT);
            $id = filter_input(INPUT_GET, 'fl', FILTER_VALIDATE_INT);
            $exclui = filter_input(INPUT_GET, 'exclui', FILTER_VALIDATE_INT);
            
            if(isset($id)):
                $id = $id / 1079 / 720 / 999;
            endif;
            
            if(isset($exclui)):
                $exclui = $exclui / 2011 / 792 / 1991;
            endif;
            
            if(isset($usuario['editar_usuario'])):
                unset($usuario['editar_usuario']);
                require('../modelos/AdmUsuario.class.php');
                
                $admUsuario = new AdmUsuario();
                $admUsuario->executarEdicao($id, $usuario);
                
                if(!$admUsuario->getResult()):
                    errosDoUsuarioCustomizados($admUsuario->getErro()[0], $admUsuario->getErro()[1]);
                else:
                    errosDoUsuarioCustomizados($admUsuario->getErro()[0], $admUsuario->getErro()[1]);
                    echo "<a class='btn_enviar' href='usuarios.php' style='text-decoration: none;'>Veja os usuários.</a>";
                endif;
            else:
                $lerDadosParaEdicao = new Ler();
                $lerDadosParaEdicao->executarLeitura("usuario", "WHERE id = :id", "id={$id}");
                
                if(!$lerDadosParaEdicao->resultado()):
                    header('Location: usuarios.php?msg=false');
                else:
                    $usuario = $lerDadosParaEdicao->resultado()[0];
                endif;
            endif;
            
            /*$img = "IMAGEM EM PNG.png";
            $imFormatada = str_replace(" ", "-", strtolower($img));
            echo $imFormatada;
            echo "<hr>";
            $numero = 1;
            for($contador = 1; $contador <= 10; $contador++):
                echo "<br>".str_pad($contador, 2, 0, STR_PAD_LEFT);
            endfor;*/
        ?>
    </header>
    <main>
        <button id="voltar">Voltar</button>
        <header>
            <h1 class="titulo_campo">Editar usuário</h1>
        </header>
        <article>
            <form action="" name="formulario_usuário" method="post">
                <label>
                    <span class="titulo_campo">Nome</span>
                    <input type="text" class="campos_formulario" placeholder="Nome:" name="nome" value="<?php if(isset($usuario['nome'])): echo $usuario['nome'];  endif; ?>" autofocus>
                </label>

                <label>
                    <span class="titulo_campo">Email</span>
                    <input type="text" class="campos_formulario" placeholder="Email:" name="email" value="<?php if(isset($usuario['email'])): echo $usuario['email'];  endif;?>">
                </label>

                <label>
                    <span class="titulo_campo">Telefone</span>
                    <input type="text" class="campos_formulario" placeholder="telefone:" id="telefone" name="telefone" value="<?php if(isset($usuario['telefone'])): echo $usuario['telefone']; endif; ?>">
                </label>
                <label>
                    <span class="titulo_campo">nome de usuário no site</span>
                    <input type="text" class="campos_formulario" placeholder="usuario:" name="usuario" value="<?php if(isset($usuario['usuario'])): echo $usuario['usuario']; endif; ?>">
                </label>
                <label>
                    <?php if($_SESSION['autenticado']['id'] == $id): ?>
                    <span class="titulo_campo">Senha</span>
                    <input type="password" class="campos_formulario" placeholder="sua senha:" name="senha" value="<?php if(isset($usuario['senha'])): echo $usuario['senha']; endif; ?>">
                </label>
                    <?php endif;?>
                <label>
                    <span class="titulo_campo">Data Nascimento</span>
                    <input type="text" class="campos_formulario" placeholder="data de nascimento: / dia/mês/ano /" id="data_nasc" name="data_nasc" value="<?php if(isset($usuario['data_nasc'])): echo date('d/m/Y H:i:s', strtotime($usuario['data_nasc'])); endif; ?>">
                </label>
                <input type="submit" class="btn_enviar" value="editar usuario" name="editar_usuario">
            </form>
        </article>
    </main>
    <script src="../../../js/jQuery.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.min.js"></script>
    <script>
        document.getElementById("voltar").addEventListener('click',()=>{
           history.back();
        });
        
        
        $("#telefone").mask('(99)9.9999-9999');
        $("#data_nasc").mask('00/00/0000');
        
    </script>
</body>
</html>