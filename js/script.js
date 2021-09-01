        
        //mostrando o diretório atual em JavaScript
        /*
            alert(window.location.pathname);
        */
        /*
        var dados = $(this).serialize();
        $.post('../../ajax/ajax.php',{descricao: 'Vai-dar-certo', conteudo: 'a vida vai dar certo. Foco e produtividade estão bem poucos, mas vai dar certo.'},function(dado){
            console.log(dado);
            
        });
        
        */
        //Para onde estou enviando  // o quê estou enviando //No parâmetro da function(), vão os dados a serem retornados pelo PHP.
        /*$.post('editar_categoria.php', dadosCategorias, function(){
            console.log(dadosCategorias);
        });        
        return false;*/        
$(function(){
    
    $(".abrir-formulario-js").click(function(){
        
        $(this).toggleClass('fechar-formulario');
        $("."+$(this).attr('rel')).slideToggle();
    });
    
    
    
    $(".ajax_enviar").submit(function(){
        var formulario = $(this);
        var dados = $(this).serialize();
        
        
        $.ajax({
            url: "../ajax/ajax.php",
            data: dados,
            type: 'POST',
            dataType: 'json',
            beforeSend: function () {                
                formulario.find(".gif-2-formulario").fadeIn(500);
                $(".exibe-msg").fadeOut(500,function(){
                    $(this).remove();
                });
            },
            success: function (respostaPHP) {
                console.log(respostaPHP);
                
                if(respostaPHP.erro){                    
                    formulario.find('.caixa-de-erro').html("<div class='exibe-msg err-msg'>"+ respostaPHP.erro +"</div>");                    
                    formulario.find('.err-msg').fadeIn();
                }else{
                    formulario.find('.caixa-de-erro').html("<div class='exibe-msg success-msg'>"+ respostaPHP.sucesso +"</div>");
                    formulario.find('.success-msg').fadeIn();
                }
                
                formulario.find('.gif-2-formulario').fadeOut(500);
            }
        });
        return false;
    });
        
});