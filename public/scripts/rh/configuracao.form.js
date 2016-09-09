$(document).ready(function(){
	$('select.nivel_1').attr('name', 'nivel1[]');
	$('select.nivel_2').attr('name', 'nivel2[]');
	$('body').on('click', 'button.nivel1.add', function(e){
		e.preventDefault();
		var select = $(this).siblings('select');
		var html = '<div class="input-append hidden-phone nivel">'+
					'<select class="m-wrap medium nivel_1" name="nivel1[]">'+select.html()+'</select>'+  
        			'<button class="btn red remove nivel1">-</button>'+
					'</div>';
		$(this).parent().parent().append(html);console.log($(this).parent().parent().find('select:last'));
		$(this).parent().parent().find('select:last').val('');
	});
	
	$('body').on('click', 'button.nivel1.remove', function(e){
		e.preventDefault();
		$(this).parent().remove();
	});

	$('body').on('click', 'button.nivel2.add', function(e){
		e.preventDefault();
		var select = $(this).siblings('select');
		var html = '<div class="input-append hidden-phone nivel">'+
					'<select class="m-wrap medium nivel_2" name="nivel2[]">'+select.html()+'</select>'+  
        			'<button class="btn red remove nivel2">-</button>'+
					'</div>';
		$(this).parent().parent().append(html);
		$(this).parent().parent().find('select:last').val('');
	});
	
	$('body').on('click', 'button.nivel2.remove', function(e){
		e.preventDefault();
		$(this).parent().remove();
	});
	
	$('body').on('change', 'select.nivel_1', function(){
		var id_usuario = $(this).val();
		if($('select.nivel_2 option[value="'+id_usuario+'"]').is(':selected')){
			alert("Este usuário já foi selecionado no Nível 2");
			$('select.nivel_1').val('');
		}
	});	
	$('body').on('change', 'select.nivel_2', function(){
		var id_usuario = $(this).val();
		if($('select.nivel_1 option[value="'+id_usuario+'"]').is(':selected')){
			alert("Este usuário já foi selecionado no Nível 1");
			$('select.nivel_2').val('');
		}
	});
});