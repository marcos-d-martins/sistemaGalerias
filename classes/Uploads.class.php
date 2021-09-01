<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Uploads
 *
 * @author Marcos Daniel
 */
class Uploads {
    private $arquivo;
    private $name;
    private $envio;
    
    /** SUBIR IMAGENS**/
    private $largura;
    private $objDaImagem;
    
    /** Gestão de RESULTADOS**/
    private $resultados;
    private $msg;
        
    /** DIRETÓRIOS  **/
    private $pasta;
    private static $diretorioPadrao;
        
    
    public function __construct($diretorioPadrao=null) {
        self::$diretorioPadrao = ( (string) $diretorioPadrao ? $diretorioPadrao : 'uploads/' );
        
        if( !file_exists(self::$diretorioPadrao) && !is_dir(self::$diretorioPadrao) ):
            mkdir(self::$diretorioPadrao,0777);
        endif;
    }
    
    
    public function formataImagem(array $imagem,$nomeImagem=null,$largura=null,$pasta=null) {
        $this->arquivo =  $imagem;
        $this->name = ( (string) $nomeImagem ? $nomeImagem : substr($imagem['name'], 0, strrpos($imagem['name'], '.')) );
        
        $this->largura = ( (int) $largura ? $largura : 1024 );
        $this->pasta = ( (string) $pasta ? $pasta : 'imagens' );
        
        $this->verificaDiretorio($this->pasta);
        $this->formatarNomeArquivo();
        $this->uploadImage();
    }
    
    public function formataMidias(array $arquivo,$nome = null,$pasta = null, $tamanhoLimite = null) {
        $this->arquivo = ( $arquivo ? $arquivo : null );
        $this->name = ( (string) $nome ? $nome : substr( $arquivo['name'], 0, strrpos($arquivo['name'], '.') ));
        $this->pasta = ( (string) $pasta ? $pasta : 'arquivos');
        $tamanhoLimite = (  (int) $tamanhoLimite ? $tamanhoLimite : 20  );
        
        $tiposAceitos = [
            'audio/mpeg',
            'audio/mp3',
            'video/mp4',
            'video/avi'
        ];
        
        if( $this->arquivo['size']   >   ( $tamanhoLimite * (1024*1024*10) ) ):
            $this->resultados = false;
            $this->msg = "Arquivo muito grande. O limite é {$tamanhoLimite}MB.";
        elseif(!in_array($this->arquivo['type'], $tiposAceitos)):
            errosDoUsuarioCustomizados("Tipo {$this->arquivo['type']} não aceito", CORPF_AMARELO);
        else:
            $this->verificaDiretorio($this->pasta);
            $this->formatarNomeArquivo();
            $this->enviarMidias();
        endif;
    }
    public function formataDocumentos(array $documento,$nome = null,$pasta = null, $tamanhoLimite = null) {
        $this->arquivo = $documento;
        $this->name = ( (string) $nome ? $nome : substr($documento['name'], 0, strrpos($documento['name'], '.') ));
        $this->pasta = ( (string) $pasta ? $pasta : 'arquivos' );
        $tamanhoLimite = (  (int) $tamanhoLimite ? $tamanhoLimite : 2  );
        
        $tiposAceitos = [
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/pdf'
        ];
        
        if($this->arquivo['size']   >   ( $tamanhoLimite * (1024*1024*5) )):
            $this->resultados = false;
            $this->msg = "Documento muito grande. O limite é {$tamanhoLimite}MB.";
        elseif(!in_array($this->arquivo['type'], $tiposAceitos)):
            errosDoUsuarioCustomizados("Tipo {$this->arquivo['type']} não aceito", CORPF_AMARELO);
        else:
            $this->verificaDiretorio($this->pasta);
            $this->formatarNomeArquivo();
            $this->enviarMidias();
        endif;
    }
    
    public function getResultados() {
        return $this->resultados;
    }

    public function getMsg() {
        return $this->msg;
    }
    
    public function getEnvio() {
        return $this->envio;
    }
    
    private function verificaDiretorio($pasta) {
        list($a,$m) = explode('/',date('Y/m'));
        $this->criarPasta("{$pasta}");
        $this->criarPasta("{$pasta}/{$a}");
        $this->criarPasta("{$pasta}/{$a}/{$m}/");
        $this->envio = "{$pasta}/{$a}/{$m}/";
    }
    private function criarPasta($pasta) {
        if(!file_exists(self::$diretorioPadrao . $pasta) && !is_dir(self::$diretorioPadrao . $pasta)):
            mkdir(self::$diretorioPadrao . $pasta, 0777);
        endif; 
    }
    
    private function formatarNomeArquivo() {
        $arquivoFormatado = Verificacao::tratamentoDeStrings($this->name) . strrchr($this->arquivo['name'], '.');
        if( file_exists(self::$diretorioPadrao . $this->envio . $arquivoFormatado) ):
            $arquivoFormatado = Verificacao::tratamentoDeStrings($this->name) . '-' . time() . strrchr($this->arquivo['name'], '.');
        endif;
        $this->name = $arquivoFormatado;
    }
    
    private function uploadImage() {
        switch ($this->arquivo['type']):
            case 'image/jpg':
            case 'image/jpeg':
            case 'image/pjpeg':
                $this->objDaImagem = imagecreatefromjpeg($this->arquivo['tmp_name']);
                break;
            case 'image/png':
            case 'image/xpng':
                $this->objDaImagem = imagecreatefrompng($this->arquivo['tmp_name']);
                break;
            case 'image/gif':
                $this->objDaImagem = imagecreatefromgif($this->arquivo['tmp_name']);
                break;
        endswitch;
        if(!$this->objDaImagem):
            $this->resultados = false;
            $this->msg = "Tipo de arquivo inválido. Enviar arquivos tipo JPEG ou PNG.";
        else:
            $x = imagesx($this->objDaImagem);
            $y = imagesy($this->objDaImagem);
            //Aqui o programa previne que distorça imagens pequenas.
            $imageX = ( $this->largura < $x ? $this->largura : $x);
            $ImageH = ($imageX * $y) / $x;
            
            $newImage = imagecreatetruecolor($imageX, $ImageH);
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
            imagecopyresampled($newImage, $this->objDaImagem, 0, 0, 0, 0, $imageX, $ImageH, $x, $y);
            
            switch ( $this->arquivo['type'] ):
                case 'image/jpg':
                case 'image/jpeg':
                case 'image/pjpeg':
                    imagejpeg($newImage, self::$diretorioPadrao . $this->envio . $this->name);
                    break;
                case 'image/png':
                case 'image/xpng':
                    imagepng($newImage, self::$diretorioPadrao . $this->envio . $this->name);
                    break;
            endswitch;
            
            if( !$newImage ):
                $this->resultados = false;
                $this->msg = "Tipo de imagem inválida. Enviar extensões JPEG ou PNG!";
            else:
                $this->resultados = $this->envio . $this->name;
                $this->msg = "Imagem enviada à pasta {$this->envio}";
            endif;
                        
            imagedestroy($this->objDaImagem);
            imagedestroy($newImage);
        endif;
    }    
    
    //Media management. 
    private function enviarMidias() {
        if(move_uploaded_file($this->arquivo['tmp_name'], self::$diretorioPadrao . $this->envio . $this->name)):
            $this->resultados = $this->envio . $this->name;
            $this->msg = ["Arquivo enviado com sucesso à pasta {$this->envio}", CORPF_VERDE];
        else:
            echo "Entrou no bloco de erro";
            $this->resultados = false;
            $this->msg = "Erro ao enviar arquivo. ";
        endif;
    }
 
}