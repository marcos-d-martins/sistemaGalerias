<?php
    session_start();
    require '../../../Config/Conf.inc.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <title> Usuários </title>
        <link href="css/bot.css" rel="stylesheet" type="text/css"/>
        <link href="css/painel.css" rel="stylesheet" type="text/css"/>
        <link rel="shortcut icon" href="../../../img/favicon.ico">
        
        <style>
            body{
                background-color: #213C93;
            }
            
            .usuarios_sistema{
                padding: 15px;
            }
            
            @media(max-width: 640px){
                .valores{
                    max-width: 100%;
                }
            }
            
        </style>
    </head>
    
    <?php
        function d($x){
            return $x * 2;
        }
        function e($x){
            return $x * 5;
        }
        
        function f($x){
            if($x < 1){
                return 0;
            }else{
                echo  4 + f($x - 1);
            }
        }
        
        function g($y){
            return 10 + f($y) + e($y) + d($y);
        }
        
        
        echo g(5);
    
    /*
    $v1 = $v2 = 0;
    $v1 += 20;
    $v2 += 30;
    var_dump($v1);
    var_dump($v2);
    
    $v1 *= 2;
    $v2 *= 3;
    var_dump($v1);
    var_dump($v2);
    
    $v1 += 10;
    $v2 += 20;
    var_dump($v1);
    var_dump($v2);
    
    $v1 *= 3 + 5;
    $v2 *= 3 + 5;
    // $v1 = 155 $v2 = 275
    var_dump($v1);
    var_dump($v2);
    */
    /*   $b = 10;
        $j = 6;
        $n = 1;
        for($i = 1; $i <= $j; $i++):
            $n = $b * $n;
            echo $n . " " . executa($n);
        endfor;
    
        function executa($n){
           $i = 0;
           $n1 = $n;
           
           while($n1 > $n):
               $i += 1;
               $n1 = $n1/2;
           endwhile;
           
           return $i;
        }*/
    
    
    
        $autenticado = new Autenticacao();
        if(!$autenticado->verificaLogin()):
            unset($_SESSION['autenticado']);
            header('Location: ../../formulario-login.php?acao=restrito');
        else:
            $usuario = $_SESSION['autenticado'];
        endif;        
        
        $usuarios = new Ler();
        $usuarios->executarLeitura("usuario");
        
        $falso = filter_input(INPUT_GET, 'msg', FILTER_VALIDATE_BOOLEAN);
        
        
        $exclui = filter_input(INPUT_GET, 'exclui', FILTER_VALIDATE_INT);
        
        if(isset($exclui)):
            $exclui = $exclui / 792 / 2011 / 1991;
            var_dump($exclui);
            $remove = new Excluir();
            $remove->exclusao('usuario', "WHERE id = :id", "id={$exclui}");
            if($remove->getResultado()):
                header('Location: usuarios.php?msg-sucesso');
            else:
                errosDoUsuarioCustomizados("Erro ao excluir", CORPF_LARANJADO);
            endif;
        endif;
    ?>
    <body>
        <main>
            <header style="margin-top: 1em;">
                <?php
                    if(  $_SESSION['autenticado']['nivel'] == 3  ):
                        echo "<h1><a href='../pagInicial.php' class='voltar_ao_painel' style='text-decoration: none;' >Painel</a>  </h1>";
                    else:
                        echo "<h1><a href='../pagInicialAutor.php' class='voltar_ao_painel' style='text-decoration: none;' >Painel</a>  </h1>";
                    endif;                    
                    
                ?>
                <span class="mostrar-usuario-na-sessao">Usuário: <b><?= $_SESSION['autenticado']['nome']; ?></b>                        
                </span>
                
            </header>
            
            <article>
                <header class="titulo_usuarios">
                    <h1>Usuários</h1>
                </header>
                <section class="mostrar-msg-resultado">                    
                    <?php
                        if(  isset($_GET['msg-sucesso'])  ):
                            errosDoUsuarioCustomizados("Usuário excluido.", CORPF_VERDE);
                        endif;
                    ?>
                </section>
                <?php
                
                    if(  isset($falso)  ):
                        errosDoUsuarioCustomizados("Esse usuário não existe.", CORPF_VERMELHO);
                    endif;
                    if(  $usuarios->resultado()  ):
                        foreach( $usuarios->resultado() AS $autoresOuAdm ):
                            extract($autoresOuAdm);
                ?>
                
                <section class="pag_usuarios">
                    <table style="width: 100%;">
                        <tr class="titulos">
                            <th>Nome</th>
                            <th>Email</th>
                            <th>Telefone</th>
                            <th>Usuário</th>
                            <th>Nível</th>
                            <th>Publicações</th>
                        <?php if(  $id == $_SESSION['autenticado']['id']  ):  ?>
                            <th>Ação</th>
                        <?php endif; ?>
                        </tr>
                        <tr class="valores">
                            <td class="valores"><?= $nome; ?></td>
                            <td class="valores"><?= $email; ?></td>
                            <td class="valores"><?= $telefone; ?></td>
                            <td class="valores"><?= $nivel = (  $nivel == 3 ? 'Usuário administrador' : 'Autor'  ); ?></td>
                            <td>
                                <?php
                                    $leitura = new Ler();
                                    $leitura->consultaManual("SELECT p.id AS id_publicacao,
                                        p.descricao AS publicacao,
                                        p.id_usuario AS id_autor, us.id AS autor,
                                        us.nome AS nome_autor FROM publicacao p 
                                        LEFT JOIN usuario us ON p.id_usuario = us.id 
                                        WHERE us.id = p.id_usuario GROUP BY p.id, p.id_usuario HAVING p.id_usuario = {$autoresOuAdm['id']}");
                                ?>
                                <select name="publicacoesDeCadaAutor">
                                    <option value="null">Publicações desse autor: </option>
                                    <?php
                                        if(  $leitura->resultado()  ):
                                            foreach( $leitura->resultado() AS $publicacoesPorAutor ):
                                                echo "<option value=\"{$publicacoesPorAutor['id_autor']}\" style=\"font-family: Verdana;\"><a href=\"publicacoes.php\">&rsaquo; {$publicacoesPorAutor['publicacao']}</a> </option>";
                                            endforeach;
                                        else:
                                            echo "<option value='null' disabled>Esse usuário não tem publicações ainda.</option>";
                                        endif;
                                    ?>
                                </select>
                            </td>
                            <?php 
                                if(  $id >= 2  ):
                            ?>
                            <td class="acoes">
                                <a href="editar_usuario.php?fl=<?=$id * 1079 * 720 * 999?>" class="botao_editar">Editar</a>
                                <a href="usuarios.php?exclui=<?=$id * 792 * 2011 * 1991?>" class="botao_excluir">Excluir</a>
                            </td>
                            <?php
                                endif;                                
                            ?>
                        </tr>
                    </table>
                </section>
                
                <?php
                        endforeach;
                    endif;
                ?>
            </article>
        </main>
        <script src="js/jQuery.js" type="text/javascript"></script>
        <script src="js/sweetalert2.all.min.js" type="text/javascript"></script>
        <script type="text/javascript">
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
                confirmButtonText: 'sim!',
                cancelButtonText: 'Não!',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    setTimeout(function(){
                        document.location.href = destino;                        
                    }, 1500);
                    swalWithBootstrapButtons.fire(
                        'Excluído!',
                        'Usuário excluído..',
                        'success'
                      )
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                      'Cancelado.',
                      'O usuário continua na lista.',
                      'error'
                    )
                }
            })
        })

        </script>
    </body>
</html>
