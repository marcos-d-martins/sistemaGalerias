<?php error_reporting (E_ALL & ~ E_NOTICE & ~ E_DEPRECATED); ?>
<?php
if (isset($_REQUEST["acao-contato"])) {
	
	$emaildoservidor = 'contacto@latransmutacion.com'; // Informar um e-mail do próprio servidor para validação.
	$emailcliente = $_REQUEST["email"];  // Informar um e-mail do cliente que entrou em contato
	$insert_objetivo = "Contato via formulário";  // Informar objetivo do e-mail
	$subject  = "Formulário de Contato";  // Informar assunto do e-mail
	$destinatarios   = 'contatociceromiguel@gmail.com,contacto@latransmutacion.com';  // Informar para quem vai a mensagem
	$message  = '
			Solicitação de Contato:
			Email enviado no dia '.$dataHojecomBarra.' as '.$horariodebrasiliaCorreto.'
			Domínio que foi enviado o e-mail: '.$endereco_da_pagina.'
			IP utilizado para enviar o e-mai: '.$ipdousuario.'
			
			Nombre: '.$_REQUEST["nombre"].'
			Email: '.$_REQUEST["email"].'
			Mensaje: '.$_REQUEST["phone"].'
			';
	
	
	$headers = "MIME-Version: 1.1\n";
	$headers .= "Content-type: text/plain; charset=iso-8859-1\n";
	$headers .= "From: $emaildoservidor\n"; // remetente
	$headers .= "Return-Path: $emaildoservidor\n"; // return-path
	$headers .= "Reply-To: $emailcliente\n"; // Endereço (devidamente validado) que o seu usuário informou no contato
	$envio = mail($destinatarios, $subject, $message, $headers, "-f$emaildoservidor");
	
	echo "<html>
			<meta http-equiv=refresh content=\"0 URL='/index.php'\"/>
			<head>
			</head>
			<body>
				<center>
					<script>alert('Gracias por contactar con nosotros. En breves momentos atenderemos tu solicitud.')</script>
				</center>
			</body>
		</html>";
	
}
?>