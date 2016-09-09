$(document).ready(function(){
	//OCULTAR SIDEBAR
	var body = $('body');
	var sidebar = $('.page-sidebar');

	if ((body.hasClass("page-sidebar-hover-on") && body.hasClass('page-sidebar-fixed')) || sidebar.hasClass('page-sidebar-hovering')) {
	    body.removeClass('page-sidebar-hover-on');
	    sidebar.css('width', '').hide().show();
	    e.stopPropagation();
	    runResponsiveHandlers();
	}

	$(".sidebar-search", sidebar).removeClass("open");

	if (body.hasClass("page-sidebar-closed")) {
	    body.removeClass("page-sidebar-closed");
	    if (body.hasClass('page-sidebar-fixed')) {
	        sidebar.css('width', '');
	    }
	} else {
	    body.addClass("page-sidebar-open");
	}


	// MARCAR DE VERMELHO O MENU PAI AONDE UM DOS MENUS FILHOS ESTIVER SELECIONADO
	var query = $('ul.page-sidebar-menu li.active');

	do{
		query = query.parent('li, ul:not(.page-sidebar-menu)');

	}while (query.parent('li, ul:not(.page-sidebar-menu)').length == 1)

	query.addClass("active");


	//REMOVER MENUS COM FILHOS MAS QUE O USUÁRIO NÃO TEM PERMISSÃO SOBRE ELES
	$( ".page-sidebar-menu" ).children("li:has(ul)").not(":has(li)").remove();
});
