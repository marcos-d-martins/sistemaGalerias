<?php
    session_start();
    date_default_timezone_set('Brazil/East');
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Galerias </title>
    <link href="../../../css/painel.css" rel="stylesheet" type="text/css"/>
    <link rel="shortcut icon" href="../../../img/favicon.ico">
</head>
<body>
    <header>
        <h2>   Cabeçalho  </h2>
    </header>
    <main>
        <button id="voltar">voltar</button>
        <article>
            <header></header>
            <section>
                
                <h1>corpo do documento.</h1>
                <a href="cadastrar_galeria.php">Cadastre aqui uma galeria</a>
                
            </section>
        </article>
    </main>
    <script>
        document.getElementById("voltar").addEventListener('click',()=>{
            history.back();
        });
    </script>
</body>
</html>