<?php
	$pessoas = getTimesByUser( $dbh );
?>
<div class="row wrapper wrapper-white">
	<div class="page-header">
		<h1><?php echo $SERVICO['nome']; ?></h1>
        <span><?php echo $SERVICO['descricao']; ?></span>
	</div>

	<div class="col-md-12 content">
		<table class="table table-striped table-hover">
			<thead>
				<tr>
					<th>Usuário</th>
					<th>Time/Grupo</th>
			</thead>
			<tbody>
			<?php foreach ($pessoas as $pessoa ): ?>
				<tr>
					<td><?php echo $pessoa['nome']; ?> </td>
					<td><?php echo $pessoa['nomehash'] ?></td>
				</tr>
			<?php endforeach ?>
			</tbody>
		</table>
	</div>
</div>