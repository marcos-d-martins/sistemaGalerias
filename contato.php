<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Entre em contato conosco.</title>
        <link rel="stylesheet" href="../css/tratamentoDeErros.css">
        <link rel="stylesheet" href="../css/fontawesome.min.css">
        <link href="painel/escolha/css/painel.css" rel="stylesheet" type="text/css"/>
        <link rel="shortcut icon" href="../img/empresa1.png">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.min.js"></script>        
        <style>
            .formulario-contato{
                display: block;
                margin: 0 auto;
            }
            .titulo{
                max-width: 600px;
                min-width: 320px;
                width: 100%;
                top: 0;
            }            
            
            .to-landing-page{
                text-decoration: none;
                margin: 0 auto;
                padding: 1em;
                background: #08c;
                color: #fff;
                font-family: 'Verdana';
                max-width: 100px;
                display: flex;
            }
            .to-landing-page:hover{
                transition: 0.6s;
                background-color: #ccc;
                color: black;
                
            }
            
            form h1{
                margin-top: 1em;
            }
            .botao-enviar{
                margin-top: 1em;
            }
            body{
                text-align: center;
                font-family: 'Verdana';
            }
            
        </style>
    </head>
    <body>
        <?php

            $telefone = '(19)9-9963.9339';

            function mascaraTelefone($telefone){
                if(preg_match('/^\([0-9]{2}\)9\-[0-9]{4}\.[0-9]{4}$/', $telefone)){
                    echo "expressão regular correta!";
                }
                else{
                    echo "expressão incorreta.";
                }
            }


            echo mascaraTelefone($telefone);
        ?>
        <!--<div class="card-columns"></div>-->
        <a href="../index.html" class="to-landing-page">Página inicial</a>
        <form action="guardar_contato.php" method="post" name="formulario_contato" >
                <h1>página contato</h1>
                <label class="titulo">Nome:</label>
                <input type="hidden" name="id">
                <input type="text" name="nomePessoa" class="formulario-contato" autofocus>

                <label class="titulo">Email:</label>
                <input type="text" name="email" class="formulario-contato">

                <label class="titulo">Telefone:</label>
                <input type="text" id="telefone" name="telefone" class="formulario-contato">

                <label class="titulo">Mensagem:</label>
                <textarea placeholder="Conte-nos algo..." class="formulario-contato"></textarea>
                <button type="submit" class="botao-enviar"> Enviar </button>
        </form>
        <script type="text/javascript">
            $('#telefone').mask("(00) 0.0000-0000");
        </script>
    </body>
</html>
