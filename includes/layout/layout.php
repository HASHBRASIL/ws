<script>

	$(window).resize(resized);

	function resized(event){

		var min_height = $('#profile').position().top + $('#profile').height();
		var container  = $('#boxes-container > div.container-fluid');

		if( container.height() <=  $(window).height() ){
			var height = 0;
			if($(window).height() < min_height )
				height = min_height;
			else
				height = $(window).height();

		 	$('#boxes-container').height( height );
		 	$('#col-01').height( height );

		}else{
			$('#col-01').height( container.height() + 135 );
		}
	}


	$(document).ready(function() {
		resized();
		$('#ronaldo').chosen({width: '100%'});
	});
</script>

<div class="row super-search-bar">
	<div class="input-group col-md-12">
		<select name="" id="ronaldo" class="chosen" multiple='multiple'>
			<option value="">Dashboard</option>
			<option value="">Wordspace</option>
			<option value="">Lembretes</option>
			<option value="">Eventos</option>
			<option value="">Arquivos</option>
			<option value="">Download</option>
			<option value="">Notícias</option>
			<option value="">Itens Biblioteca</option>
			<option value="">Imagens</option>
		</select>
	</div>
</div>

<div id="dashboard-tools">
	<ul class="nav nav-pills">
		<li class="col-md-1"><a href=""><i class="fa fa-language"></i><span>WorkSpace</span></a></li>
		<li class="col-md-1"><a href=""><i class="fa fa-language"></i><span>Tarefas</span></a></li>
		<li class="col-md-1"><a href=""><i class="fa fa-language"></i><span>Lembretes</span></a></li>
		<li class="col-md-1"><a href=""><i class="fa fa-language"></i><span>Memo</span></a></li>
		<li class="col-md-1"><a href=""><i class="fa fa-language"></i><span>Eventos</span></a></li>
		<li class="col-md-1"><a href=""><i class="fa fa-language"></i><span>Arquivos</span></a></li>
		<li class="col-md-1"><a href=""><i class="fa fa-language"></i><span>Download</span></a></li>
		<li class="col-md-1"><a href=""><i class="fa fa-language"></i><span>Hash</span></a></li>
		<li class="col-md-1"><a href=""><i class="fa fa-language"></i><span>Hash</span></a></li>
		<li class="col-md-1"><a href=""><i class="fa fa-language"></i><span>Hash</span></a></li>
		<li class="col-md-1"><a href=""><i class="fa fa-language"></i><span>Hash</span></a></li>
		<li class="col-md-1"><a href=""><i class="fa fa-language"></i><span>Hash</span></a></li>
	</ul>
</div>

<div class="row wrapper">
	<div class="page-header">
		<h1><?= $HASH_SERVICO['nome']; ?></h1>
		<span><?= $HASH_SERVICO['descricao']; ?></span>
		<?php require_once 'includes/rastro.php'; ?>
	</div>
	<div class="col-md-12 content">
		<div class="form-header">
			<h1>Título do Formuláro <span>Breve descriçao do formulário</span></h1>
		</div>
		<div class="row">
			<div class="col-md-6">
				<div class="panel panel-default">
					<div class="panel-body">
						Basic panel example
					</div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="panel panel-default">
					<div class="panel-body">
						Basic panel example
					</div>
				</div>
			</div>
		</div>

		<div class="form-group-one-unit">
			<div class="row">
				<div class="col-md-12">
					<div class="form-group">
						<label for="teste0">Nome <span>Exemplo de Nome</span></label>
						<input type="text" placeholder="Nome" id"teste0" class="form-control input-sm">
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-4">
					<div class="form-group">
						<label for="teste1">Nome <span>Exemplo de Nome</span></label>
						<input type="text" placeholder="Nome" id="teste1" class="form-control input-sm">
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<label for="teste2">Nome <span>Exemplo de Nome</span></label>
						<input type="text" placeholder="Nome" id="teste2" class="form-control input-sm">
					</div>
				</div>
				<div class="col-md-4">
					<div class="form-group">
						<label for="teste3">Nome <span>Exemplo de Nome</span></label>
						<input type="text" placeholder="Nome" id="teste3" class="form-control input-sm">
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<button type="button" class="btn btn-success btn-sm pull-right margin-left">Enviar</button>
			<button type="button" class="btn btn-danger btn-sm pull-right">Limpar</button>
		</div>

		<div class="form-header">
			<h1>Título do Formuláro <span>Breve descriçao do formulário</span></h1>
		</div>
		<div class="form-group">
			<label for="teste4">Nome <span>Exemplo de Nome</span></label>
			<input type="text" placeholder="Nome" id="teste4" class="form-control input-sm">
		</div>
		<div class="row">
			<button type="button" class="btn btn-success btn-sm pull-right margin-left">Enviar</button>
			<button type="button" class="btn btn-danger btn-sm pull-right">Limpar</button>
		</div>
	</div>
</div>
<div class="row wrapper wrapper-white">
	<div class="page-header">
		<h1>Título da página</h1>
		<span>Texto descritivo da sessão da página</span>
	</div>

	<div class="col-md-12 content">
		<table class="table table-striped table-hover">
			<thead>
				<tr>
					<th>Nome</th>
					<th>Senha</th>
					<th>Confirmar Senha</th>
					<th>Ferramentas</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>antonio@titaniumtech.com.br</td>
					<td>brasil 123</td>
					<td>Brasília</td>
					<td class="tool-cell">
						<ul>
							<li><a class="laranja" href=""></a></li>
							<li><a class="verde" href=""></a></li>
							<li><a class="azul" href=""></a></li>
							<li><a class="amarelo" href=""></a></li>
							<li><a class="vermelho" href=""></a></li>
							<li><a class="cinza" href=""></a></li>
						</ul>
					</td>
				</tr>
				<tr>
					<td>antonio@titaniumtech.com.br</td>
					<td>brasil 123</td>
					<td>Brasília</td>
					<td class="tool-cell">
						<ul>
							<li><a class="laranja" href=""></a></li>
							<li><a class="verde" href=""></a></li>
							<li><a class="azul" href=""></a></li>
							<li><a class="amarelo" href=""></a></li>
							<li><a class="vermelho" href=""></a></li>
							<li><a class="cinza" href=""></a></li>
						</ul>
					</td>
				</tr>
				<tr>
					<td>antonio@titaniumtech.com.br</td>
					<td>brasil 123</td>
					<td>Brasília</td>
					<td class="tool-cell">
						<ul>
							<li><a class="laranja" href=""></a></li>
							<li><a class="verde" href=""></a></li>
							<li><a class="azul" href=""></a></li>
							<li><a class="amarelo" href=""></a></li>
							<li><a class="vermelho" href=""></a></li>
							<li><a class="cinza" href=""></a></li>
						</ul>
					</td>
				</tr>
				<tr>
					<td>antonio@titaniumtech.com.br</td>
					<td>brasil 123</td>
					<td>Brasília</td>
					<td class="tool-cell">
						<ul>
							<li><a class="laranja" href=""></a></li>
							<li><a class="verde" href=""></a></li>
							<li><a class="azul" href=""></a></li>
							<li><a class="amarelo" href=""></a></li>
							<li><a class="vermelho" href=""></a></li>
							<li><a class="cinza" href=""></a></li>
						</ul>
					</td>
				</tr>
				<tr>
					<td>antonio@titaniumtech.com.br</td>
					<td>brasil 123</td>
					<td>Brasília</td>
					<td class="tool-cell">
						<ul>
							<li><a class="laranja" href=""></a></li>
							<li><a class="verde" href=""></a></li>
							<li><a class="azul" href=""></a></li>
							<li><a class="amarelo" href=""></a></li>
							<li><a class="vermelho" href=""></a></li>
							<li><a class="cinza" href=""></a></li>
						</ul>
					</td>
				</tr>
				<tr>
					<td>antonio@titaniumtech.com.br</td>
					<td>brasil 123</td>
					<td>Brasília</td>
					<td class="tool-cell">
						<ul>
							<li><a class="laranja" href=""></a></li>
							<li><a class="verde" href=""></a></li>
							<li><a class="azul" href=""></a></li>
							<li><a class="amarelo" href=""></a></li>
							<li><a class="vermelho" href=""></a></li>
							<li><a class="cinza" href=""></a></li>
						</ul>
					</td>
				</tr>
				<tr>
					<td>antonio@titaniumtech.com.br</td>
					<td>brasil 123</td>
					<td>Brasília</td>
					<td class="tool-cell">
						<ul>
							<li><a class="laranja" href=""></a></li>
							<li><a class="verde" href=""></a></li>
							<li><a class="azul" href=""></a></li>
							<li><a class="amarelo" href=""></a></li>
							<li><a class="vermelho" href=""></a></li>
							<li><a class="cinza" href=""></a></li>
						</ul>
					</td>
				</tr>
			</tbody>
		</table>
	</div>


</div>
