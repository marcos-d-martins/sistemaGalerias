<!DOCTYPE html>
<html lang="pt-br">
<?php
    session_start();
?>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="css/bot.css" rel="stylesheet" type="text/css"/>
    <link href="css/painel.css" rel="stylesheet" type="text/css"/>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="icone/windows-close.png">
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/bootstrap-datetimepicker.min.css">
    <link rel="stylesheet" type="text/css" href="shadowbox/shadowbox.css">
    <link rel="stylesheet" type="text/css" href="css/photoswipe.css">
    <script type="text/javascript" src="js/shadowbox.js"></script>
    <script src="https://kit.fontawesome.com/e5e59e1fb1.js" crossorigin="anonymous"></script>
    <script type="text/javascript">
        Shadowbox.init({
            handleOversize: "drag",
            modal: true
        });
    </script>
    
    <style>
        body{
            background: #cce5ff;
        }
        
        /*.galeria{
            display: inline;
            position: relative;
            width: 100%;
            height: auto;
            //background: #00254A url(../../../img/empresa1.png) center center no-repeat;
            background-size: 100%;
            padding: 0.5em 1em;
            left:0;
            margin: 15px 0.5em;
        }*/
        
        .galeria-botao-excluir{
            position: relative;
            right: 13.7em;
            top: 1.2em;
            height: 1.7em;
            width: 1.7em;
            //background-color: #000;
        }
        .galeria-botao-excluir:hover{
            background-color: #330099;
            transition: 0.5s;
            border-radius: 50em;
        }
        
        .ticar_foto{
            position: relative;
            right: 4.1em;
            top: 1.2em;
            height: 1.2em;
            width: 1.2em;
        }
        
        .btn_enviar{
            margin: 1.2em auto;
        }
        .botao_galeria{
            display: inline-block;
            background-color: #006dcc;
            color: #fff;
        }
        
        
        .menu_superior{
            max-width: 1000px;
            width: 86%;
            padding: 20px;
            background-color: #000;
            display: flex;
            margin: 0 auto;
        }
    </style>
    
    
    <title>Editar publicação</title>
</head>
<body class="editar_post">
    <header>        
        <?php
            date_default_timezone_set('Brazil/East');
            require('../../../Config/Conf.inc.php');
            
            if( !class_exists('Autenticacao') ):
                errosDoUsuarioCustomizados("Você não pode acessar à essa área por essa caminho.", CORPF_VERMELHO);
                header('Location:../index.php');
                die;
            endif;
            
            $autentica = new Autenticacao();
            
            if( !$autentica->verificaLogin() ):
                unset($_SESSION['autenticado']);
                header('Location: ../../formulario-login.php?acao=restrito');
            else:
                $usuario = $_SESSION['autenticado'];
            endif;
            
            /** CAPTURAR ID DA PUBLICAÇÃO NO ARQUIVO  publicacao.php?id=id  */
            $id = filter_input(INPUT_GET,'v', FILTER_VALIDATE_INT);
            echo "<p>Publicação id " . $id . "</p>";
           
            if( isset($id) ):
                $id = $id / 1024 / 1024 / 3;
            endif;
            /**/
            
            
            /** CAPTURAR DADOS DO FORMULÁRIO. */
            $publicacao = filter_input_array(INPUT_POST, FILTER_DEFAULT);
            
            if(  isset($publicacao) && $publicacao['editar_publicacao']  ):
                $publicacao['imagem'] = ( $_FILES['imagem']['tmp_name'] ? $_FILES['imagem'] : 'null' );
                unset($publicacao['editar_publicacao']);
                
                require('../modelos/AdmPublicacoes.class.php');
                
                echo "<span class='titulo_campo'>Foto da capa atual</span>";
                $admPublicacoes = new AdmPublicacoes();
                $admPublicacoes->executaEdicao($publicacao, $id);
                
                /**    INSERIR NOVAS FOTOS À GALERIA.    */
                if(  !empty( $_FILES['fotos']['tmp_name'] )  ):
                    $enviarGaleria = new AdmPublicacoes();
                    $enviarGaleria->enviarGaleria( $_FILES['fotos'], $id );
                    
                    if(  $enviarGaleria->getResult()  ):
                        errosDoUsuarioCustomizados("A galeria foi atualizada! {$enviarGaleria->getQtdGaleria()} imagens enviadas à galeria", CORPF_VERDE);
                        
                        $buscarCapa = new Ler();
                        $buscarCapa->executarLeitura('publicacao', "WHERE id = :id", "id={$id}");
                        
                        $publicacao['imagem'] = (  $_FILES['imagem']['tmp_name'] ?  $_FILES['imagem']['tmp_name'] : $buscarCapa->resultado()[0]['imagem']  );                        
                   
                        Verificacao::imagens($publicacao['imagem'], $publicacao['descricao'], 200,200);                        
                    endif;
                    
                endif;
                /* ESSE BLOCO SERVE PARA MODIFICAR APENAS A GALERIA (adicionar ou excluir DELA), 
                    ALTERANDO OU NÃO ALGUM OUTRO CAMPO. */
                if(  isset( $_FILES['fotos']['tmp_name']) && $publicacao == '' ||  isset( $_FILES['fotos']['tmp_name'] ) && $publicacao['imagem'] != ''  ):
                    $ng = new AdmPublicacoes();
                    $ng->enviarGaleria( $_FILES['fotos'], $id );
                    
                    if(  $ng->getResult()  ):
                        $publicacao['imagem'] = (  $_FILES['imagem']['tmp_name'] ?  $_FILES['imagem']['tmp_name'] : $buscarCapa->resultado()[0]['imagem']  );                        
                       
                        Verificacao::imagens($publicacao['imagem'], $publicacao['descricao'], 200,200);                        
                        errosDoUsuarioCustomizados( "Galeria atualizada! {$ng->getQtdGaleria()} enviadas.", CORPF_VERDE );
                    endif;
                endif;
                
                if(  $admPublicacoes->getResult()  ):
                    errosDoUsuarioCustomizados($admPublicacoes->getErro()[0], $admPublicacoes->getErro()[1]);
                    echo "<a href='publicacoes.php' style='text-decoration: none;'>Clique para ver todas as publicações.</a>";
                endif;
                /*
                $existeCadastro = filter_input(INPUT_GET, 'existeCadastro', FILTER_VALIDATE_BOOLEAN);
                if(isset($existeCadastro) && empty($admPublicacoes)):
                    
                endif;                */
            else:
                //Nesse bloco é quando tentei atualizar uma categoria que não existe.
                //header('Location:publicacoes.php?msg=false');
                $lerDadosParaEdicao = new Ler();
                $lerDadosParaEdicao->executarLeitura('publicacao', "WHERE id = :id", "id={$id}");
                
                if(  $lerDadosParaEdicao->resultado()  ):
                    /* 
                      Bem aqui, peguei a variável $publicacao e reescrevi a mesma, 
                        alimentando-a com os dados da leitura no Banco de Dados(AFINAL, É UM FORMULÁRIO DE EDIÇÃO, 
                        OU SEJA, ME TRARÁ OS DADOS JÁ PRONTOS PARA EDITAR ALGUM CAMPO) ao invés 
                        de cadastrar novamente.
                     */
                    $publicacao = $lerDadosParaEdicao->resultado()[0];
                    $publicacao['data_publicacao'] = date('d/m/Y H:i:s', strtotime( $publicacao['data_publicacao']) );
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
        <header>
            <p class="titulo_campo">Editar publicação</p>            
        </header>
        
        <section>
            <nav class="menu">
                
                <ul class="menu_editar_publicacao">
                    <li class="usuario">Usuário:<?= $_SESSION['autenticado']['nome'];?></li>
                    
                    <li>
                        <?php
                            if(  $_SESSION['autenticado']['nivel'] == 3  ):
                                echo "<a href='../pagInicial.php'>Painel</a>";
                            else:
                                echo "<a href='../pagInicialAutor.php'>Painel</a>";
                            endif;
                        ?>
                    </li>
                    
                    <li><a href="#" id="voltar">Voltar</a></li>
                </ul>
            </nav>
        </section>
        <input type='button' name="verifica" class="ticar_foto" onclick="selecionar()" value="Selecionar todas">
        <input type='button' name="verifica" class="ticar_foto" onclick="desmarcar()" value="Desmarcar">
        <?php
            /*  CAPTURAR id DE CADA FOTO DA GALERIA A FIM DE IDENTIFICAR CADA UMA(ENUMERAR POR id),
                   E EXCLUIR AQUELA EM QUE O USUÁRIO CLICAR.  */
            $imagemGaleria = filter_input(INPUT_GET, 'imagemGal', FILTER_VALIDATE_INT);
            $postId = filter_input(INPUT_GET, 'postid', FILTER_VALIDATE_INT);
            
            $dadosDoBanco = filter_input_array( INPUT_POST, FILTER_DEFAULT );
            
            if(  isset($postId)  ):
                $postId = $postId / 9378 / 825 / 14;
 
                require_once '../modelos/AdmPublicacoes.class.php';
                $excluir = new AdmPublicacoes();
                $excluir->atualizarGaleria($imagemGaleria);
                                
                /*  eSSE BLOCO É RESPONS[AVEL POR TRATAR FOTOS DA GALERIA E A CAPA DA MESMA.  */
                if(  $excluir->getResult()  ):
                    errosDoUsuarioCustomizados($excluir->getErro()[0], $excluir->getErro()[1]);
                    
                    echo "<a href='publicacoes.php' style='text-decoration: none;'>Clique para ver todas as publicações.</a>";
                    
                    $lerDadosParaEdicao = new Ler();
                    $lerDadosParaEdicao->executarLeitura( 'publicacao', "WHERE id = :id", "id={$postId}" );
                    
                    $galerias = new Ler();
                    $galerias->executarLeitura( 'fotos', "WHERE id_publicacao = :id", "id={$postId}" );
                    $gb = 0;
                        
                    if(  $galerias->resultado()  ):
                        foreach( $galerias->resultado() AS $galeria ):
                            $gb++;
                ?>
                <div class="moldura_galeria">
                    <div class="galeria"><?= Verificacao::imagens( $galeria['foto'], 
                            $publicacao['descricao'].'-'.
                            $gb, 200, 185 ); ?></div>

                    <a href="editar_publicacao.php?postid=<?= $id*9378*825*14; ?>&imagemGal=<?= $galeria['id'];?>" class="imagem j_delete">
                        <img src="icone/cancel.png" alt="excluir" title="<?= $galeria['id']; ?>" class="galeria-botao-excluir">
                    </a>
                    <input type='checkbox' name="verifica" id="verifica" class="ticar_foto">
                </div>
                
                <?php
                        endforeach;
                    endif;
                    
                    if(  $lerDadosParaEdicao->resultado()  ):
                        $dadosDoBanco = $lerDadosParaEdicao->resultado()[0];
                        $dadosDoBanco['data_publicacao'] = date('d/m/Y H:i:s', strtotime( $dadosDoBanco['data_publicacao']) );
                                          
                    else:
                        $dadosDoBanco = $lerDadosParaEdicao->resultado()[0];
                        $dadosDoBanco['data_publicacao'] = date('d/m/Y H:i:s', strtotime( $dadosDoBanco['data_publicacao']) );
                    endif;                    
                    
                endif;
                
            endif;
            
            ?>
                
        <section>
            
            <form name="formulario_publicacoes" method="post" enctype="multipart/form-data" onsubmit="return deleteMultip();">
                <header>
                    <p>PARA CADASTRAR UMA GALERIA, INSIRA UMA CAPA.</p>
                </header>
                
                <input type="hidden" name="id_usuario" value="<?= $_SESSION['autenticado']['id']; ?>">
                
                <span class="titulo_campo">Capa atual:</span>

                <div>
                     <?php if(  isset($publicacao['imagem'])  ): echo Verificacao::imagens( $publicacao['imagem'], $publicacao['descricao'], 400,200 ); else: echo Verificacao::imagens( $dadosDoBanco['imagem'], $dadosDoBanco['descricao'], 400,200 ); endif;?> 
                </div>

                <div>
                    <header>
                        <h3>
                            <label class="titulo_campo">Galeria</label>
                        </h3>
                        
                    </header>
                    
                    <input type="file" class="campos_formulario_arquivo" multiple="multiple" name="fotos[]" accept="image/png,image/jpeg">                    
                    <?php
                        $gb = 0;
                        $trazerGaleria = new Ler();
                        $trazerGaleria->executarLeitura( 'fotos', "WHERE id_publicacao = :id_post", "id_post={$id}" );

                        if(  $trazerGaleria->resultado()  ):
                            foreach( $trazerGaleria->resultado() AS $galeria ):
                                $gb++;
                    ?>
                    <div class="galeria">
                        <div class="moldura_galeria"><?= Verificacao::imagens( $galeria['foto'], $publicacao['descricao'].'-'.$gb, 200, 185 ); ?></div>
                        
                        <a href="editar_publicacao.php?postid=<?= $id*9378*825*14; ?>&imagemGal=<?= $galeria['id'];?>" class="imagem j_delete">
                            <img src="icone/cancel.png" alt="excluir" title="<?= $galeria['id']; ?>" class="galeria-botao-excluir" id="zoom">
                        </a>
                        <input type='checkbox' name="verifica" id="verifica" class="ticar_foto">
                    </div>
                    
                    <?php
                            endforeach;
                        endif;
                    ?>
                    <input type="checkbox" name="verifica" value="selecionar todas">
                    <input type="checkbox" name="verifica" value="desmarcar todas">
                        
                    <?php  
                        $selecionouFotos['verifica'] = filter_input_array(INPUT_POST, FILTER_DEFAULT);
                        $contador = 0;
                        
                        if(  isset($selecionouFotos)  ):
                            var_dump($selecionouFotos);
                        endif;
                    ?>
                </div>
                                
                <label> <span class="titulo_campo">Nome da publicação</span> </label>
                
                <input type="hidden" name="id_usuario" value="<?= $_SESSION['autenticado']['id']; ?>">
                <input type="text" name="descricao" class="campos_formulario" autofocus value="<?php if(  isset($publicacao['descricao'])  ): echo $publicacao['descricao']; else: echo $dadosDoBanco['descricao'];  endif;?>">
                
                <span class="titulo_campo">Carregar nova capa</span>
                <input type="file" name="imagem">
                                
                <label>
                    <span class="titulo_campo">Criada em:</span>
                </label>
                <div class="input-group date data_formato" data-date-format="dd-mm-YYYY">                    
                    <input type="text" id="data_post" class="campos_formulario form-control" placeholder="informe data e hora" name="data_publicacao" value="<?php if(  isset($publicacao['data_publicacao'])  ): echo $publicacao['data_publicacao']; else: echo $dadosDoBanco['data_publicacao']; endif;?>">
                    
                </div>

                <label>
                    <span class="titulo_campo">Autor</span>
                    <select name="id_usuario" class="titulo_campo_selecoes">
                        <option value="null">Selecione um autor</option>
                        <?php
                            $lerAutor = new Ler();
                            $lerAutor->consultaManual("SELECT DISTINCT u.id AS id_usuario,
                                p.id_usuario AS id_usuario_publicacao, u.nome AS autor 
                                FROM usuario u LEFT JOIN publicacao p ON u.id = p.id_usuario
                                WHERE u.id = {$_SESSION['autenticado']['id']}");

                            if(  !$lerAutor->resultado()  ):
                                 echo "<option disabled='disabled'>Não há autores</option>";
                            else:
                                foreach( $lerAutor->resultado() AS $autores ):
                                    echo "<option value=\"{$autores['id_usuario']}\" ";
                                    if(  $autores['id_usuario'] == $publicacao['id_usuario'] || $dadosDoBanco['id_usuario'] == $autores['id_usuario']  ):
                                        echo " selected=\"selected\" ";
                                    endif;

                                    echo "><b> &rsaquo; </b>{$autores['autor']}</option>";
                                endforeach;
                            endif;
                            
                            if(  $publicacao['id_usuario'] != $_SESSION['autenticado']['id']  ):
                                $lerAutor = new Ler();
                                $lerAutor->consultaManual("SELECT DISTINCT u.id AS id_usuario,
                                    p.id_usuario AS id_usuario_publicacao, u.nome AS autor 
                                    FROM usuario u LEFT JOIN publicacao p ON u.id = p.id_usuario
                                    WHERE u.id = p.id_usuario");
                                if(  $lerAutor->resultado()  ):
                                    foreach( $lerAutor->resultado() AS $au ):
                                        echo "<option value=\"{$au['id_usuario_publicacao']}\" ";
                                        if(  $au['id_usuario_publicacao'] == $publicacao['id_usuario']  ):
                                            echo " selected=\"selected\" ";
                                        endif;

                                        echo "><b> &rsaquo; </b>{$au['autor']}</option>";
                                    endforeach;
                                endif;
                            endif;
                        ?>
                    </select>
                </label>
                
                <input type="submit" class="btn_enviar" value="Editar publicacao" name="editar_publicacao">
            </form>
        </section>
    </main>
    <!--<script async src="js/barra_carregamento.js"></script>
    <script async src="js/barra_carregar_capa.js"></script>-->
    <script src="js/jQuery.js"></script>
    <script src="js/jquery.form.min.js"></script>
    <script src="js/bootstrap-datepicker.min.js"></script>
    <script src="js/locales/bootstrap-datetimepicker.pt-BR.js"></script>
    <script src="js/funcoes.js"></script>
    <script src="js/jquery.mask.min.js"></script>
    <script src="js/sweetalert2.all.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/photoswipe.min.js"></script>
    <script src="js/photoswipe-ui-default.min.js"></script>
    <script src="js/inicializa-photoswipe.js"></script>
    <script type="text/javascript">
        /*tinymce.init({
          selector: '#publicar',
          language: 'pt_BR'
        });  */
        
        function zoomIn(){
            var GFG = document.getElementById("zoom");
            var alturaAtual = GFG.clientHeight;
            
            GFG.style.height = (alturaAtual + 80) + "px";
        }
        
        function seleciona(){
                
                var elm = document.getElementsByName('verifica');
                
                for(  var i=0; i < elm.length; i++  ){
                    
                    if(  elm[i].type=='checkbox'  )
                        elm[i].checked=true;
                }
        }
        
        
        function desmarcar(){
            var ele = document.getElementsByName('verifica');
            
            for( var i=0; i<ele.length; i++ ){
                
                if(  ele[i].type=='checkbox'  )
                    ele[i].checked=false;
            }
        } 
        
        
        var slides = [
            // slide 1
            {
                src: 'path/to/image1.jpg', // path to image
                w: 1024, // image width
                h: 768, // image height

                msrc: 'path/to/small-image.jpg', // small image placeholder,
                                // main (large) image loads on top of it,
                                // if you skip this parameter - grey rectangle will be displayed,
                                // try to define this property only when small image was loaded before

                title: 'Image Caption'  // used by Default PhotoSwipe UI
                                        // if you skip it, there won't be any caption

                // You may add more properties here and use them.
                // For example, demo gallery uses "author" property, which is used in the caption.
                // author: 'John Doe'
            },
            // slide 2
            {
                src: 'path/to/image2.jpg', 
                w: 600,
                h: 600
                // etc.
            }
        ];        
        
        $("#data_post").mask('99/99/0000 00:00:00');
    
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
                if (  result.isConfirmed  ) {
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
        
        
        $(function(){
            $(".excluir-item-galeria").click(function(){
                console.log($(this));
                $(this).slideDown(100, function(){
                    $("html, body").animate({scrollTop: $(this).offset().top}, 500)
                });
            });
            
        });
    </script>
</body>
</html>