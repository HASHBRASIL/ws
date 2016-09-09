<?php
	$system_url    = "http://".$_SERVER['HTTP_HOST'];
	$sessionUserID = $_SESSION['USUARIO']['ID'];
	$sessionTimeID = $_SESSION['TIME']['ID'];
	$usuario       = getNomeUsuarioByID ( $sessionUserID, $dbh, $param = PDO::FETCH_OBJ );

	$idServico = ( isset( $_GET['servico'] ) ) ? $_GET['servico'] : NULL;
	$idTab     = ( isset( $_GET['tab'] ) ) ? $_GET['tab'] : NULL;

	$queryTime = $dbh->prepare( "SELECT * FROM tb_grupo WHERE id_representacao IS NOT NULL AND id_criador = :userid" );
	$queryTime->bindParam( ':userid', $sessionUserID );
	$queryTime->execute();
	$timesPorUsuario = $queryTime->fetchAll( PDO::FETCH_ASSOC );

	$queryGrupo = $dbh->prepare( "SELECT * FROM tb_grupo WHERE id_pai IS NOT NULL AND id_pai = :idtime" );
?>

<?php include_once "modais/modalNovoTime.php" ?>
<?php include_once "modais/modalEditarGrupo.php" ?>
<?php include_once "modais/modalNovoGrupo.php" ?>
<style>
	.time-tool-cell ul li{ list-style: none; }
</style>

<div class="row wrapper">
	<div class="page-header">
		<h1><?= $SERVICO['nome']; ?></h1>
        <span><?= $SERVICO['descricao']; ?></span>
	</div>

	<div class="col-md-12 content">
		<table class="table table-striped table-hover">
			<thead>
				<tr>
					<th>Time/Grupo</th>
					<th>
						<button class="pull-right btn btn-xs btn-success" data-toggle="modal" data-target="#modalNovoTime">
							<i class="fa fa-hashtag"></i> Novo Time
						</button>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ( $timesPorUsuario as $timesUsuario ): ?>
					<tr>
						<td>
							<strong><?= $timesUsuario['nome']; ?></strong>
						</td>
						<td class="time-tool-cell">
							<ul>
								<li>
									<a class="btn btn-xs btn-info"  href="#" data-toggle="modal" data-target="#modalNovoGrupo" data-idTime="<?= $timesUsuario['id']; ?>"> <i class="fa fa-users"></i> Novo Grupo</a>
								</li>
							</ul>
						</td>
					</tr>

					<?php
						$queryGrupo->bindParam( ':idtime', $timesUsuario['id'] );
						$queryGrupo->execute();
						$gruposPorTime = $queryGrupo->fetchAll( PDO::FETCH_ASSOC );
					?>
					<?php if ( count( $gruposPorTime ) != 0 ): ?>
						<?php for ($i=0; $i < count( $gruposPorTime ) ; ++$i): ?>
							<tr>
								<td>-> <?= $gruposPorTime[$i]['nome']; ?></td>
								<td class="time-tool-cell">
									<ul>
										<li>
											<a class="btn btn-xs btn-success"  href="#" data-toggle="modal" data-target="#modalEditarGrupo" data-idGrupo="<?= $gruposPorTime[$i]['id']; ?>"> <i class="fa fa-pencil"></i> Editar Grupo</a>
										</li>
									</ul>
								</td>
							</tr>
						<?php endfor; ?>
					<?php endif; ?>
				<?php endforeach; ?>
			</tbody>
		</table>
	</div>
</div>
<script>
	$('.time-tool-cell').on('click', 'a', function() {
		console.log( $(this).attr('data-idGrupo') );
	});
</script>