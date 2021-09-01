<!DOCTYPE html>
<html>
<?php
    session_start();
    date_default_timezone_set('Brazil/East');
    
    require('../../../Config/Conf.inc.php');
    
    $insere = new Inserir();
    $editar = new Editar();
    $camposFormulario = filter_input_array(INPUT_POST, FILTER_DEFAULT);
    
    
    $autentica = new Autenticacao();
    if(!$autentica->verificaLogin()):
        unset($_SESSION['autenticado']);
        header('Location: ../../formulario-login.php?acao=restrito');
        //var_dump($autentica);
    else:
        $usuario = $_SESSION['autenticado'];
    endif;
    
?>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/bot.css" rel="stylesheet" type="text/css"/>
    <link href="css/painel.css" rel="stylesheet" type="text/css"/>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@400;500;700&display=swap" rel="stylesheet">
    <title>Categorias </title>
    
    <style>
        .titulo_categoria{
            display: flex;
            justify-content: flex-start;
            padding: 0;
            margin: 0;
        }
    </style>
</head>
<body class="pag_categorias">
    <header>
        <?php 
            if($_SESSION['autenticado']['nivel'] == 3):
                echo "<h2><a href='../pagInicial.php' class='btn_enviar' style='text-decoration: none; '>Painel</a>  </h2>";
            else:
                echo "<h2><a href='../pagInicialAutor.php' class='btn_enviar' style='text-decoration: none; '>Painel</a>  </h2>";
            endif;          
        ?>
    </header>
    <main class="formulario">
        <button id="voltar">voltar</button>
            <span class="mostrar-usuario-na-sessao">Usuário: <b><?= $_SESSION['autenticado']['usuario'];?></b> |
            Usuário id: <b><?= $_SESSION['autenticado']['id'] * 795 * 157977 * 235;?> </b>
            </span>
        <article class="ajax_enviar">
            <header></header>               
            <?php 
                $falso = filter_input(INPUT_GET, 'msg',FILTER_VALIDATE_BOOLEAN);
                if(isset($falso)):
                    errosDoUsuarioCustomizados("Você tentou editar uma categoria que não existe.", CORPF_VERMELHO);
                endif;
            ?>
        </article>
        <section>
            <header style="font-family: 'Ubuntu',sans-serif">
                <h1>
                    Categorias.
                </h1>
            </header>

            <a href="cadastrar_categoria.php" class="botao_azul" style="text-decoration: none;">Cadastre aqui uma categoria</a>            
            <article class="ajax_enviar">
                <?php

                    $tituloCategoria = filter_input(INPUT_GET, 'titulo', FILTER_DEFAULT);
                    if(isset($tituloCategoria)):
                        errosDoUsuarioCustomizados($tituloCategoria, CORPF_VERDE);
                    endif;

                    $leitura = new Ler();
                    $leitura->consultaManual("SELECT DISTINCT ca.id AS id_cat,"
                            . " ca.titulo AS titulo, ca.conteudo AS conteudo "
                            . " FROM categorias ca LEFT JOIN publicacao p ON p.id_categoria = ca.id "
                            . " ORDER BY id_cat");
                    //var_dump($ler); exit;
                    if($leitura->resultado()):
                        foreach($leitura->resultado() as $categoriasCadastradas):
                            extract($categoriasCadastradas);
                ?>
                    <table>
                        <tr>
                            <th class="titulos">Título da categoria</th>
                            <th class="titulos">Conteúdo</th>
                            <!--<span class="titulo">Publicações nessa categoria</span>-->
                            <th class="titulos">Ação</th>
                        </tr>

                        <tr>
                            <td class="valores"><?= $titulo; ?></td>
                            <td class="valores"><?= $conteudo; ?></td>
                            <?php 
                                /* AQUI vai a listagem de publicações por categoria. 
                                 Ex: Vai listar todas as publicações que estão têm categoria_id = 1 
                                 */
                                $trazerPublicacoesPorCat = new Ler();
                                $trazerPublicacoesPorCat->consultaManual("SELECT *,  "
                                        . " descricao,p.id_categoria AS id_cat "
                                        . " FROM publicacao p LEFT JOIN categorias ca ON ca.id = p.id_categoria"
                                        . " WHERE p.id_categoria = ca.id");
                                if($trazerPublicacoesPorCat->resultado()):
                                    foreach($trazerPublicacoesPorCat->resultado() AS $publicacoesEmCada):
                            ?>
                            
                            <?php endforeach;
                            endif; ?>
                            <td><a href="editar_categoria.php?fl=<?=$id_cat *749 *785 * 999;?>" class="valores">Editar categoria</a></td>
                        </tr>
                    </table>
                    <?php
                        endforeach;
                    else:
                        errosDoUsuarioCustomizados("Não há nenhum registro.", CORPF_LARANJADO);
                    endif;
                ?>
            </article>
        </section>
    </main>
    
    <script src="js/jQuery.js" type="text/javascript"></script>
    <!--<script src="../../../js/script.js" type="text/javascript"></script>-->
    <script src="js/enviar-dados.js" type="text/javascript"></script>
    <script>
        document.getElementById("voltar").addEventListener('click',()=>{
            history.back();
        });
    </script>
</body>
</html>