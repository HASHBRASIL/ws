<?php
    require_once __DIR__ . '/../vendor/Facebook/autoload.php';
?>

<div class="row wrapper">
    <div class="page-header">
        <h1><?php echo $SERVICO['nome']; ?></h1>
        <span><?php echo $SERVICO['descricao']; ?></span>
    </div>
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <?php
                    $fb = new Facebook\Facebook([  
                      'app_id' => '1625494914396290',
                      'app_secret' => '4c42a14e2fc1ec6df75aa72d34646272',
                      'default_graph_version' => 'v2.4',
                      ]);

                    try {
                        $helper = $fb->getRedirectLoginHelper();
                        $accessToken = $helper->getAccessToken();
                        if(!isset($accessToken)){
                            if ($helper->getError()) {
                                echo "Error: " . $helper->getError() . "\n";
                                echo "Error Code: " . $helper->getErrorCode() . "\n";
                                echo "Error Reason: " . $helper->getErrorReason() . "\n";
                                echo "Error Description: " . $helper->getErrorDescription() . "\n";
                            } else {
                                $permissions = ['publish_pages','manage_pages', 'user_friends', 'email', 'read_page_mailboxes', 'user_about_me', 'user_hometown', 'user_birthday', 'read_custom_friendlists']; // Optional permissions
                                $loginUrl = $helper->getLoginUrl('http://localhost:90/home.php?servico='.$SERVICO['id'], $permissions);
                                echo '<a href="' . htmlspecialchars($loginUrl) . '">Log in with Facebook!</a>';
                            }
                        } else {
                            $oAuth2Client = $fb->getOAuth2Client();
                            if (! $accessToken->isLongLived()) {
                                try {
                                    $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
                                } catch (Facebook\Exceptions\FacebookSDKException $e) {
                                    echo "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>";
                                    exit;
                                }
                                //$perfil = $fb->get('/me?fields=id,name,about,bio,birthday,devices,email,hometown,installed,languages,location,locale,verified',$accessToken);
                                echo '<pre>';
                                $response = $fb->get('/me/accounts',$accessToken);
                                $pagesEdge = $response->getGraphEdge();
                                // Only grab 5 pages
                                $maxPages = 5;
                                $pageCount = 0;

                                do {
                                  echo '<h1>Page #' . $pageCount . ':</h1>' . "\n\n";

                                  foreach ($pagesEdge as $page) {
                                    //var_dump($page->asArray());
                                    $arrPage = $page->asArray();
                                    echo 'Carregando ' . $arrPage['name'] . '<br>';
                                    $respPage = $fb->get('/' . $arrPage['id'] . '?fields=id,name,likes', $arrPage['access_token']);
                                    $initlikes = $fb->get('/' . $arrPage['id'] . '/notifications', $arrPage['access_token']);
                                    $likes = $initlikes->getGraphEdge();
                                    var_dump($likes);
                                    // do {
                                    //   echo '<p>Likes:</p>' . "\n\n";
                                    //   var_dump($likes->asArray());
                                    // } while ($likes = $fb->next($likes));
                                  }
                                  $pageCount++;
                                } while ($pageCount < $maxPages && $pagesEdge = $fb->next($pagesEdge));

                                //var_dump($perfil->getGraphUser());
                                // $lstamigos = $fb->get('/me/friends?fields=id,name',$accessToken)->getGraphEdge();
                                // do {

                                // } while ($lstamigos = $fb->next($lstamigos));
                                // var_dump($lstamigos);

                                //$requestFriends = $fb->get('/me/invitable_friends?fields=id,name',$accessToken);
                                //$batchFrineds = ['user-friends' => $requestFriends];

                                //$responsesFriends = $fb->sendBatchRequest($batchFrineds);

                                // foreach ($requestFriends as $key => $response)
                                // {
                                    // echo "Response: " . var_dump($requestFriends) . "</p>\n\n";
                                // }


                                // $accounts = $fb->get('/me/accounts',$accessToken);
                                // $paginas = $accounts->getDecodedBody()['data'];
                                // foreach ($paginas as $key => $value) {
                                //     foreach ($value as $chave => $valor) {
                                //         echo '<p><b>' . $chave . '</b> ' . $valor . '</p>';
                                //     }
                                //     //echo '<p>' . $value['name'] . '</p>';
                                //     //var_dump($value);
                                // }
                            }
                        }
                    } catch(Facebook\Exceptions\FacebookResponseException $e) {
                        // When Graph returns an error
                        echo 'Graph returned an error: ' . $e->getMessage();
                        exit;
                    } catch(Facebook\Exceptions\FacebookSDKException $e) {
                        // When validation fails or other local issues
                        echo 'Facebook SDK returned an error: ' . $e->getMessage();
                        exit;
                    }
                ?>
            </div>
        </div>
    </div>
</div>

