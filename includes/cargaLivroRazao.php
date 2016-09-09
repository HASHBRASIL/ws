<?php

    $qry = $dbh->prepare("insert into ing_dexion_livrorazao (id,contade,datalivro,lancamento,valordebito,valorcredito,saldo,contapara,texto1,debcred,segundoid) values (:id,:contade,:datalanc,:lancamento,:valordebito,:valorcredito,:saldo,:contapara,:texto1,:debcred,:segundoid)");

//    for($cnt=1;$cnt<=   12;$cnt++){
        echo "Carregando Mês " . $cnt;
        $num = str_pad($cnt,2,'0',STR_PAD_LEFT);
        $handle = fopen("/Users/solbisio/dev/hash-ws-php/public/planilhasfred/Livro Razão - 01012013 a 31122013.fernando2013.XML", "r");

//        $teste = file_get_contents("/Users/solbisio/dev/hash-ws-php/public/planilhasfred/Livro Razão - 01012013 a 31122013.fernando2013.XML");

//    $objXml = new SimpleXMLIterator("/Users/solbisio/dev/hash-ws-php/public/planilhasfred/Livro Razão - 01012013 a 31122013.fernando2013.XML", null, true);


    exit('end');


        $contade = getLinha($handle, null, true);

        $rodar = true;
        while($rodar) {
            $datalanc = getLinha($handle);
            if($datalanc!="**EOF**"){
                $lanc = getLinha($handle);
                $vdeb = getLinha($handle);
                $vcred = getLinha($handle);
                $saldo = getLinha($handle);
                $contapara = getLinha($handle);
                $texto = array();
                while ($txt = getLinha($handle)){
                    if(($txt==='D') || ($txt==='C')) {
                        $debcred = $txt;
                        break;
                    } else {
                        array_push($texto,$txt);
                    }
                }
                $secid = getLinha($handle);
                $nada2 = getLinha($handle);
                $id = UUID::v4();
                $qry->bindParam('id',$id);
                $qry->bindParam('contade',$contade);
                $qry->bindParam('datalanc',$datalanc);
                $qry->bindParam('lancamento',$lanc);
                $qry->bindParam('valordebito',$vdeb);
                $qry->bindParam('valorcredito',$vcred);
                $qry->bindParam('saldo',$saldo);
                $qry->bindParam('contapara',$contapara);
                $texto = implode(' ',$texto);
                $qry->bindParam('texto1',$texto);
                $qry->bindParam('debcred',$debcred);
                $qry->bindParam('segundoid',$secid);

                //echo $contade . ' - ' . $datalanc . ' - ' . $lanc . ' - ' . $vdeb . ' - ' . $vcred . ' - ' .  $saldo . ' - ' .  $contapara . ' - ' .  implode(' ',$texto)  . ' - ' .  $debcred  . ' - ' . $secid;

                $qry->execute();

                if(getLinha($handle)==='**EOF**') {
                    fclose($handle);
                    $rodar = false;
                }
            } else {
                $rodar = false;
            }
        }
//    }

    function getLinha($handle) {
        if ($handle) {
            if($linha = fgets($handle)) {
                $ini = strpos($linha,'>')+1;
                $fim = strpos($linha,'<',$ini-1);
                //echo $ini . ' - ' . $fim;
                return mb_convert_encoding(substr($linha,$ini,$fim-$ini), 'UTF-8', 'Windows-1252');
            } else {
                return '**EOF**';
            }
        } else {
            return '**EOF**';
        }
    }