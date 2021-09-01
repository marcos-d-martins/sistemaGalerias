/*function triggerClick(){
         document.querySelector('#profileImage').click();
     }


 function displayImage(e){
     if(e.files[0]){
         var reader = new FileReader();

         reader.onload = function(e){
             document.querySelector("#profileDisplay").setAttribute('src',e.target.result);
         };
         reader.readAsDataURL(e.file[0]);
     }
}*/
$(function(){
    $(".imagem").click('.j_delete', function(e){
        var gallery_id = $(this).attr('rel');
        $.ajax({
            url: "../../ajax/ajax.php",
            data: {action: 'delete', gallery_id: gallery_id},
            type: 'POST',
            dataType: 'json',
            success: function (data) {
                if(data.erro){
                    alert("Erro ao excluir galeria. Recarregue a página!");
                }else{
                    $('.'+gallery_id).fadeOut(400);
                }
            }
        });
    });
});


        $(function () {
            $('#submitButton').click(function (ev) {
                ev.preventDefault();
                $('#uploadForm').ajaxForm({
                    target: '#outputImage',
                    url: 'uploadFile.php',
                    beforeSubmit: function () {
                          $("#outputImage").hide();
                           if($("#uploadImage").val() == "") {
                               $("#outputImage").show();
                               $("#outputImage").html("<div class='botao_cancela_js'>Escolha um arquivo!</div>");
                        return false;
                    }
                    console.log($(this));
                        $("#progressDivId").css("display", "block");
                        var percentValue = '0%';

                        $('#progressBar').width(percentValue);
                        $('#percent').html(percentValue);
                    },
                    uploadProgress: function (event, position, total, percentComplete) {
                        var percentValue = percentComplete + '%';
                        $("#progressBar").animate({
                            width: '' + percentValue + ''
                        }, {
                            duration: 5000,
                            easing: "linear",
                            step: function (x) {
                            percentText = Math.round(x * 100 / percentComplete);
                                $("#percent").text(percentText + "%");
                            if(percentText == "100") {
                                   $("#outputImage").show();
                            }
                            }
                        });
                        },
                    error: function (response, status, e) {
                        alert('Oops , deu erro...');
                    },
                
                    complete: function (xhr) {
                        if (xhr.responseText && xhr.responseText != "error")
                        {
                              $("#outputImage").html(xhr.responseText);
                        }
                        else{  
                               $("#outputImage").show();
                                $("#outputImage").html("<div class='botao_cancela_js'>Erro ao capturar a imagem.</div>");
                                $("#progressBar").stop();
                        }
                    }
                });
    });
});