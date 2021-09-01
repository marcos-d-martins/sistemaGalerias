<?php

    $pegarDadosAjax = filter_input_array(INPUT_POST, FILTER_DEFAULT);
    $limparCodigo = array_map('strip_tags', $pegarDadosAjax);
    $dados = array_map('trim', $limparCodigo);
    sleep(1);
    print_r($dados);
    $acao = $dados['acao'];
    $dados['data_da_publicacao'] = date('Y-m-d H:i:s');
    //print_r($dados['data_da_publicacao']);
    if($acao):
        require '../../../Config/Conf.inc.php';
        $cadastro = new Inserir();
    endif;
    
    unset($dados['acao']);
    $respostaJSON = array();
    
    switch($acao):
        case 'cadastro':
            if(in_array('', $dados)):
                $respostaJSON['erro'] = "Nenhum campo pode ficar vazio!";
            else:
                $cadastro->executarInsercao('publicacao', $dados);
                $respostaJSON['deu_certo'] = "Publicação id {$cadastro->pegarConexao()->lastInsertId()} cadastrado!";
            endif;
            break;
        default:
            $respostaJSON['erro'] = "Erro ao cadastrar: ";
    endswitch;
    
    
    print_r(json_encode($respostaJSON));