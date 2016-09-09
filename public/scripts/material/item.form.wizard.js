$(document).ready(function(){
    FormWizard.init();
});

var FormWizard = function () {

    return {
        //main function to initiate the module
        init: function () {
            if (!jQuery().bootstrapWizard) {
                return;
            }

            var form = $('#form_item');
            var error = $('.alert-error', form);
            var success = $('.alert-success', form);

            form.validate({
                errorElement: 'span', //default input error message container
                errorClass: 'help-inline', // default input error message class
                focusInvalid: false, // do not focus the last invalid input
                ignore: "",
                rules: {
                    nome: {
                        required: true
                    },
                    id_tipo_unidade_compra : {
                        required: true
                    },
                    id_tipo_unidade_consumo : {
                        required: true
                    }
                },
                messages: { // custom messages for radio buttons and checkboxes
                    nome: {
                        required: "Campo obrigatório."
                    },
                    id_tipo_unidade_compra : {
                        required: "Selecione uma unidadade de compra."
                    },
                    id_tipo_unidade_consumo : {
                        required: "Selecione uma unidadade de consumo."
                    }
                },

                invalidHandler: function (event, validator) { //display error alert on form submit              
                    App.scrollTo(form, -200);
                },

                highlight: function (element) { // hightlight error inputs
                    $(element)
                        .closest('.help-inline').removeClass('ok'); // display OK icon
                    $(element)
                        .closest('.control-group').removeClass('success').addClass('error'); // set error class to the control group
                },

                unhighlight: function (element) { // revert the change dony by hightlight
                    $(element)
                        .closest('.control-group').removeClass('error'); // set error class to the control group
                },

                success: function (label) {
                    label
                        .addClass('valid') // mark the current input as valid and display OK icon
                    .closest('.control-group').removeClass('error').addClass('success'); // set success class to the control group
                },

            });

            // default form wizard
            $('#form_wizard').bootstrapWizard({
                'nextSelector': '.button-next',
                'previousSelector': '.button-previous',
                onTabClick: function (tab, navigation, index) {
                    if (form.valid() == false) {
                        return false;
                    }
                    if($('#id_item').val() == ""){
                        alert("E necessário salvar o produto para continuar.");
                        return false;
                    }
                },
                onNext: function (tab, navigation, index) {
                    success.hide();
                    error.hide();

                    if (form.valid() == false) {
                        return false;
                    }
                    if($('#id_item').val() == ""){
                        alert("E necessário salvar o item para continuar.");
                        return false;
                    }

                    var total = navigation.find('li').length;
                    var current = index + 1;
                    // set wizard title
                    $('.step-title', $('#form_wizard')).text('Passo ' + (index + 1) + ' de ' + total);
                    // set done steps
                    jQuery('li', $('#form_wizard')).removeClass("done");
                    var li_list = navigation.find('li');
                    for (var i = 0; i < index; i++) {
                        jQuery(li_list[i]).addClass("done");
                    }

                    if (current == 1) {
                        $('#form_wizard').find('.button-previous').hide();
                    } else {
                        $('#form_wizard').find('.button-previous').show();
                    }

                    if (current >= total) {
                        $('#form_wizard').find('.button-next').hide();
                    } else {
                        $('#form_wizard').find('.button-next').show();
                    }
                    App.scrollTo($('.page-title'));
                },
                onPrevious: function (tab, navigation, index) {
                    success.hide();
                    error.hide();

                    var total = navigation.find('li').length;
                    var current = index + 1;
                    // set wizard title
                    $('.step-title', $('#form_wizard')).text('Passo ' + (index + 1) + ' de ' + total);
                    // set done steps
                    jQuery('li', $('#form_wizard')).removeClass("done");
                    var li_list = navigation.find('li');
                    for (var i = 0; i < index; i++) {
                        jQuery(li_list[i]).addClass("done");
                    }

                    if (current == 1) {
                        $('#form_wizard').find('.button-previous').hide();
                    } else {
                        $('#form_wizard').find('.button-previous').show();
                    }

                    if (current >= total) {
                        $('#form_wizard').find('.button-next').hide();
                    } else {
                        $('#form_wizard').find('.button-next').show();
                    }

                    App.scrollTo($('.page-title'));
                },
                onTabShow: function (tab, navigation, index) {
                    var total = navigation.find('li').length;
                    var current = index + 1;
                    var $percent = (current / total) * 100;
                    $('#form_wizard').find('.bar').css({
                        width: $percent + '%'
                    });
                    
                    // se clickar no tab continuará com o mesmo efeito do proximo
                    var total = navigation.find('li').length;
                    var current = index + 1;
                    // set wizard title
                    $('.step-title', $('#form_wizard')).text('Passo ' + (index + 1) + ' de ' + total);
                    // set done steps
                    jQuery('li', $('#form_wizard')).removeClass("done");
                    var li_list = navigation.find('li');
                    for (var i = 0; i < index; i++) {
                        jQuery(li_list[i]).addClass("done");
                    }

                    if (current == 1) {
                        $('#form_wizard').find('.button-previous').hide();
                    } else {
                        $('#form_wizard').find('.button-previous').show();
                    }

                    if (current >= total) {
                        $('#form_wizard').find('.button-next').hide();
                    } else {
                        $('#form_wizard').find('.button-next').show();
                    }
                    App.scrollTo($('.page-title'));
                },
                onPrevious: function (tab, navigation, index) {
                    success.hide();
                    error.hide();

                    var total = navigation.find('li').length;
                    var current = index + 1;
                    // set wizard title
                    $('.step-title', $('#form_wizard')).text('Passo ' + (index + 1) + ' de ' + total);
                    // set done steps
                    jQuery('li', $('#form_wizard')).removeClass("done");
                    var li_list = navigation.find('li');
                    for (var i = 0; i < index; i++) {
                        jQuery(li_list[i]).addClass("done");
                    }

                    if (current == 1) {
                        $('#form_wizard').find('.button-previous').hide();
                    } else {
                        $('#form_wizard').find('.button-previous').show();
                    }

                    if (current >= total) {
                        $('#form_wizard').find('.button-next').hide();
                    } else {
                        $('#form_wizard').find('.button-next').show();
                    }

                    App.scrollTo($('.page-title'));
                }
            });

            $('#form_wizard').find('.button-previous').hide();
        }

    };

}();