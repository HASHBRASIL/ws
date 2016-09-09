<?php

session_start();
session_destroy();

// echo "<pre>";
// var_dump($_SESSION);
// die();

if(isset($_SESSION['USUARIO']['ID'])){
    //header('location: home.php');
    unset($_SESSION['USUARIO']);
}

include 'includes/header.php';
?>
<div id="loginHome">
    <div id="progressiveBar">
        <div id="bar"></div>
    </div>
    <form action="home.php" method="POST" id="login"><input type="hidden" name="usr" id="usr"/><input type="hidden" name="pwd" id = "pwd"/></form>
    <div class="form-group-one-unit">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="teste0">Usuário</label>
                    <input type="text" placeholder="joaodasilva" id="usuario" name="usuario" class="form-control input-sm">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="teste3">Senha</label>
                    <input type="password" placeholder="**********" id="senha" name="senha" class="form-control input-sm">
                </div>
            </div>
        </div>
    </div>
    <button type="button" id="btnSubmit" data-loading-text="Loading..." autocomplete="off" class="btn-sm btn-success btn pull-right">Login</button>
</div>
<script>
    $(document).ready(function() {

        $("#btnSubmit").click(function(){
            $(this).text('Validando...').attr('disabled', 'disabled');
            $.ajax({
                url: 'login/getuser.php',
                type: 'GET',
                data: { usuario: $("#usuario").val() },
                success: function(data){
                    var ret = JSON.parse(data);
                    var mypbkdf2 = new PBKDF2($("#senha").val(), ret['dbsalt'], 10000, 20);

                    var status_callback_1 = function(percent_done) {
                        $('#bar').css('width', (percent_done/2) + '%');
                    };
                    var status_callback_2 = function(percent_done) {
                        $('#bar').css('width', 50 + (percent_done/2) + '%');
                    };
                    var result_callback = function(key) {

                        var mypbkdf3 = new PBKDF2(key, ret['sessionsalt'], 10000, 20);
                        var result_final = function(key) {
                            $("#usr").val($("#usuario").val());
                            $("#pwd").val(key);
                            $("#login").submit();
                        }
                        mypbkdf3.deriveKey(status_callback_2, result_final);
                    };
                    mypbkdf2.deriveKey(status_callback_1, result_callback);
                }
            });
        });
    });
</script>

<?php
	include 'includes/footer.php';
?>
