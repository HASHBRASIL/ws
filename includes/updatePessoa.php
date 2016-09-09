<?php
    // new dBug($_REQUEST);
    // die();

    if (isset($SERVICO['id_grupo'])){
        $grupo = $SERVICO['id_grupo'];
    } else {
        $grupo = $identity->time['id'];
    }

    if (isset($SERVICO['metadata']['ws_id']) && ($SERVICO['metadata']['ws_id'])) {
        $param = array ('usr' => $_SESSION['USUARIO']['ID'], 'time' => $grupo);
        $uuidPessoa = $param[$SERVICO['metadata']['ws_id']];
    } elseif ($_REQUEST['id']) {
        $uuidPessoa = $_REQUEST['id'];
    } else {
        // @todo erro!
        parseJson(true, 'É necessário selecionar uma pessoa para editar!');
    }

    require_once "UUID.php";

    $data = $_POST;

    $tpInformacao = new TpInformacao();

    $campos = $tpInformacao->getTpInformacaoByPerfisByPessoaByGrupo($SERVICO['metadata']['ws_perfil'], $uuidPessoa, $grupo);

    $arPerfil = explode(',', $SERVICO['metadata']['ws_perfil']);

    foreach ($campos as $campo) {
        $campo['metadatas'] = json_decode($campo['metadatas']);
        $arCampos[$campo['perfil']][] = $campo;
    }


    foreach ($arCampos as $chave => $campo) {
       
        foreach ($arCampos[$chave] as $key => $field) {
            if ( !empty($field['id_pai']) ) {
                $arrayCampos[$chave][$field['nome_pai']][] = $field;
            } else {
                if ( $field['tipo'] != 'Master' ) {
                    $arrayCampos[$chave][$key] = $field;
                }
            }
        }
    }

    try {

        $dbh->beginTransaction();

        $queryInsertInformacao = $dbh->prepare("INSERT INTO tb_informacao ( id, valor, id_criador, id_tinfo, id_pai, id_pessoa ) VALUES (:id, :valor, :id_criador, :id_tinfo, :id_pai, :id_pessoa);");
	    $querySelectInfo = $dbh->prepare("select inf.* from tb_informacao inf join rl_grupo_informacao rgi on (inf.id = rgi.id_info) where inf.id_tinfo = :id_tinfo and inf.id_pessoa = :id_pessoa and rgi.id_grupo = :id_grupo");
        $queryInsertRelacionamento = $dbh->prepare("INSERT INTO rl_grupo_informacao (id, id_grupo, id_pessoa, id_info) VALUES (:id, :id_grupo, :id_pessoa, :id_info);");

        $idPaiTipo = null;
        $idNulo = null;

        // estado inicial.
        $queryInsertInformacao->bindParam(':id_pai', $idNulo);

        // campos iguais para todos os inserts.
        $queryInsertInformacao->bindParam(':id_pessoa', $uuidPessoa);
        $queryInsertInformacao->bindParam(':id_criador', $_SESSION['USUARIO']['ID']);

        $queryInsertRelacionamento->bindParam(':id_pessoa', $uuidPessoa);
        $queryInsertRelacionamento->bindParam(':id_grupo', $grupo);

        // prepare para update
        $queryUpdateInformacao = $dbh->prepare("UPDATE tb_informacao set valor = :valor where id = :id");

        // prepare para delete
        $queryDeleteInformacao = $dbh->prepare("DELETE from tb_informacao where id = :id");

	$querySelectCampoPai = $dbh->prepare("SELECT id_pai from tp_informacao where id = :id");
        foreach ($campos as $campo) {
            //  && (!$campo['metanome']) verificar isso
            // caso INSERT
            // TEM QUE DAR UMA OLHADA NESSE IF POIS ELE NUNCA ESTÃ� PASSANDO TRUE, principalmente 
            // ESSA CLAUSULA AQUI && (!$campo['metanome']) && (!$campo['valor'])
            
            //x( $_SESSION['Zend_Auth']['storage']->id );
        	//x($data[$campo['id'].'_'.$campo['metanome']],0);
            //x($data[$campo['id'].'_'.$campo['metanome']],0);
            //x($campo['valor']);
            //x($campo['id'].'_'.$campo['metanome'],0);
        	//x(count ($data[$campo['id'].'_'.$campo['metanome']]));
        	$objInformacao	=	new Informacao();
        	$criador		=	$_SESSION['Zend_Auth']['storage']->id;	
        	if ( isset( $data[$campo['id'].'_'.$campo['metanome']] ) ) {
				$querySelectInfo->bindParam('id_tinfo',$campo['id']);
                                $querySelectInfo->bindParam('id_grupo',$grupo);
                                $querySelectInfo->bindParam('id_pessoa',$uuidPessoa);
                                $querySelectInfo->execute();
				$campoExiste = $querySelectInfo->fetchAll(PDO::FETCH_ASSOC);
				if ( count($campoExiste) > 0 ){
					$id_info	=	$campoExiste[0]['id'];
					//$objInformacao->updateValor( $id_info, $data[$campo['id'].'_'.$campo['metanome']]);
					$queryUpdateInformacao->bindParam('id',$id_info);
					$queryUpdateInformacao->bindParam('valor',$data[$campo['id'].'_'.$campo['metanome']]);
					$queryUpdateInformacao->execute();
				} else {
					// =õ( .. sem tempo pra fazer com calma
					/*
					 * tenho que ver se o cara tem um pai salvo na tb_informacao, para criar ele como filho, caso ele tenha o id_pai não nulo
					 * se precisar de pai, deve criar o pai e apenas depois disso criar o filho, o relacionando
					 *
					 *
					 *
					 *
					 * */
					if ( isset($campo['id_pai'])){
						$querySelectCampoPai->bindParam('id',$campo['id']);
						$querySelectCampoPai->execute();
						$objPai = $querySelectCampoPai->fetchAll(PDO::FETCH_ASSOC);
						$id_tp_info_pai = null;
						if($objPai[0]['id_pai']){
							$id_tp_info_pai = $objPai[0]['id_pai'];
						}

						if ( $id_tp_info_pai ){
							//x( $id_tp_info_pai,0);
							//x( $objInformacao->euExisto( $id_tp_info_pai , $data['id'], $criador ), 0);
							$querySelectInfo->bindParam('id_tinfo',$id_tp_info_pai);
							$querySelectInfo->bindParam('id_grupo',$grupo);
							$querySelectInfo->bindParam('id_pessoa',$uuidPessoa);
							$querySelectInfo->execute();
							$euExisto = $querySelectInfo->fetchAll(PDO::FETCH_ASSOC);

							if (count($euExisto)>0){
								//$id_pai	= $objInformacao->euExisto( $id_tp_info_pai , null, $grupo );
								$id_pai = $euExisto[0]['id'];
								//$id_ins = $objInformacao->criarNova($campo['id'], $data['id'], $data[$campo['id'].'_'.$campo['metanome']], 'TbInformacao', $id_pai);
								$id_ins = UUID::v4();
								    $queryInsertInformacao->bindParam(':id', $id_ins);
                                                                    $queryInsertInformacao->bindParam(':id_tinfo', $campo['id']);
                                                                    $queryInsertInformacao->bindParam(':id_pai', $id_pai);
                                                                    $queryInsertInformacao->bindParam(':valor', $data[$campo['id'].'_'.$campo['metanome']]);
                                                                    $queryInsertInformacao->execute();

								$idrel = UUID::v4();
								$queryInsertRelacionamento->bindParam(':id', $idrel);
							        $queryInsertRelacionamento->bindParam(':id_info', $id_ins);
								$queryInsertRelacionamento->execute();
								//echo "INSERT com Pai <br />";
							} else {
								//$id_pai = $objInformacao->criarNova( $id_tp_info_pai, null , $criador );
								$idPaiInfo = UUID::v4();

						                    $queryInsertInformacao->bindParam(':id', $idPaiInfo);
						                    $queryInsertInformacao->bindParam(':id_tinfo', $id_tp_info_pai);
						                    $queryInsertInformacao->bindParam(':id_pai', $idNulo);
						                    $queryInsertInformacao->bindParam(':valor', $idNulo);
						                    $queryInsertInformacao->execute();
								$idRefPai = UUID::v4();
								$queryInsertRelacionamento->bindParam(':id', $idRefPai);
                                                                $queryInsertRelacionamento->bindParam(':id_info', $idPaiInfo);
                                                                $queryInsertRelacionamento->execute();
								//$id_ins = $objInformacao->criarNova($campo['id'], $data['id'], $data[$campo['id'].'_'.$campo['metanome']], 'TbInformacao', $id_pai);
								$idInfo = UUID::v4();

						                    $queryInsertInformacao->bindParam(':id', $idInfo);
						                    $queryInsertInformacao->bindParam(':id_tinfo', $campo['id']);
						                    $queryInsertInformacao->bindParam(':id_pai', $idPaiInfo);
						                    $queryInsertInformacao->bindParam(':valor', $data[$campo['id'].'_'.$campo['metanome']]);
						                    $queryInsertInformacao->execute();

								$idRef = UUID::v4();
								$queryInsertRelacionamento->bindParam(':id', $idRef);
                                                                $queryInsertRelacionamento->bindParam(':id_info', $idInfo);
                                                                $queryInsertRelacionamento->execute();
								//echo "INSERT sem Pai <br />";
							}
						} 
						
					} else {
						//$id_ins = $objInformacao->criarNova($campo['id'], $data['id'], $data[$campo['id'].'_'.$campo['metanome']], 'TbInformacao');
		 				$idInfo = UUID::v4();

                        $queryInsertInformacao->bindParam(':id', $idInfo);
                        $queryInsertInformacao->bindParam(':id_tinfo', $campo['id']);
                        $queryInsertInformacao->bindParam(':id_pai', $idNulo);
                        $queryInsertInformacao->bindParam(':valor', $data[$campo['id'].'_'.$campo['metanome']]);
                        $queryInsertInformacao->execute();

						$idrel = UUID::v4();
						$queryInsertRelacionamento->bindParam(':id', $idrel);
                        $queryInsertRelacionamento->bindParam(':id_info', $idInfo);
                        $queryInsertRelacionamento->execute();

						//echo "INSERT sozinho <br />";
					}
				}
				/*
                if ((!$campo['id_pai']) && ($idPaiTipo)) {
                    $idPaiTipo = null;
                    $idPaiInfo = null;

                    $queryInsertInformacao->bindParam(':id_pai', $idNulo);

                } else if (((!$idPaiTipo) && ($campo['id_pai'])) || (($campo['id_pai'] != $idPaiTipo))) {
                    $idPaiTipo = $campo['id_pai'];
                    $idPaiInfo = UUID::v4();

                    $queryInsertInformacao->bindParam(':id', $idPaiInfo);
                    $queryInsertInformacao->bindParam(':id_tinfo', $campo['id_pai']);
                    $queryInsertInformacao->bindParam(':id_pai', $idNulo);
                    $queryInsertInformacao->bindParam(':valor', $idNulo);
                    $queryInsertInformacao->execute();

                    $uuidRelacionamento = UUID::v4();
                    $queryInsertRelacionamento->bindParam(':id', $uuidRelacionamento);
                    $queryInsertRelacionamento->bindParam(':id_info', $idPaiInfo);
                    $queryInsertRelacionamento->execute();

                    $queryInsertInformacao->bindParam(':id_pai', $idPaiInfo);
                }

                $uuidInfo = UUID::v4();
                $queryInsertInformacao->bindParam(':id', $uuidInfo);
                $queryInsertInformacao->bindParam(':id_tinfo', $campo['id']);
                $queryInsertInformacao->bindParam(':valor', $data[$campo['id'].'_'.$campo['metanome']]);
                $queryInsertInformacao->execute();

                if (!$idPaiTipo) {
                    $uuidRelacionamento = UUID::v4();
                    $queryInsertRelacionamento->bindParam(':id', $uuidRelacionamento);
                    $queryInsertRelacionamento->bindParam(':id_info', $uuidInfo);
                    $queryInsertRelacionamento->execute();
                }x('entrei',0);
            } elseif (isset($data[$campo['id'].'_'.$campo['metanome']]) && $data[$campo['id'].'_'.$campo['metanome']] && $campo['valor'] && ($campo['valor'] != $data[$campo['id'].'_'.$campo['metanome']])) {
                x('1',0);// UPDATE
                $queryUpdateInformacao->bindParam(':id', $campo['tbinfoid']);
                $queryUpdateInformacao->bindParam(':valor', $data[$campo['id'].'_'.$campo['metanome']]);
                $queryUpdateInformacao->execute();

            } elseif (isset($data[$campo['id'].'_'.$campo['metanome']]) && (!$data[$campo['id'].'_'.$campo['metanome']]) && ($campo['valor'])) {
               x('2',0);// DELETE
                $queryDeleteInformacao->bindParam(':id', $campo['tbinfoid']);
                $queryDeleteInformacao->execute();
            } else {
               x('3',0);// nÃ£o faz nada.
            }x('sai',0);
        }*/
        	} else {
			//echo "Campo não existe <br />";
		}


 /*else {
        		if ( $objInformacao->euExisto($campo['id'], $data['id'], $criador)){
        			$id_info	=	$objInformacao->euExisto($campo['id'], $data['id'], $criador);
        			$objInformacao->deletar( $id_info);
        		} */
        	}
        	

        // resposta padrÃ£o para salvo com sucesso.
        $dbh->commit();
        //echo "commitou";
        if (isset($SERVICO['metadata']['ws_target']) && ($SERVICO['metadata']['ws_target'])) {
            $servico = new Servico();
            $servicoDestino = $servico->getServiceByMetanome($SERVICO['metadata']['ws_target']);
        } elseif (isset($SERVICO['metadata']['ws_target']) && (!$SERVICO['metadata']['ws_target'])) {
            $servicoDestino = $SERVICO['id_pai'];
        }
        
        $servicoDestino = current($servicoDestino);

        if ($servicoDestino) {
            $flashMsg = new flashMsg();
            $flashMsg->success('Salvo com sucesso!');

            parseJsonTarget($servicoDestino);
        } else {
            parseJson();
        }
    } catch (PDOException $e) {
        // @todo verificar se vai ter algum tratamento especial para PDO.
        $dbh->rollBack();
        parseJson(true, $e->getMessage(), $e->getTraceAsString());
    } catch (exception $e) {
        $dbh->rollBack();
        parseJson(true, $e->getMessage(), $e->getTraceAsString());
    }
