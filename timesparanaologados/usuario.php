<?php include_once 'header.php'; ?>
    <div class="timeUsuario">
        <h1>Nome de usuário:</h1>
        <form action="validations.php" method="post" class="time-nome">
            Nome: <input type="text" name="nomeusuario" title="Campo obrigatorio" required>
                  <input type="hidden" name="enviar" value="novoTime-nome">
            <button type="submit" id="time-nome">Proximo</button>
        </form>
        <div class="retorno"></div>
    </div>
<?php include_once 'footer.php'; ?>
