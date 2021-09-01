/**
   06 de agosto de 2021
    Marcos Daniel
    .CARREGAMENTO DA CAPA
*/

var formFiles, divReturn, progressBar;

formFiles = document.getElementById('formFiles');
divReturn = document.getElementById('return');
progressBar = document.getElementById('progressBar');

formFiles = addEventListener('submit',enviaFormulario, false);

function enviaFormulario(evt){
    
    var dados, ajax, pct;
    dados = new FormData(evt.target);
    
    ajax = new XMLHttpRequest();
    
    ajax.onreadystatechange = function(){
        if(ajax.readyState == 4){
            //formFiles.reset();
            //divReturn.textContent = ajax.response;
            //console.log(ajax.response);
        } else {
            
        }
    }
    
    ajax.open('POST','editar_publicacao.php');
    ajax.send(dados);
}