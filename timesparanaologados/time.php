<?php include_once 'header.php'; ?>
<div class="timeCriarTime">
    <h1>Nome do Time:</h1>
    <form action="validations.php" method="post" class="time-nometime">
        Nome do time: <input type="text" name="nome" title="Campo obrigatorio" required>
                      <input type="hidden" name="enviar" value="novoTime-nometime">
        <button type="submit" id="time-nometime">Proximo</button>
    </form>
</div>
<?php include_once 'footer.php'; ?>