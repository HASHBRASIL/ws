<?php include_once 'header.php'; ?>
    <div class="timeAlias">
        <h1>Endereço do site (Alias do time):</h1>
        <form action="validations.php" method="post" class="time-alias">
            Alias: <input type="text" name="timealias" pattern="[a-zA-Z0-9]+" title="Campo obrigatorio, campo aceita epenas letras(a-z/A-Z) e numeros(0-9)" required>
                   <input type="hidden" name="enviar" value="novoTime-alias">
            <button type="submit" id="time-alias">Proximo</button>
        </form>
        <div class="retorno"></div>
    </div>
<?php include_once 'footer.php'; ?>
