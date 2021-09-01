<?php
    session_start();
    date_default_timezone_set('Brazil/East');
    
    $autentica = new Autenticacao();
    if(!$autentica->verificaLogin()):
        unset($_SESSION['autenticado']);
        header('Location: ../../formulario-login.php?acao=restrito');
        //var_dump($autentica);
    else:
        $usuario = $_SESSION['autenticado'];
    endif;
?>
<!DOCTYPE html>
<html>
<head> 
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="../../../css/painel.css" rel="stylesheet" type="text/css"/>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@400;500;700&display=swap" rel="stylesheet">

    <title>Cadastrar galeria</title>
</head>
<body>
    <header>
        <?php 
            $galerias = filter_input_array(INPUT_POST, FILTER_DEFAULT);
            if(isset($galerias)):
                var_dump($galerias);
            endif;
        ?>
    </header>
    <main>
        <?php 
            if($_SESSION['autenticado']['nivel'] == 3):
                echo "<h2><a href='../pagInicial.php' class='btn_enviar' style='text-decoration: none; '>Painel</a>  </h2>";
            else:
                echo "<h2><a href='../pagInicialAutor.php' class='btn_enviar' style='text-decoration: none; '>Painel</a>  </h2>";
            endif;
        ?>
        <header>
            <h1 class="titulo_campo">cadastrar Galeria</h1>
        </header>
        <form name="formulario_galerias" method="post">
            <label>
                <span class="titulo_campo">Título da galeria</span>
                <input type="text" name="titulo" class="campos_formulario" autofocus>
            </label>

            <label>
                <span class="titulo_campo">conteúdo</span>
                <textarea class="textarea_conteudo" name="conteudo"></textarea>
            </label>

            <label>
                <span class="titulo_campo">Data</span>
                <input type="text" class="campos_formulario" name="data_hoje" value="<?= date('d/m/Y H:i:s');?>">
            </label>

            <input type="submit" class="btn_enviar" value="cadastrar galeria" name="cadastrar_galeria">
        </form>
    </main>
    <script>
        document.getElementById("voltar").addEvent('click',()=>{
            history.back();
        });
    </script>
</body>
</html>