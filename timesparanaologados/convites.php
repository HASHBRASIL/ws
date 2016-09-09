<?php include_once 'header.php'; ?>
<div class="convites">
    <h1>Enviar Convite:</h1>
    <form action="insertConvite.php" method="post" class="convite-email">
        <div id="listas">
            <div>
                Email: <input type="email" name="convite[]">
            </div>
        </div>
        <input type="button" id="add_field" value="+ adicionar" style="margin-top: 10px">
        <input type="submit" value="Salvar" >
    </form>
    <div class="retorno"></div>
</div>
<?php include_once 'footer.php'; ?>
<script>
    // Cria e remove botões
    var x = 0;
    $('#add_field').click(function(e) {
        e.preventDefault(); //prevenir novos clicks
        $('#listas').append('<div>Email: <input type="email" name="convite[]"><button class="remover_campo">X</button></div>');
        x++;
    });
    // Remover o div anterior
    $('#listas').on("click", ".remover_campo", function(e) {
        e.preventDefault();
        $(this).parent('div').remove();
        x--;
    });

    $( ".convite-email" ).bind('submit', function( e ){
        e.preventDefault();
        var data      = $( this ).serializeArray();
        var arrEmails = [];
        $.each( data, function( i, v ){
            arrEmails.push( v['value'] );
        });
        // remove valores vazios em um array de strings
        var emails = $.grep( arrEmails ,function( n ){ return( n ); });

        $.post( $(this).attr( 'action' ), { 'convite': emails } )
            .done(function( res ) {
                console.log( res );
                $('.retorno').html("");
                $('.retorno').append(res);
            });
    });
</script>
