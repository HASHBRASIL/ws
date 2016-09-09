$( document ).ready(function() {
    // Email
    $( '#time-email' ).on( 'click', function() {
        $.post( "validations.php", $( '.time-email' ).serializeArray() )
    });

    // Time
    $( '#time-nometime' ).on( 'click', function() {
        $.post( "validations.php", $( '.time-nometime' ).serializeArray() )
    });

    // Alias
    $( '#time-alias' ).on( 'click', function() {
        $.post( "validations.php", $( '.time-alias' ).serializeArray() )
    });

    // Usuario
    $( '#time-nome' ).on( 'click', function() {
        $.post( "validations.php", $( '.time-nome' ).serializeArray() )
    });

    // Verifica email/pessoa
    $( ".time-email" ).bind('submit', function( e ){
        e.preventDefault();
        var data = $( this ).serializeArray();
        if( data[0]['value'].length != 0 ){
            $.post( $(this).attr('action'), data )
                .done(function( res ) {
                    if( res == 0 ){
                        $('.retorno').append('<p style="color:red">Email já cadastrado, gostaria de fazer o <a href="'+'redirectLogin.php">Login</a><p>');
                    }
                    else{
                        window.location = res ;
                    }
                });
        }
    });

    // Cadastra novo time
    $( ".novoTime" ).submit(function( e ) {
        e.preventDefault();
        var data = $( this ).serializeArray();
        $.post( "insert.php", data )
            .done(function( res ) {
                var rtn = JSON.parse( res );

                console.log( rtn );

                switch( rtn ) {
                    case 0:
                        $('.retorno').append('<p style="color:red">Ops ocorreu algum erro, <a href="/">tente novamente</a> <p>');
                        break;
                    case 1:
                        window.location = "convites.php";
                    case 10:
                        $('.retorno').append('<p style="color:red">Alias e/ou Nome já cadastrado, tente outro.<p>');
                        break;
                }
            });
    });

    // Verifica alias
    $( ".time-alias" ).bind('submit', function( e ){
        e.preventDefault();
        var data = $( this ).serializeArray();
        if( data[0]['value'].length != 0 ){
            $.post( "validations.php", data )
                .done(function( res ) {
                    if( res == 0 ){
                        $('.retorno').append('<p style="color:red">Alias já cadastrado, por favor tente outro.<p>');
                    }
                    else{
                        window.location = res ;
                    }
                });
        }
    });

    // Verifica usuário
    $( ".time-nome" ).bind('submit', function( e ){
        e.preventDefault();
        var data = $( this ).serializeArray();
        if( data[0]['value'].length != 0 ){
            $.post( "validations.php", data )
                .done(function( res ) {
                    console.log( res );
                    if( res == 0 ){
                        $('.retorno').append('<p style="color:red">Usuário já cadastrado, por favor tente outro.<p>');
                    }
                    else{
                        window.location = res ;
                    }
                });
        }
    });

});