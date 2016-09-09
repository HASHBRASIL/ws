<?php include_once 'header.php'; ?>
<div class="timeEmailUsuario">
    <h1>Email do Usuário:</h1>
    <form action="validations.php" method="post" class="time-email">
        Email: <input type="email" name="email" title="Campo obrigatorio" required>
        <input type="hidden" name="enviar" value="novoTime-email">
        <button type="submit" id="time-email">Proximo</button>
    </form>
    <div class="retorno"></div>
</div>
<?php include_once 'footer.php'; ?>
