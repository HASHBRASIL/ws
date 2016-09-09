$(document).ready(function(){
	
	$( "#print" ).click(function() {
	  
	  $("#extratoTable_length").addClass("hidden-print");
	  $("#extratoTable_filter").addClass("hidden-print");
	  $(".dataTables_paginate").addClass("hidden-print");
	  javascript:window.print();
	  
	});
	
});