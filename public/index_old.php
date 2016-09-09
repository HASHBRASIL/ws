<?php
/**
 * @Author: toinsane
 * @Date:   2016-01-20 14:51:27
 * @Last Modified by:   toinsane
 * @Last Modified time: 2016-01-20 15:55:12
 */
?>
<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="description" content="">
        <meta name="author" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" id="myViewport">
        <title>Hash</title>
        <script src="library/jquery/jquery-2.1.4.min.js" type="text/javascript"></script>        
        <script src="library/bootstrap/js/bootstrap.min.js"></script> 
        <script src="js/main.js" type="text/javascript"></script>
        <link href='https://fonts.googleapis.com/css?family=Arimo:400,400italic,700,700italic' rel='stylesheet' type='text/css'>
        <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,600,700,800,300italic,600italic,700italic,800italic,400italic,300' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="library/fortawesome/css/font-awesome.min.css">
        <link rel="stylesheet" href="library/bootstrap/css/bootstrap.min.css" type="text/css" >
        <link rel="stylesheet" href="css/general.css" type="text/css" >
    </head>
    <body>
        <main>
            <aside class="area-sidebar">
                <section class="area-usuario">
                    <figure>
                        <img src="img/perfil.png" alt="Imagem do usuário">
                        <figcaption><i class="fa fa-circle"></i>Giovanna</figcaption>
                    </figure>
                    <a href="#" class="btn-show-user-menu">
                        <i class="fa fa-angle-right"></i>
                    </a>
                </section>
                <section class="area-nav-times">
                    <nav>
                        <header>
                            <h1>
                                <a href="#" class="btn-show-times">
                                    TIMES
                                    <i class="fa fa-angle-right"></i>
                                    <span class="badge">12</span>
                                </a>
                                <a href="#" class="btn-add"><i class="fa fa-plus-circle"></i></a>
                            </h1>
                        </header>
                        <ul>
                            <li><a href="#" class="">TitaniumTech</a></li>
                        </ul>
                    </nav>

                    <nav>
                        <header>
                            <h1>
                                <a href="#" class="btn-show-times">
                                    GRUPOS
                                    <i class="fa fa-angle-right"></i>
                                    <span class="badge">113</span>
                                </a>
                                <a href="#" class="btn-add"><i class="fa fa-plus-circle"></i></a>
                            </h1>
                        </header>
                        <ul>
                            <li>
                                <a href="#" class="">
                                    <span class="mark-grupo-hash">#</span>Gráfica São Jorge 
                                    <span href="#" class="btn-fav-grupo"><i class="fa fa-star-o"></i></span>
                                </a>
                            </li>
                        </ul>
                    </nav>
                </section>
                <section class="area-nav-menu">
                    <header>
                        <h1>
                            MÓDULO DE DEBUG
                            <a href="#"><i class="fa fa-navicon"></i></a>
                        </h1>
                    </header>
                    <nav>
                        <ul>
                            <li><a href="#">Menu de Criação</a></li>
                            <li><a href="#">Inserir Pessoa</a></li>
                            <li><a href="#">Serviço de Teste de Abas <i class="fa fa-angle-right"></i></a></li>
                            <li><a href="#">Listar Conteúdo (NOVO) <i class="fa fa-angle-right"></i></a></li>
                            <li><a href="#">Edição de Times</a></li>
                            <li><a href="#">Layout Padrão</a></li>
                            <li><a href="#">Criar Times <i class="fa fa-angle-right"></i></a></li>
                            <li><a href="#">Teste Criar Menu</a></li>
                            <li><a href="#">Teste Gestão de Pessoa</a></li>
                        </ul>
                    </nav>
                </section>
            </aside>
            <section class="area-conteudo">
                <section class="row area-top">
                    <div class="col-md-12">
                        <nav class="nav-grupo-times">
                            <ul>
                                <li>
                                    <a href="#" class="btn-times">
                                        <span>TitaniumTech</span>
                                        <i class="fa fa-chevron-down"></i>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="btn-fav">
                                        <i class="fa fa-star-o"></i>
                                    </a>
                                    <a href="#" class="btn-times">
                                        <span class="mark-grupo-hash">#</span>
                                        <span>Gráfica São Jorge</span>
                                        <i class="fa fa-chevron-down"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </section>
                <section class="row content-wrapper">
                    <h1 class="cabecalho-titulo-pagina">
                        <header>Gestão de Conteúdo</header>
                        <nav class="rastro">
                            <ul>
                                <li><a href="#">Dashboard</a> <i class="fa fa-angle-right"></i></li>
                                <li><a href="#">Serviços de Conteúdo</a> <i class="fa fa-angle-right"></i></li>
                                <li class="active">Gestão de Conteúdo</li>
                            </ul>
                        </nav>
                    </h1>
                    <!-- Nav tabs -->
                    <nav class="content-tabs">
                        <ul class="nav nav-tabs" role="tablist">
                            <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Home</a></li>
                            <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">Profile</a></li>
                            <li role="presentation"><a href="#messages" aria-controls="messages" role="tab" data-toggle="tab">Messages</a></li>
                            <li role="presentation"><a href="#settings" aria-controls="settings" role="tab" data-toggle="tab">Settings</a></li>
                        </ul>
                    </nav>

                    <!-- Tab panes -->
                    <section class="tab-content">
                        <div role="tabpanel" class="tab-pane active" id="home">HOME</div>
                        <div role="tabpanel" class="tab-pane" id="profile">PROFILE</div>
                        <div role="tabpanel" class="tab-pane" id="messages">MESSAGES</div>
                        <div role="tabpanel" class="tab-pane" id="settings">SETTINGS</div>
                    </section>

                    <div class="col-md-12">
                        
                    </div>
                </section>
            </section>
            <section class="area-chat">
                <h1>AREA CHAT</h1>
            </section>
        </main>
    </body>
</html>
