<?php
    include_once 'header.php';

    if( isset( $_SESSION['time'] ) )
    {
        $time = $_SESSION['time'];

        $nomeusuario = ( isset( $time['nome'] ) )      ? $time['nome']      : "";
        $email       = ( isset( $time['email'] ) )     ? $time['email']     : "";
        $nometime    = ( isset( $time['time'] ) )      ? $time['time']      : "";
        $timealias   = ( isset( $time['aliastime'] ) ) ? $time['aliastime'] : "";
    }else{
        $erro = "<p style='color: red'>Dados não foram preenchidos corretamente</p>";
    }
?>
<div class="confirmacao">
    <h1>Tela de confirmação:</h1>
    <form action="insert.php" method="post" class="novoTime">
        Email:                    <input type="email" name="email" value="<?= ( isset( $email ) )       ? $email:''; ?>"        title="Campo obrigatorio" required> <br>
        Nome do time:             <input type="text"  name="time"  value="<?= ( isset( $nometime ) )    ? $nometime:''; ?>"     title="Campo obrigatorio" required> <br>
        Endereço do site (Alias): <input type="text"  name="alias" pattern="[a-zA-Z0-9]+" value="<?= ( isset( $timealias ) )   ? $timealias:''; ?>"    title="Campo obrigatorio, campo aceita epenas letras(a-z/A-Z) e numeros(0-9)" required> <br>
        Nome de usuário:          <input type="text"  name="nome"  value="<?= ( isset( $nomeusuario ) ) ? $nomeusuario :''; ?>" title="Campo obrigatorio" required>
        <button type="submit" id="confirm-novoTime">Salvar</button>
    </form>
    <div class="retorno"><?php echo ( isset( $erro ) ) ? $erro: ""; ?></div>
</div>
<?php include_once 'footer.php'; ?>
