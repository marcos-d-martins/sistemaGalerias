<!DOCTYPE html>
<html>
<?php
    session_start();
    date_default_timezone_set('Brazil/East');
?>
<head>
    <?php
        require ('../../../Config/Conf.inc.php');

        /* VERIFICA A AUTENTICAÇÃO (SESSÃO) do usuário. 
            Se o usuário não entrou no sistema e tenta acessar sem estar autenticado, 
            o sistema o joga pra fora.
         */
        $autentica = new Autenticacao();

        if(!$autentica->verificaLogin()):
            unset($_SESSION['autenticado']);
            header('Location: ../../formulario-login.php?acao=restrito');
        else:
            $usuario = $_SESSION['autenticado'];
        endif;
        if(!class_exists('Autenticacao')):
            errosDoUsuarioCustomizados("Você não pode acessar à essa área por essa caminho.", CORPF_VERMELHO);
            header('Location:../pagInicial.php?acao=naoAutorizado');
            die;
        endif;
    ?>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/bot.css" rel="stylesheet" type="text/css"/>
    <link href="css/painel.css" rel="stylesheet" type="text/css"/>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;500;600&display=swap" rel="stylesheet">

    <link rel="shortcut icon" href="../../../img/favicon.ico">
    <title>Cadastrar post</title>
    <style>
        .gif_formulario{
            display: none;
            vertical-align: middle;
        }
        
        body{
            background: #FFE000;
        }
        
        input [type="file"]{
            color: #fff;
            background: #fff;
        }
       .mostrar-usuario-na-sessao{
            position: absolute;
            top: 2px;
            right: 0.5em;
            font-size: 1em;
            padding: 0.8em;
        }
        
        @media(max-width:717px){
            #voltar{
                width: 25%;
            }            
        }
        @media(max-width:515px){
            .botao_painel{
                margin-left: 0.3em;
            }
        }
        
        
        .botao_painel{
            padding: 1em 2.5em;
            background-color: #006699;
            color: #fff;
            font-size: 0.7em;
            margin: 1.8em;
            text-decoration: none;
            box-shadow: 2px 5px #000;
            text-shadow: 2px 2px #000;
        }
        
        .botao_painel:hover{
            padding: 1em 7.3em;
            color: #000;
            background-color: #fff;
            transition: 0.6s;
            text-shadow: 1px 1px #000;
            margin: 1em 2em;
            max-width: 1200px;
            width: 100%;
        }
        .btn_enviar_rascunho{
            margin: 1em 0.3em;
            padding: 0.5em 1em;
            background: #006699;
            color: #fff;
        }
        .btn_enviar{
            margin: 1em 0.3em;
            padding: 0.5em 1em;
            background: #20b426;
            color: #fff;
        }
        @media(max-width:515px){
            .btn_enviar{
               margin: 0; 
            }
        }
        @media(max-width:500px){
            .btn_enviar{
               margin: 0 auto;
               max-width: 1300px;
               padding: 0 2.5em;
            }
        }
    </style>
</head>
<body>
    <header>

        <?php
            /*CAPTURA OS DADOS DO FORMULÁRIO E CHAMA O OBJETO DE CADASTRO.
             */
            $posts = filter_input_array(INPUT_POST, FILTER_DEFAULT);
            
            if(  isset($posts) && $posts['mandar_formulario']  ):
                $posts['post_status'] = ( $posts['mandar_formulario'] == 'cadastrar' ? '0' : '1' );
                $posts['imagem'] = ( $_FILES['imagem']['tmp_name'] ? $_FILES['imagem'] : 'null' );
                var_dump($posts);
                if($posts['post_status'] == 'cadastrar'):
                    errosDoUsuarioCustomizados("Rascunho salvo!", CORPF_VERDE);
                endif;
                
                unset($posts['mandar_formulario']);
                var_dump($posts);
                
                 //OBJETO DE CADASTRO.
                require '../modelos/AdmPublicacoes.class.php';
                $publica = new AdmPublicacoes();
                $publica->executaCadastro($posts);
                
                if(!empty($_FILES['fotos']['tmp_name'])):
                    $imagens = new AdmPublicacoes();
                    $imagens->enviarGaleria($_FILES['fotos'], $publica->getResult());
                endif;
                // FIM DE TRATAMENTO DE MÍDIAS.
                
                if($publica->getResult()):
                    errosDoUsuarioCustomizados($publica->getErro()[0], $publica->getErro()[1]);
                    echo "<a href='publicacoes.php' style='text-decoration: none;'>Clique para ver todas as publicações.</a>";
                else:
                    errosDoUsuarioCustomizados("Erro ao cadastrar: <b>{$publica->getErro()[0]}</b>", $publica->getErro()[1]);
                endif;
            endif;
        ?>
    </header>
    <main class="formulario">
        <article>
            <button id="voltar">Voltar</button>
            <header>
                <?php 
                    if($_SESSION['autenticado']['nivel'] == 3):
                        echo "<h2><a href='../pagInicial.php' class='botao_painel'>Painel</a>  </h2>";
                    else:
                        echo "<h2><a href='../pagInicialAutor.php' class='botao_painel'>Painel</a>  </h2>";                
                    endif;
                ?>
                <h2 class="titulo_campo">Cadastrar Publicação</h2>
            </header>
            <!-- enctype=multipart/form-data SERVE PARA O PROGRAMA INTERPRETAR ARQUIVOS de mídias.-->
            <section class="cadastra_post">
                <span class="mostrar-usuario-na-sessao">Usuário: <br><b><?= $_SESSION['autenticado']['nome'];?></b><br>
                Usuário id: <b><?= $_SESSION['autenticado']['id'] * 795 * 157977 * 235;?> </b>
                </span>
                <form name="cadastra_publicacao" method="post" enctype="multipart/form-data">
                    <div class="mostra-msg"></div>
                    <label class="t_campo_arq">
                        <span class="titulo_campo">Carregar capa da galeria(JPEG ou PNG)</span>
                        <input  type="file" class="campos_formulario_arquivo" name="imagem" accept="image/png,image/jpeg">
                    </label>
                    <label>
                        <span class="titulo_campo">Nome da publicação</span>
                        <input type="hidden" name="id_usuario" value="<?= $_SESSION['autenticado']['id']; ?>">
                        <input type="text" name="descricao" class="campos_formulario" autofocus value="<?php if(isset($posts['descricao'])):echo $posts['descricao']; endif;?>">
                    </label>
                    <!--<label>
                        <!--<span class="titulo_campo">Validador</span>-->
                        <!--<input type="hidden" name="acao" class="campos_formulario" value="cadastro">-->
                    <!--</label>-->
                    <!--<label>
                        <span class="titulo_campo">Conteúdo</span>
                        <textarea class="textarea_content" name="conteudo"> </textarea>
                    </label>-->
                    <label class="t_campo_arq">
                        <span class="titulo_campo">Carregar fotos da galeria(JPEG ou PNG)</span>
                        <input type="file" class="campos_formulario_arquivo" name="fotos[]" multiple accept="image/png,image/jpeg">
                    </label>
                    <!--
                    <span class="titulo_campo">[opcional] Incluir arquivo (música/vídeo, etc.) </span>
                    <input  type="file" multiple class="campos_formulario_arquivo" name="arquivo[]" accept="audio/mpeg,video/mp4" value="">

                    <span class="titulo_campo">[opcional] Incluir documento(.PDF, .DOCX)</span>
                    <input  type="file" multiple class="campos_formulario_arquivo" name="documento[]" accept=".doc, .docx, application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" value="">
                    -->
                    <label>
                        <span class="titulo_campo">Data</span>
                        <input type="text" class="campos_formulario" name="data_publicacao" value="<?= date('d/m/Y H:i:s') ?>">
                    </label>

                    <button class="btn_enviar_rascunho" value="cadastrar" name="mandar_formulario">enviar</button>
                    <button class="btn_enviar" value="cadastrar e publicar" name="mandar_formulario">enviar & Publicar</button>
                    <img class="gif_formulario" src="../../../img/ajax-loader.gif" alt="CARREGANDO..." title="CARREGANDO...">
                </form>
            </section>
        </article>
    </main>
    <footer>
        <script src="js/jQuery.js"></script>
        <script src="js/enviar-dados.js"></script>
        <script src="__jsc/tiny_mce/tiny_mce.js"></script>
        <script src="__jsc/tiny_mce/plugins/tinybrowser/tb_tinymce.js.php"></script>
        <script src="__jsc/admin.js"></script>
        <script>
            tinymce.init({
              selector: '#publicar',
              language: 'pt_BR'
            });
        </script>
        <script>
            //BOTÃO para voltar uma página
            document.getElementById("voltar").addEventListener('click', () => {
                history.back();
            });
            
            
        </script>
    </footer>
</body>
</html>