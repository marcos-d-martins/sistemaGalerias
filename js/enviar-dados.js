$(function(){
    
    $(".abrir-formulario-js").click(function(){
        
        $(this).toggleClass('fechar-formulario');
        $("."+$(this).attr('rel')).slideToggle();
    });
    
    $(".ajax_enviar").submit(function(){
        var dados = $(this).serialize();
        console.log(dados);
        var esteFormulario = $(this);
        
        $.ajax({
            url: "../ajax/ajax.php",
            data: dados,
            type: 'POST',
            dataType: 'json',
            beforeSend: function () {
                $('.gif_formulario').fadeIn(500);
                $('.exibe-msg').fadeOut(500, function(){
                   $(this).remove();
                });
            },
            success: function (respostaPHP) {
                $(".mandar_formulario_ajax").on("click", function(){
                    $(this).css("bottom","40px");
                });
                console.log(respostaPHP);
                
                if(respostaPHP.erro){
                    $(".caixa-de-erro").html('<div class="exibe-msg err-msg">'+ respostaPHP.erro +'</div>');
                    $(".err-msg").fadeIn();
                }else{
                    $('.caixa-de-erro').html('<div class="exibe-msg success-msg">' + respostaPHP.deu_certo + '</div>');
                    $('.success-msg').fadeIn();
                    $('input[class!="sem_classe"]').val('');
                    $('input[type!="hidden"]').val('');
                }
                
                $('.gif_formulario').fadeOut(500);
            }
        });
        
        return false;
    });
    
    
    
    $(".editar-e-excluir-publicacao").submit(function(){
        var dados = $(this).serialize();
                
        $.ajax({
            url: 'edicaoAjax.php',
            data: dados,
            type: 'GET',
            dataType: 'json',
            beforeSend: function () {
                
            },
            success: function (paramResposta) {
                console.log(paramResposta);
            }
        });
        return false;
    });
});