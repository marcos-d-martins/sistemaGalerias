/**
   06 de agosto de 2021
    Marcos Daniel 
*/

var fotos_galeria, divRetorno, barra_carregamento;

fotos_galeria = document.getElementById('fotos_galeria');
divRetorno = document.getElementById('retorno');
barra_carregamento = document.getElementById('barra_carregamento');

//console.log(fotos_galeria, divRetorno, barra_carregamento);

fotos_galeria.addEventListener('submit', enviaGaleria, false);

function enviaGaleria(evt){
    
    var dadosFormulario;
    var ajx; 
    var pct;
    dadosFormulario = new FormData(evt.target);
    
    ajx = new XMLHttpRequest();
    
    ajx.onreadystatechange = function () {
        if(ajx.readyState == 1){
            fotos_galeria.reset();
            divRetorno.textContent = ajx.response;
        } else{
            alert("erro...");
        }        
    }
    ajx.open('POST','uploads.php');
    ajx.send(dadosFormulario);
    
}