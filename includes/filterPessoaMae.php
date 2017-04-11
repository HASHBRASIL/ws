<?php

    $return_arr = array();
    $row_array = array();

    if ((isset($_GET['search']) && strlen($_GET['search']) > 0) || (isset($_GET['id']) && is_numeric($_GET['id']))) {

        $pessoa = new Pessoa();

        if (isset($_GET['search'])) {
            /* limit with page_limit get */
    //        $limit = intval($_GET['page_limit']);
            $rsPessoa = $pessoa->getPessoaByNome($_GET['search'], 10);
        } elseif (isset($_GET['id'])) {
            $rsPessoa = $pessoa->getPessoaById($uuidPessoa);
        }

    //    var_dump($rsPessoa);

        foreach ($rsPessoa as $row) {
            $row_array['id'] = $row['id'];
            $row_array['text'] = $row['nome'];
            array_push($return_arr, $row_array);
        }

    } else {
        $row_array['id'] = 0;
        $row_array['text'] = utf8_encode('Nome E Sobrenome');

        array_push($return_arr, $row_array);
    }

    $ret = array();

    if (isset($_GET['id'])) {
        $ret = $row_array;
    } else {
        $ret['results'] = $return_arr;
    }

    parseResults($return_arr);
