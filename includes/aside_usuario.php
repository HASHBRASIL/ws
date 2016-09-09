<?php
    $nomeUsuario = $_SESSION['USUARIO']['NOME'];
    $fotoUsuario = ($_SESSION['USUARIO']['FOTO'])?$_SESSION['USUARIO']['FOTO']:'imgs/partidos.png';
?>

<div id="userData">
    <img id="pictureUser" src="<?php echo $fotoUsuario; ?>" alt="Imagem do Usuário">
    <div>
        <span id="nameUser"><?php echo $nomeUsuario; ?></span>
    </div>
</div>
