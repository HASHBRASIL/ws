var callbackTableFilter;

$(document).ready(function(){



    procedimentosPesquisaTabelaPadrao();

    function procedimentosPesquisaTabelaPadrao()
    {
        var bloqueadoNovaPesquisa = false;

        $('.table th input[type="text"]').each(function(){
            if($(this).val() != ''){
                $('.table th input[type="text"]').show();
                $(this).parent().parent().children('span').css('color', '#F6FFA1');
            }
        });

        function getFilterParams(newData)
        {
            var data = {};

            data['page']    = $('.pagination .active .ng-binding').html();
            data['itens']   = $('.ng-table-counts .active .ng-binding').html();
            data['search'] = $('.list-filter input').val();
            data['searchFields'] = {};

            $('.table th input[type="text"]').each(function(){
                data['searchFields'][$(this).attr('campo')] = $(this).val();
            });

            $('.table .fa').each(function(){
                var retorno = getOrderIco($(this));
                if(retorno['order']){
                    data['order'] = retorno['order'];
                    data['orderby'] = retorno['orderby'];
                }
            });

            for(var a in newData)
            {
                data[a] = newData[a];
            }
            return data;

            function getOrderIco(ico)
            {
                if(ico.hasClass('fa-sort-asc')){
                    return {'orderby' : ico.attr('campo'), 'order' : 'ASC'};
                }else if(ico.hasClass('fa-sort-desc')){
                    return {'orderby' : ico.attr('campo'), 'order' : 'DESC'};
                }else{
                    return {};
                }
            }
        }

        $('body').on('click', '.table th span', function(){

            if($(this).parent().find('input').css('display') == 'none'){
                $(this).parent().parent().find('input[type="text"]').slideDown().removeAttr('disabled');
            }else{
                $(this).parent().parent().find('input[type="text"]').slideUp().attr('disabled', 'disabled');
            }
        });

        $('body').on('submit', '.list-filter form', function(e){

            if(bloqueadoNovaPesquisa == true){ return true; } else { bloqueadoNovaPesquisa = true; }

            var data = getFilterParams({
                'servico' : getParameterByName('servico'),
                'id' : getParameterByName('id')
            });
            for(var a in data)
            {
                if(a == 'searchFields'){
                    for(var s in data[a])
                    {
                        $(this).prepend('<input type="hidden" name="searchFields['+s+']" value="'+data[a][s]+'"/>');
                    }
                }else{
                    $(this).prepend('<input type="hidden" name="'+a+'" value="'+data[a]+'"/>');
                }
            }

            if(typeof(callbackTableFilter) == 'function'){
                e.preventDefault();
                callbackTableFilter($.param(data));
            }else{

                var data2 = getUrlVars();

                for (var i in data2) {
                    if (!data[i]) {
                        data[i] = data2[i];
                    }
                }

                window.location = self.location.origin+self.location.pathname+'?'+$.param(data);
            }
        });

        $('body').on('click', '.ng-table-counts button', function(e){

            e.preventDefault();
            if(bloqueadoNovaPesquisa == true){ return true; } else { bloqueadoNovaPesquisa = true; }

            var data = getFilterParams({
                'servico' : getParameterByName('servico'),
                'id' : getParameterByName('id'),
                'itens' : $(this)[0].innerText
            });

            if(typeof(callbackTableFilter) == 'function'){
                e.preventDefault();
                callbackTableFilter($.param(data));
            }else{
                var data2 = getUrlVars();

                for (var i in data2) {
                    if (!data[i]) {
                        data[i] = data2[i];
                    }
                }
                window.location = self.location.origin+self.location.pathname+'?'+$.param(data);
            }
        });

        $('body').on('click', '.pagination a', function(e){

            e.preventDefault();
            if(bloqueadoNovaPesquisa == true){ return true; } else { bloqueadoNovaPesquisa = true; }

            var data = getFilterParams({
                'servico' : getParameterByName('servico'),
                'id' : getParameterByName('id'),
                'page' : $(this)[0].innerText
            });

            if(typeof(callbackTableFilter) == 'function'){
                e.preventDefault();
                callbackTableFilter($.param(data));
            }else{
                var data2 = getUrlVars();

                for (var i in data2) {
                    if (!data[i]) {
                        data[i] = data2[i];
                    }
                }
                window.location = self.location.origin+self.location.pathname+'?'+$.param(data);
            }
        });

        $('body').on('click', '.table th .fa', function(){

            if(bloqueadoNovaPesquisa == true){ return true; } else { bloqueadoNovaPesquisa = true; }

            if($(this).hasClass('fa-sort')){
                $(this).removeClass('fa-sort');
                $(this).addClass('fa-sort-desc');

                var sort = {'orderby' : $(this).attr('campo'), 'order' : 'DESC'};

            }else if($(this).hasClass('fa-sort-desc')){
                $(this).removeClass('fa-sort-desc');
                $(this).addClass('fa-sort-asc');

                var sort = {'orderby' : $(this).attr('campo'), 'order' : 'ASC'};

            }else if($(this).hasClass('fa-sort-asc')){
                $(this).removeClass('fa-sort-asc');
                $(this).addClass('fa-sort');

                var sort = {};
            }

            var data = getFilterParams({
                'servico' : getParameterByName('servico'),
                'id' : getParameterByName('id'),
                'orderby' : sort['orderby'],
                'order' : sort['order']
            });

            $('.table .fa').unbind('click');

            if(typeof(callbackTableFilter) == 'function'){
                callbackTableFilter($.param(data));
            }else{
                var data2 = getUrlVars();

                for (var i in data2) {
                    if (!data[i]) {
                        data[i] = data2[i];
                    }
                }

                window.location = self.location.origin+self.location.pathname+'?'+$.param(data);
            }
        });

        $('body').on('submit', '.table th form', function(e){

            if(bloqueadoNovaPesquisa == true){ return true; } else { bloqueadoNovaPesquisa = true; }

            var data = getFilterParams({
                'servico' : getParameterByName('servico'),
                'id' : getParameterByName('id')
            });

            var data2 = getUrlVars();

            for (var i in data2) {
                if (!data[i]) {
                    data[i] = data2[i];
                }
            }

            for(var a in data)
            {
                if(a == 'searchFields'){
                    for(var s in data[a])
                    {
                        $(this).prepend('<input type="hidden" name="searchFields['+s+']" value="'+data[a][s]+'"/>');
                    }
                }else{
                    $(this).prepend('<input type="hidden" name="'+a+'" value="'+data[a]+'"/>');
                }
            }

            if(typeof(callbackTableFilter) == 'function'){
                e.preventDefault();
                callbackTableFilter($.param(data));
            }else{
                window.location = self.location.origin+self.location.pathname+'?'+$.param(data);
            }
        });

        $('body').on('click', '.list-filter-close-button', function(e){
            e.preventDefault();
            $('.list-filter').slideToggle();
        });

        $('body').on('click', '.filter-toggle', function(e){
            e.preventDefault();
            $('.list-filter').slideToggle();
            //$('.content-wrapper').mCustomScrollbar('scrollTo',['top',null]);
        });
    }

    $('body').on('click', '.table th label[for="checkbox-todos"]', function(e){

        var table = $(this).parent().parent().parent().parent().parent();

        var input = $(this).parent().children('input');

        if(input.prop('check') == undefined || input.prop('check') == false){
            table.find('td input[type="checkbox"]').each(function(){
                $(this).prop('checked', true);
            });

            input.prop('check', '1');

        }else{
            table.find('td input[type="checkbox"]').each(function(){
                $(this).prop('checked', false);
            });
            input.prop('check', false);
        }
    });

    $('body').on('click', '.formaction', function(e){
        //console.log('ronaldo');
        // return false;
        e.preventDefault();
        var form          = $(this).closest('form');
        var servico       = $(this).attr('data-servico');
        var show          = $(this).attr('data-show');

        data = new FormData();//seleciona classe form-horizontal adicionada na tag form do html
        $(form).find(':input').not('select.select2-hidden-accessible').each(function(){
            if($(this).attr('type') == 'checkbox'){
                if($(this).is(':checked') == true) {
                    data.append($(this).attr('name'), true);
                } else {
                    data.append($(this).attr('name'), false);
                }
            }else if ($(this).attr('type') == 'file') {
                if($(this).get(0).files[0] != 'undefined'){
                    data.append($(this).attr('name'),$(this).get(0).files[0]);
                }
            }else {
                if($(this).val() != ""){
                    data.append($(this).attr('name'), $(this).val());
                }
            }
        });
        $(form).find('textarea').each(function(){
            var nome = $(this).attr('name');
            var arrayNome = nome.split('_');
            if (typeof CKEDITOR !== "undefined" && typeof CKEDITOR.instances[arrayNome[0]] !== "undefined" ) {
                var texto = CKEDITOR.instances[arrayNome[0]].getData();
                if(texto != ""){
                    data.append(nome, texto);
                }
            } else {
                data.append(nome, $(this).val());
            }

        });

        contentType = false;
        processData = false;

        if(show != 'reload') {
            var ret = ajaxFunction(servico, data, show);
        } else {
            idservico = $(form).find("input[name*='servico']").val(servico);
            $(form).attr('action','/home.php?servico='+servico);
            $(form).submit();
        }
    });

    $('body').on('change', 'select.select2-skin', function(){
        $('input[type=hidden][name=' +$(this).attr('name')+']').val($(this).find('option:selected').val());
    });

    $('body').on('change', 'select.select-multiple', function(){
        var valor = "";
        var contador = 0;
        $(this).find('option:selected').each(function(){
            if (contador > 0)
                valor += "|";
            valor += $(this).val();
            contador++;
        });
        console.log(valor);
        $('input[type=hidden][name=' +$(this).attr('name')+']').val(valor);
    });

    //$('.{{ campo.metanome|lower }}').on("change", function(e) {
    //    $("input[type=hidden][name={{ campo.id }}_{{ campo.metanome }}]").val( $('.{{ campo.metanome|lower }} option:selected').val() );
    //});

    //$('body').on('click', '[data-comportamento=action][data-show="dropdown"]', function(e){
    //    e.preventDefault();
    //    var form           = $(this).closest('form');
    //    var servico        = $(this).attr('data-servico');
    //    var comportamento  = $(this).attr('data-comportamento');
    //    var titulo         = $(this).attr('data-titulo');
    //    var objBotoesModal = new Object();
    //
    //    if ( typeof modalBTNS == 'function' ) {
    //        var modalBtn = modalBTNS($(this).attr('data-metanome'));
    //        $.each(modalBtn['filhos'], function(i, v){
    //            objBotoesModal[v.metanome] = {  label:     v.nome,
    //                className: v.ws_style,
    //                callback:  function () {
    //                    var form = $('.modal-body').find('form').serializeArray();
    //                    ajaxBtnModal(v.id, form);
    //                }
    //            }
    //        });
    //    }
    //
    //    if (typeof data_modal != "object") {
    //        data_modal = null;
    //    }
    //
    //    $.ajax({
    //        type: "GET",
    //        url:  "home.php?servico=" + servico,
    //        data: data_modal,
    //        success: function(response, status, XMLHttpRequest) {
    //
    //            if (processAjaxSuccess(response, status, XMLHttpRequest) == false) {
    //                return;
    //            }
    //
    //            bootbox.dialog({
    //                title:   titulo || 'Dropdown',
    //                message: response,
    //                buttons: objBotoesModal,
    //                className: 'bootbox-large',
    //                onEscape: function() {
    //                    if( typeof customEscape == 'function'){
    //                        customEscape();
    //                    }else{
    //                    }
    //                }
    //            });
    //
    //            $(document).trigger('custom');
    //
    //        }
    //    });
    //});


    //$('body').on('click', '[data-show="modal"]', function(e){
    $('[data-show="modal"]').click(function(e){
        e.preventDefault();
        var form           = $(this).closest('form');
        var servico        = $(this).attr('data-servico');
        var comportamento  = $(this).attr('data-comportamento');
        var titulo         = $(this).attr('data-titulo');
        var objBotoesModal = new Object();

        if ( typeof modalBTNS == 'function' ) {
            var modalBtn = modalBTNS($(this).attr('data-metanome'));
            $.each(modalBtn['filhos'], function(i, v){
                objBotoesModal[v.metanome] = {  label:     v.nome,
                    className: v.ws_style,
                    callback:  function () {
                        var form = $('.modal-body').find('form').serializeArray();
                        ajaxBtnModal(v.id, form);
                    }
                }
            });
        }


        var data_modal = {'checkbox':{}};
        if (typeof data_modal != "object") {
           //data_modal = null;
        }
        i=0;
        $( "input[type=checkbox]" ).each(function( index ) {
            if($( this ).is(':checked' )) {
                data_modal['checkbox'][i] = $(this).attr('id');
                i++;
            }
        });
        console.log(data_modal)
        $.ajax({
            type: "POST",
            url:  "home.php?servico=" + servico,
            data: data_modal,
            success: function(response, status, XMLHttpRequest) {

                if (processAjaxSuccess(response, status, XMLHttpRequest) == false) {
                    return;
                }

                bootbox.dialog({
                    title:   titulo,
                    message: response,
                    buttons: objBotoesModal,
                    className: 'bootbox-large',
                    onEscape: function() {
                        if( typeof customEscape == 'function'){
                            customEscape();
                        }else{
                            console.log('teste');
                        }
                    }
                });
                $(document).trigger('custom');
            }
        });
    });

    function ajaxBtnModal (servico, data) {
        $.ajax({
            type: "POST",
            url:  "home.php?servico=" + servico,
            data: { data: data },
            success: function (resposta) {
                var dataModal = resposta['dataModal'];

                $.each(dataModal, function(i, v){
                    $('[name="'+ v.name +'"]').attr(v.attr, v.value);
                });
            }
        });
    }

    $('.action').on('click', function(e){
        var servico       = $(this).attr('data-servico');
        var show          = $(this).attr('data-show');

        switch(show) {
            case 'ajax':
            // break omitido intencionalmente
            case 'modal':
                var ret = ajaxFunction(servico, null, show);
                break;
            case 'reload':
                window.location.href = "home.php?servico=" + servico;
                break;
            default:
                break;
        }

    });

    $('.multiaction').on('click', function(e){
        var servico       = $(this).attr('data-servico');
        var show          = $(this).attr('data-show');

        switch(show) {
            case 'ajax':
            // break omitido intencionalmente
            case 'modal':
                var data_modal = {'checkbox':{}};
                i=0;
                $( "input[type=checkbox]" ).each(function( index ) {
                    if($( this ).is(':checked' )) {
                        data_modal['checkbox'][i] = $(this).attr('id');
                        i++;
                    }
                });
                console.log(data_modal)
                $.ajax({
                    type: "POST",
                    url:  "home.php?servico=" + servico,
                    data: data_modal,
                    success: function(response, status, XMLHttpRequest) {
                       if (typeof response == 'string') {
                            response = $.parseJSON(response);
                         }

                         if (!processAjaxSuccess(response, status, XMLHttpRequest)) {
                             return;
                         }


                        if (response.data.target.servico) {
                            var target = $.param(response.data.target);

                            window.location.href = "home.php?" + target;
                        }
                        return response;
                    }
                });
                break;
            case 'reload':
                window.location.href = "home.php?servico=" + servico;
                break;
            case 'dropdown':
                    var data_modal = {'checkbox':{}};
                    i=0;
                    $( "input[type=checkbox]" ).each(function( index ) {
                        if($( this ).is(':checked' )) {
                            data_modal['checkbox'][i] = $(this).attr('id');
                            i++;
                        }
                    });
                    /* depois criar uma função pra isso... agora só estou fazendo acontecer by toinsane GHP*/
                    if( $('.dropdown-content').length == 0 ){
                        var div     = $(this).closest('div');
                        var row     = $(this).closest('tr');
                        var countTd = row.find('td').length;
                        var tr      = $(document.createElement('tr'));
                        var td      = $(document.createElement('td')).attr('colspan', countTd).addClass('dropdown-content');

                        //Seta o valor do id da linha caso não seja undefined
//                        if ( $(this).closest('tr').attr('data-id') != undefined ) {
//                            data.append(field, $(this).closest('tr').attr('data-id'));
//                        }
                        //data[field] = $(this).closest('tr').attr('data-id');
                        td.appendTo(tr);
                        tr.prependTo(div);
                        $.ajax({
                            type: "GET",
                            url:  "home.php?servico=" + servico,
                            data: data_modal,
                            success: function(response, status, XMLHttpRequest) {

                                if (processAjaxSuccess(response, status, XMLHttpRequest) == false) {
                                    return;
                                }

                                td.html( response );
                                $(document).trigger('custom');
                            }
                        });
                    }
                    break;
            default:
                break;
        }

    });

    $('body').on("click", '.listaction,[data-comportamento=action][data-show="dropdown"]', function(e){

        e.preventDefault();
        var servico = $(this).attr('data-servico');
        var show    = $(this).attr('data-show');
        var confirm = $(this).attr('data-confirm');
        var comportamento = $(this).attr('data-comportamento');
        var field = $(this).attr('data-field') || 'id';
        var data = new FormData();
        if (comportamento == 'action' && show == 'dropdown') {
            show = 'dropdowntopo';
        }

        if(typeof(callbackTableFilter) == 'function'){
            e.preventDefault();
            callbackTableFilter($(this).closest('tr').attr('data-id'));
        }else{

            switch(show) {
                case 'dropdownheader':
                    /* depois criar uma função pra isso... agora só estou fazendo acontecer by toinsane GHP*/
                    if( $('.dropdown-content').length == 0 ){
                        var tbody   = $(this).closest('tbody');
                        var row     = $(this).closest('tr');
                        var countTd = row.find('td').length;
                        var tr      = $(document.createElement('tr'));
                        var td      = $(document.createElement('td')).attr('colspan', countTd).addClass('dropdown-content');

                        //Seta o valor do id da linha caso não seja undefined
//                        if ( $(this).closest('tr').attr('data-id') != undefined ) {
//                            data.append(field, $(this).closest('tr').attr('data-id'));
//                        }
                        data[field] = $(this).closest('tr').attr('data-id');
                        td.appendTo(tr);
                        tr.prependTo(tbody);
                        $.ajax({
                            type: "GET",
                            url:  "home.php?servico=" + servico,
                            data: data,
                            success: function(response, status, XMLHttpRequest) {

                                if (processAjaxSuccess(response, status, XMLHttpRequest) == false) {
                                    return;
                                }

                                td.html( response );
                                $(document).trigger('custom');
                            }
                        });
                    }
                    break;

                case 'dropdowntopo':

                    if( $('.dropdown-content').length == 0 ){

                        var botao = $(this);
                        var classeFilho = botao.children('i').attr('class');
                        botao   .addClass('active')
                                .attr('disabled', 'disabled')
                                .children('i')
                                .attr('class', 'fa active fa-spinner fa-pulse');
                        
                        var div     = $(this).closest('div').addClass('box-dropdown');
                        var row     = $(this).closest('tr');
                        var countTd = row.find('td').length;
                        var tr      = $(document.createElement('tr'));
                        var td      = $(document.createElement('td')).attr('colspan', countTd).addClass('dropdown-content');


                        var data = {};
                        if ( $(this).closest('tr').attr('data-id') != undefined ) {
                           data[field] = $(this).closest('tr').attr('data-id');
                        }

                        td.appendTo(tr);
                        tr.appendTo(div);

                        $.ajax({
                            url:  "home.php?servico=" + servico,
                            data: data,
                            success: function(response, status, XMLHttpRequest) {
                                
                                botao.removeAttr('disabled').children('i').attr('class', classeFilho);
                                
                                if (processAjaxSuccess(response, status, XMLHttpRequest) == false) { return; }

                                td.html( response );
                                $(document).trigger('custom');
                            }
                        });
                    }else{

                        $('.dropdown-content').parent().remove();
                        
                        if($(this).hasClass('active')){
                            $(this).removeClass('active');
                        }else{
                            $(this).trigger('click');
                        }
                    }
                    break;

                case 'dropdownfooter':
                    /* depois criar uma função pra isso... agora só estou fazendo acontecer by toinsane GHP*/
                    if( $('.dropdown-content').length == 0 ){
                        var tbody     = $(this).closest('tbody');
                        var row     = $(this).closest('tr');
                        var countTd = row.find('td').length;
                        var tr      = $(document.createElement('tr'));
                        var td      = $(document.createElement('td')).attr('colspan', countTd).addClass('dropdown-content');

                        //Seta o valor do id da linha caso não seja undefined
//                        if ( $(this).closest('tr').attr('data-id') != undefined ) {
//                            data.append(field, $(this).closest('tr').attr('data-id'));
//                        }
                        data[field] = $(this).closest('tr').attr('data-id');
                        td.appendTo(tr);
                        tr.appendTo(tbody);

                        $.ajax({
                            type: "GET",
                            url:  "home.php?servico=" + servico,
                            data: data,
                            success: function(response, status, XMLHttpRequest) {

                                if (processAjaxSuccess(response, status, XMLHttpRequest) == false) {
                                    return;
                                }

                                td.html( response );
                                $(document).trigger('custom');
                            }
                        });
                    }
                    break;
                case 'dropdown':
                    //console.log( $('.dropdown-content').length )
                    $(this).closest('table').find('tr').removeClass('tr-dropdown')

                    if( $('.dropdown-content').length == 0 ){
                        var botao = $(this);

                        $(this).children('i').hide();
                        //$(this).append('<i class="fa fa-refresh"></i>');
                        /* depois criar uma função pra isso... agora só estou fazendo acontecer by toinsane GHP*/
                        var row     = $(this).closest('tr');
                        row.addClass('tr-dropdown');
                        var countTd = row.find('td').length;
                        var tr      = $(document.createElement('tr'));
                        var td      = $(document.createElement('td')).attr('colspan', countTd).addClass('dropdown-content');

                        //Seta o valor do id da linha caso não seja undefined
//                        if ( $(this).closest('tr').attr('data-id') != undefined ) {
//                            data.append(field, $(this).closest('tr').attr('data-id'));
//                        }
                        var data = {};
                        data[field] = $(this).closest('tr').attr('data-id');
                        td.appendTo(tr);
                        tr.insertAfter(row);

                        $.ajax({
                            type: "GET",
                            url:  "home.php?servico=" + servico,
                            data: data,
                            success: function(response, status, XMLHttpRequest) {

                                if (processAjaxSuccess(response, status, XMLHttpRequest) == false) {
                                    return;
                                }
                                botao.children('.fa').remove()
                                botao.children('i').show();
                                td.addClass('dropdown-inline form-table');
                                td.html( response );
                                $(document).trigger('custom');
                            }
                        });
                    }else{
                        var validacao = true;
                        if($(this).closest('tr').hasClass('tr-dropdown') && $('.dropdown-content').length > 0){
                            validacao = false;
                        }
                        $('.dropdown-content').closest('tr').remove();

                        if(validacao){
                            $(this).trigger('click');
                        }
                    }
                    break;
                case 'ajax':
                // break omitido intencionalmente
                case 'modal':
                    //Seta o valor do id da linha caso não seja undefined
                    if ( $(this).closest('tr').attr('data-id') != undefined ) {
                        data.append(field, $(this).closest('tr').attr('data-id'));
                    }

                    if (confirm) {
                        bootbox.confirm(confirm, function(result) {
                            console.log(ret);
                            if (result) {

                                var ret = ajaxFunction(servico, data, show);
                            }
                        });
                    } else {
                        var ret = ajaxFunction(servico, data, show);
                    }

                    if (typeof ret !== 'undefined' && ret.data.itemremove == 1) {
                        $(this).closest('tr').remove();
                    }
                    $(document).trigger('custom');

                    break;
                case 'reload':
                    window.location.href = "home.php?servico=" + servico + "&" + field + "=" + $(this).closest('tr').attr('data-id');
                    break;
                default:
                    break;
            }
        }
    });

    $('main').on('click', '[data-duplicate]', function(e){
        e.preventDefault();

        var data        = $(this).attr('data-duplicate');
        var count       = $('[data-duplicatable^="' + data + '"]').length;
        var matrix      = $('[data-duplicatable="' + data + '_' + (count-1) +'"]');
        var clone       = matrix.clone().attr('data-duplicatable', data + '_' + count);

        if($(this).find('i').hasClass('fa-times-circle')) {
            $(this).parents("[data-duplicatable^='" + data + "']").remove();
        } else {
            clone.find('input').each(function(i, v){
                $(v).val('');
                applyMask( v );
            });
            matrix.after(clone);
        }

        createDeleteClone(data);
        transformNameIntoArray(data);

        clone.find('select').each(function(i, v){

            var dataObj       = $(v).attr('data-metanome');
            var dataDescricao = $(v).attr('data-descricao');
            var dataTags      = $(v).attr('data-tags');
            var dataServico   = $(v).attr('data-servico');

            clone.find('.select2-container').remove();
            transformSelect2(dataObj, dataDescricao, dataTags, dataServico);

        });
    });


    $(document).trigger('custom');

    // @fix para modal com select2
    $.fn.modal.Constructor.prototype.enforceFocus = function () {};


    function getParameterByName(name, url) {
        if (!url) url = window.location.href;
        name = name.replace(/[\[\]]/g, "\\$&");
        var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)", "i"),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, " "));
    }

    function getUrlVars()
    {
        var vars = [], hash;
        var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
        //console.log(hashes);

        for(var i = 0; i < hashes.length; i++)
        {
            hash = hashes[i].split('=');
            //vars.push(hash[0]);
            vars[hash[0]] = hash[1];
        }
        return vars;
    }

});

$(document).ready(function(){
    $('body').on('click', '.tool-cell .dropdown-toggle, .container-form-action .dropdown-toggle', function(){
        var estado = $(this).parent().children('.dropdown-menu').css('display');
        $('.tool-cell .dropdown-toggle, .container-form-action .dropdown-toggle').parent().children('.dropdown-menu').hide();

        if(estado === 'none'){ $(this).parent().children('.dropdown-menu').show(); }
    });

    $('body').on('click', '.block-backsidebar', function(){
        $('.area-sidebar').addClass('hidden-xs');
        $('.area-sidebar').addClass('hidden-sm');
        $('.area-sidebar').addClass('visible-lg');
        $('.area-sidebar').addClass('visible-md');
        $('.block-backsidebar').fadeOut('fast');
    });

    $('body').on('click', '.btn-menu-open', function(){

        $('.area-sidebar').removeClass('hidden-xs');
        $('.area-sidebar').removeClass('hidden-sm');
        $('.area-sidebar').removeClass('visible-lg');
        $('.area-sidebar').removeClass('visible-md');
        $('.block-backsidebar').fadeIn('fast');
    });

     $('body').on('change', "#selectEntity", function(){
        callAjax({id_grupo: $(this).children('option:selected').val()}, '#listMenu', 'includes/listMenu.php');
    });

    $('body').on('change', "#selectEntitytopo", function(){
        callAjax({id_grupo: $(this).children('option:selected').val()}, '#listMenutopo', 'includes/listMenu.php');
    });

    $('body').on('click', "#formUpdate button[type=submit]", function(e){

        $("button[type=submit]", $(this).parents("form")).removeAttr("clicked");
        $(this).attr("clicked", "true");
    });

    $('body').on('submit', "#formUpdate", function(e){

        var val = $("button[type=submit][clicked=true]").attr('id');
        if(val == 'duplicateMaster'){
            $("#formUpdate").attr('action', 'includes/duplicateDataMaster.php');
        }

        var data = $("#formUpdate").serialize();

        $.ajax({
            url: $("#formUpdate").attr('action'),
            type: 'POST',
            data: data,
        }).done(function(response) {
            if(response == 'error'){
                alert('Erro ao salvar!');
            }else{
                alert('Sucesso!');
            }
        });
        e.preventDefault();
    });

});

$(document).on('custom', function() {
   
    $('[data-toggle="tooltip"]').tooltip();

    // @todo ajustar mascara para mostrar valor mesmo sem clicar no campo.
    $('.decimal').maskMoney({prefix:'R$ ', allowNegative: true, thousands:'.', decimal:',', affixesStay: false});

    PNotify.prototype.options.styling = "bootstrap3";

    $('.datepicker-skin').datepicker({
        dateFormat: 'dd/mm/yy',
        dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado','Domingo'],
        dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
        dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
        monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
        monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez']
    });

    $('.select2-skin:not([data-done="ok"])').each(function(i, obj){
        //@todo - autocomplete ws_comportamento = 'filter';
        if ($(obj).data('comportamento') == 'filter') {
            transformSelect2($(this), $(this).data('descricao'), $(this).data('selecttags'), $(this).data('servico'));
        } else {
            $(obj).select2({width: '100%', placeholder: "Selecione" } );
        }

        // coloca um data-done="ok"
        $(this).data('done', 'ok');
    });

    $('input[data-mask]').each(function(i, v){
        applyMask(v);
    });

    $('main [data-duplicate]').each(function(i, v){
        var data = $(this).attr('data-duplicate');
        createDeleteClone(data);
        transformNameIntoArray(data);
    });

    $('.messagelist .msg').each(function(){
        msg($(this).html(), $(this).attr('data-type'), 'Aviso');
    });
});

function transportaDados (  ) {

}

function transformSelect2 (obj, descricao, tags, servico) {
    $(obj).select2({
        // placeholder: descricao || '',
        tags: tags || '',
        //allowClear: true,
        ajax: {
            url: "home.php?servico="+servico,
            dataType: 'json',
            delay: 350,
            data: function (params) {
                var query = {
                    search: params.term,
                    page: params.page
                }
                // Query paramters will be ?search=[term]&page=[page]
                return query;
            }
        },
        width: '100%'
    });
}

function transformNameIntoArray (baseId) {
    $('[data-duplicatable^="' + baseId + '"]').find('[name]').each(function(i, v){
        if( $('[data-duplicatable^="' + baseId + '"]').length == 1 ) {
            $(v).attr('name', $(v).attr('name').split('[]')[0]);
        } else {
            $(v).attr('name', $(v).attr('name').split('[]')[0] + '[]');
        }
    });
}

function createDeleteClone (baseId) {
    $('[data-duplicate^="' + baseId + '"]').each(function(i, v){
        if (i < $('[data-duplicate^="' + baseId + '"]').length - 1) {
            $(v).find('i.fa').attr('class', '').addClass('fa').addClass('fa-times-circle');
            $(v).find('span').text('Remover campo');
        }
    });

    $('[data-duplicatable^="' + baseId + '"]').each(function(i, v){
        var splitStr = $(v).attr('data-duplicatable').split('_');
        $(v).attr('data-duplicatable', splitStr[0] + '_' + i );
    });
}

function processAjaxSuccess (response, status, xhr) {
    try {
        if (typeof response == 'string') {
            response = $.parseJSON(response);
        }

        if (response.msg) {
            if (response.error) {
                msg_error(response.msg);
            } else if (response.success == false) {
                msg_error(response.msg);
            } else {
                msg_success(response.msg);
            }
        }

        if (response.error) {
            return false;
        }
    } catch (e) {
        // error
        return true;
    }

    return true;
}

function callAjax (data, container, url) {
    $.ajax({
        method: "POST",
        url: url,
        data: data
    }).done(function(rtn){
        $(container).html(rtn);
    });
}

function ajaxFunction(servico, data, show){
    $.ajax({
        type: "POST",
        url:  "home.php?servico=" + servico,
        contentType: false,
        processData: false,
        data: data,
        success: function(response, status, XMLHttpRequest) {

            if (typeof response == 'string') {
               response = $.parseJSON(response);
            }

            if (!processAjaxSuccess(response, status, XMLHttpRequest)) {
                return;
            }

            switch(show){
                case 'paginacao':
                    loadTable(response.data);
                    break;
                case 'ajax':
                    //MOSTRAR msg de sucesso / erro
                    if (response.data.target.servico) {
                        var target = $.param(response.data.target);

                        window.location.href = "home.php?" + target;
                    }
                    return response;
                    break;
                case 'modal':
                    //CHAMA MODAL E INSERE HTML NO MODAL
                    bootbox.dialog({
                        title:   'teste',
                        message: response
                    });

                    break;
                case 'reload':
                    // aqui nunca entra.
                    break;
                default:
                    break;
            }
            $('.bootbox-close-button.close').trigger('click');
        }
    });
}

function GetQueryStringParams(sParam)
{
    var sPageURL = window.location.search.substring(1);
    var sURLVariables = sPageURL.split('&');
    for (var i = 0; i < sURLVariables.length; i++)
    {
        var sParameterName = sURLVariables[i].split('=');
        if (sParameterName[0] == sParam)
        {
            return sParameterName[1];
        }
    }
}

function mostraMSG(idMensagem, idTexto, sucesso)
{
    // Ao clicar na mensagem retornada, esconder.
    $( idMensagem ).click(function() {
        $( idMensagem ).fadeOut( "slow" );
    });

    switch( idTexto ) {
        case 1:
            texto = "Dados salvos com sucesso.";
            break;
        case 2:
            texto = "Dados atualizados com sucesso.";
            break;
        case 3:
            texto = "Dados duplicados com sucesso.";
            break;
        case 4:
            texto = "Ocorreu algum erro, entre em contato com o suporte.";
            break;
        default:
            texto = idTexto;
    }

    if(sucesso == true){
        // Mensagem de sucesso
        $( idMensagem ).removeClass('hidden').show();
        $( idMensagem ).text( texto );

    }else{
        // Mensagem de erro
        $( idMensagem ).removeClass("hidden").removeClass("alert-success").addClass("alert-danger").show();
        $( idMensagem ).text( texto );
    }
}

/**
*   form:           Formulário que esta sendo trabalhado
*   formSerialize:  Formulário serializado
*   formData:       Objeto FormData()
**/
function getCKEditorData( form, formSerialize, formData )
{
    // Percorre todos os textareas do form
    $.each( $(form).find( "textarea" ), function ( i, v ) {
        // atribui a variavel editor a instancia do ckeditor pega pelo id do textarea
        // onde v é todo o objeto textarea
        editor = CKEDITOR.instances[$( v ).attr( 'name' )].getData();
        //console.log( editor );
        // percorre todo o array arData
        $.each( formSerialize, function ( indice, value ) {
            // e pergunta se o name do arData é o mesmo do textarea
            if ( value['name'] == $( v ).attr( 'name' ) ) {
                // caso seja, no indice que bateu de o name do arData e o name do textarea são iguas
                // ele atribui o valor retornado pela variavel editor.
                if (formData) {
                    formData.append( value['name'], editor );
                }else{
                    formSerialize[indice]['value'] = editor;
                }
            }
        });
    });
}

function loadTable(data)
{
    var tr = $('table.paginacao tbody tr:first');

    $.each(data, function(i, row) {
        var clone = tr.clone();
        $.each(clone.find('td'), function(k,v) {
            if ($(v).data('checkbox') == 1) {
                $(v).find('label').attr('for', 'checkbox_' + row.id);
                $(v).find('input').attr('id', 'checkbox_' + row.id);
            } else if ($(v).data('field')) {
                $(v).html(row[$(v).data('field')]);
            }
        });
        $(clone).removeAttr('style');
        $(clone).attr('data-id', row.id);

        $('table.paginacao > tbody').append(clone);
    });
}

function msg (message, type, title)
{
    title = title || "Aviso!";

    new PNotify({
        title: title,
        text: message,
        hide: false,
        type: type,
        buttons: {
            sticker: false,
            closer_hover: false
        }
    });
}

function msg_error(message)
{
    msg(message, 'error');
}

function msg_success(message)
{
    msg(message, 'success');
}

function msg_warning(message)
{
    msg(message);
}

function msg_info(message)
{
    msg(message, 'info');
}

function applyMask(obj){
    var mask = $(obj).attr('data-mask');
    $(obj).inputmask({ mask: mask , greedy: false});
}

function inArray(valor, array) {
    var length = array.length;
    for(var i = 0; i < length; i++) {
        if(array[i] == valor) return true;
    }
    return false;
}
