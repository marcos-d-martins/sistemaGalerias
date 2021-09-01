<?php
    session_start();
    date_default_timezone_set('Brazil/East');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/bot.css" rel="stylesheet" type="text/css"/>
    <link href="css/painel.css" rel="stylesheet" type="text/css"/>
    <script src=js/sweetalert2.all.min.js" type="text/javascript"></script>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="../../../img/favicon.ico">
    <style>
        .pag_publicacoes{
            width: 100%;
        }
        
        
        .btn_enviar{
            margin: 0.9em 1em;
            border: 2px solid #CCC;
            border-radius: 1px;
            font-size: 0.8em;
            background-color: #00f169;
            font-family: 'Ubuntu',sans-serif;
            padding: 0.2em;
            max-width: 550px;
            width: 10%;
            text-align: center;
            font-size: 0.9em;
            display: block;
        }
        .btn_enviar:hover{
            transition: 0.9s;
            background-color: #002a80;
            color: #fff;
        }
        
        @media(max-width: 640px){
            .btn_enviar{
                max-width: 250px;
                width: 25%;
                padding: 0.2em 60px;
            }
            .pag_publicacoes .listagem_conteudo{
                font-size: 0.6em;
                margin: 1em 4em;
                width: 100%;
                
            }
        }
        #voltar{
            margin: 2em 4em;
        }
        
        
    </style>
    <title>Publicações </title>
    <?php
        require('../../../Config/Conf.inc.php');
        $exclui = filter_input(INPUT_GET, 'exclui', FILTER_VALIDATE_INT);

        if(isset($exclui)):
            $exclui = $exclui / 1079 / 2253 / 3;
            $remover = new Excluir();
            $remover->exclusao( 'publicacao', "WHERE id = :id", "id={$exclui}" );
            if($remover->getResultado()):
                header('Location: publicacoes.php?msg-sucesso');
            else:
                echo "deu erro";
            endif;
        endif;

        $autentica = new Autenticacao();
        if( !$autentica->verificaLogin() ):
            unset($_SESSION['autenticado']);
            header('Location: ../../formulario-login.php?acao=restrito');
            //var_dump($autentica);
        else:
            $usuario = $_SESSION['autenticado'];
        endif;
        $ler = new Ler();
        $pagina = filter_input(INPUT_GET, 'atual', FILTER_VALIDATE_INT);
        $fazrPaginacao = new Paginacao('publicacoes.php?atual=', 'Primeira página', 'Última página', 2);
        $fazrPaginacao->definePaginacao($pagina, 3);


        /* AFINAL, O QUE É ESSE offset do Banco de Dados?! 
            *Offset* é a partir de qual linha da tabela quero trazer. 
            Na verdade, OFFSET trará uma linha depois a linha informada. Por exemplo:
            SELECT * FROM tabela OFFSET 4 = ME RETORNE TUDO DA TABELA A PARTIR DA QUARTA LINHA, 
             OU SEJA, A PARTIR DO QUARTO REGISTRO.
        */
        if( $_SESSION['autenticado']['nivel'] == 3 ):
            $ler->consultaManual("SELECT p.id AS id,
                p.descricao AS descricao,
                p.imagem AS imagem, p.data_publicacao AS data, u.nome AS autor, u.id AS id_usuario
                FROM publicacao p LEFT JOIN usuario u ON u.id = p.id_usuario LIMIT :limit OFFSET :offset",
                "limit={$fazrPaginacao->getLimiteDeDados()}&offset={$fazrPaginacao->getAPartirDeQual()}");
        else:        
            $ler->consultaManual("SELECT p.id AS id,
                p.descricao AS descricao, p.imagem AS imagem,
                p.data_publicacao AS data, u.nome AS autor, u.id AS id_usuario
                FROM publicacao p LEFT JOIN usuario u ON u.id = p.id_usuario WHERE p.id_usuario = {$_SESSION['autenticado']['id']}
                LIMIT :limit OFFSET :offset","limit={$fazrPaginacao->getLimiteDeDados()}&offset={$fazrPaginacao->getAPartirDeQual()}");
        endif;
        
        $falso = filter_input(INPUT_GET, 'msg', FILTER_VALIDATE_BOOLEAN);
        if( $falso ):
            errosDoUsuarioCustomizados("Você tentou editar uma publicação que não existe.", CORPF_VERMELHO);
        endif;
    ?>
</head>
<body class="pag_publicacoes">
    <main>
        <header>
            <?php
                if( $_SESSION['autenticado']['nivel'] == 3 ):
                    echo "<h1><a href='../pagInicial.php' class='btn_enviar' style='text-decoration: none; '>Painel</a>  </h2>";
                else:
                    echo "<h1><a href='../pagInicialAutor.php' class='btn_enviar' style='text-decoration: none; '>Painel</a>  </h2>";
                endif;
            ?>            
        </header>
        <button id="voltar">voltar</button>
        <article>
            <header>
                <?php
                    if( $_SESSION['autenticado']['nivel'] == 1 ):
                        echo "<h1 style=\"font-family: 'Ubuntu',sans-serif; \" class=\"titulo\">Suas publicações.</h1>";
                    else:
                        echo "<h1 style=\"font-family: 'Ubuntu',sans-serif; \" class=\"titulo\">Publicações.</h1>";                    
                    endif;                
                ?>
                <span class="mostrar-usuario-na-sessao">Usuário: <b><?= $_SESSION['autenticado']['nome'];?></b>
                Usuário id: <b><?= $_SESSION['autenticado']['id'] * 795 * 157977 * 235;?> </b>
                </span>
            </header>
            
            <a href="cadastrar_publicacao.php" class="botao_azul">cadastrar nova publicação</a>
            <section>
                <?php
                    if( isset($_GET['msg-sucesso']) ):
                        errosDoUsuarioCustomizados("Registro excluído!", CORPF_VERDE);
                    endif;
                ?>
            </section>
            <section>
                <?php
                    if( $ler->resultado() ):
                        foreach ( $ler->resultado() as $publicacao ) :
                            extract($publicacao);
                ?>

                <article class="listagem_conteudo">
                    <table>
                        <tr class="titulos">
                            <th>Descrição</th>
                            <th>Publicada em</th>
                            <th>Autor</th>
                            <th>capa publicação</th>
                            <th>Galeria cadastrada</th>
                            <th>Ação</th>
                        </tr>
                        <?php /*
                            if($_SESSION['autenticado']['id'] != $publicacao['id_usuario']):
                                echo "id {$publicacao['id_usuario']} não corresponde ao do autor {$_SESSION['autenticado']['id']} ";
                            else:
                                echo "Autor corresponde ao autor autenticado!";
                            endif;*/
                            $verificaGaleria = new Ler();
                            $verificaGaleria->executarLeitura('fotos', "WHERE id_publicacao = :id_post", "id_post={$id}");
                        ?>
                        <tr class="valores">                            
                            <td class="valores"><?= $descricao; ?></td>
                            <td class="valores"><?= date('d/m/Y H:i:s', strtotime($data) ); ?></td>
                            <td class="valores"><?= $autor; ?></td>
                            <td class="valores trazer_imagem"><?= Verificacao::imagens( $imagem,null,200,150 ); ?></td>
                            <?php if( $verificaGaleria->resultado() ): ?>
                                <td class="valores"><?= "Galeria cadastrada nesse post!"?></td>
                            <?php else: ?>
                                <td class="valores"><?= "Não há galeria nesse post." ?></td>
                            <?php endif; ?>
                            <td class="valores">
                                <a href="editar_publicacao.php?v=<?=$id * 1024 * 1024 * 3 ?>" class="botao_editar">Editar</a>
                                <a href="publicacoes.php?exclui=<?=$id * 1079 * 2253 * 3 ?>" class="botao_excluir" id="excluir">Excluir</a>
                            </td>
                            <!--<img src='http://localhost/CorporativoFacil/index/painel/escolha/tim.php?src=http://localhost/CorporativoFacil/index/painel/escolha/uploads/imagens/2020/11/teste-1605244019.jpg&w=250&h=250' alt='foto' title='foto'>-->
                        </tr>
                    </table>
                </article>
                
                <?php
                        endforeach;
                    else:
                        errosDoUsuarioCustomizados("Não há resultados.", CORPF_AMARELO);
                    endif;
                    $fazrPaginacao->executarPaginacao('publicacao');
                    echo $fazrPaginacao->getPaginacao();
                ?>
            </section>
        </article>
    </main>
    <script src="js/jQuery.js"></script>
    <script src="js/enviar-dados.js"></script>
    <script src="js/sweetalert2.all.min.js"></script>
    <script>
        document.getElementById("voltar").addEventListener('click',()=>{
            history.back();
        });
        
        
        $(".botao_excluir").on("click", function(e){
            e.preventDefault();
            
            const destino = $(this).attr('href');
            console.log(destino);
            
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                  confirmButton: 'botao_confirma_js',
                  cancelButton: 'botao_cancela_js'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Quer excluir mesmo?',
                text: "não há como reverter a exclusão.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sim!',
                cancelButtonText: 'Não!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    setTimeout(function(){
                        document.location.href = destino;                        
                    }, 1500);
                    swalWithBootstrapButtons.fire(
                        'Excluído!',
                        'Você excluiu o post..',
                        'success'
                    )
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                      'Cancelado.',
                      'O post continua aqui :)',
                      'error'
                    )
                }
            })
        })

        /*
        $(function(){
            $("table").delegate("td","hover",function(){
                var parent_tr = $(this).parent().children();
            });
        })*/
    </script>
</body>
</html>