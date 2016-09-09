<html>
<head><title>CADASTRANDO USUARIO</title>
<script src="http://code.jquery.com/jquery-2.1.4.min.js"></script>
<script src="login/sha1.js"></script>
<script src="login.pbkdf2.js"></script>
</head>
<body>
Nome completo: <input type="text" id="nome" /><br />
Email: <input type="text" id="email" /><br />
Usuário: <input type="text" id="usuario" /> <div id="verificado"> </div> <a href="#" id="verificausr">Verificar disponibilidade</a><br />
Senha: <input type="password" id="senha" /><br />
Confirmar senha: <input type="password" id="confirmarsenha" /><br />
Telefone Celular: <input type="text" id="telcel" /><br />
CEP.: <input type="text" id="cep" /><br />
<input type="button" value="Cadastrar" id="enviar"/>
<script>
$(document).ready(function() {
    $("#verificausr").click(function() {
        $.ajax({
            url: 'getuserdisp.php',
            type: 'GET',
            data: {usuario: $("#usuario").val()},
            success: function(data){
                $("#verificado").html(data);
            }
        });
    });
    $("#enviar").click(function(){
        var sal = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
        for( var i=0; i < 8; i++ ) sal += possible.charAt(Math.floor(Math.random() * possible.length));
        var mypbkdf2 = new PBKDF2($("#senha").val(), sal, 10000, 20);
        var status_callback = function(percent_done) {
            $("#verificado").html(percent_done);
        };
        var result_callback = function(key) {
            $.ajax({
                url: 'insereuser.php',
                type: 'POST',
                data: {
                    nome: $("#nome").val(),
                    email: $("#email").val() ,
                    usuario: $("#usuario").val() ,
                    sal: sal ,
                    senha: key ,
                    telcel: $("#telcel").val() ,
                    cep: $("#cep").val()
                },
                success: function(data){ $("#verificado").html(data); }
            });
        };
         mypbkdf2.deriveKey(status_callback, result_callback) ;
    });
});
</script>
</body>
</html>

