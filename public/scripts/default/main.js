$(document).ready(function(){
    Column();
});

function Column()
{
    var maxMenuItens = 4;
    preceduresMenuButton();
    preoceduresMenuBody();

    $('body').on('click','.coluna-row-data .fa-star', function(){
       $(this).removeClass('fa-star').addClass('fa-star-o');
    });
    
    $('body').on('click','.coluna-row-data .fa-star-o', function(){
       $(this).removeClass('fa-star-o').addClass('fa-star');
    });

    
    function preoceduresMenuBody()
    {
        $('body').on('click','.data-field a', function()
        {
            if($(this).parent('div').children('.modules-list').is(":visible")){
                $(this).parent('div').children('.modules-list').hide();
            }else{
                $('.modules-list').hide();
                $(this).parent('div').children('.modules-list').show();
            }
        });
    }

    function preceduresMenuButton()
    {
        changeColMenu('menu-debug');
        
        $('.menu-header-item').click(function(){
            changeColMenu($(this).attr('id'));
        });
        
        $('.btn-lista-modulos').click(function(){
            $('#content-menu-debug .modules-list').hide();
            $('.menu-header-item .modules-list').toggle();
        });

        $('body').on('click', '.menu-header-item .modules-list ul li', function(){

            var menu = $(this).attr('menu');
            var check = checkMenuEvaluability('menu-'+menu);
            console.log(menu+' >> '+check)
            switch (check){
                case 'limite':

                        var lastOne = null;

                        $('.ico-menu').each(function(){
                            lastOne = $(this); 
                        });

                        var nameLastOne = lastOne.parent('div').attr('id').substr(5);
                        removeMenuItem(nameLastOne);

                        addMenuItem(menu, $(this).attr('ico'));

                        $(this).children('.fa-positionRight').attr('class', 'fa fa-check-circle-o active fa-positionRight');

                        changeColMenu('menu-'+menu);

                    break;
                case 'semcorrespondencia':

                        addMenuItem(menu, $(this).attr('ico'));

                        $(this).children('.fa-positionRight').attr('class', 'fa fa-close fa-positionRight');

                         changeColMenu('menu-'+menu);

                    break;
                case 'encontrado':

                    removeMenuItem(menu);
                    $(this).children('.fa-positionRight').attr('class', 'fa fa-check-circle-o active fa-positionRight');

                    /*
                    $('#menu-'+menu).fadeOut(500, function(){
                        $('#menu-'+menu).fadeIn(500, function(){
                            $('#menu-'+menu).fadeOut(500, function(){
                                $('#menu-'+menu).fadeIn(500, function(){

                                });
                            });
                        });
                    });*/

                    break;

            }
        });

        $('.menu-header-item .modules-list ul li').mouseenter(function(){

            var menu = $(this).attr('menu');
            var check = checkMenuEvaluability('menu-'+menu);

            if(check == 'limite' || check == 'semcorrespondencia'){

                $(this).children('.fa-positionRight').attr('class', 'fa fa-check-circle-o active fa-positionRight');

            }else if(check == 'encontrado'){

                $(this).children('.fa-positionRight').attr('class', 'fa fa-close fa-positionRight');

            }

        }).mouseout(function(){
            var menu = $(this).attr('menu');

            var check = checkMenuEvaluability('menu-'+menu);

            if(check == 'limite' || check == 'semcorrespondencia'){
                $(this).children('.fa-positionRight').attr('class', 'fa fa-positionRight');
            }else if(check == 'encontrado'){
                $(this).children('.fa-positionRight').attr('class', 'fa fa-check-circle-o fa-positionRight');
            }
        });

        function addMenuItem(name, ico)
        {
            var primeiro = null;
            $('.ico-menu').each(function(){
                if(primeiro == null){
                    if($(this).attr('class') == 'ico-menu'){
                        primeiro = $(this);
                        primeiro.attr('class', 'fa ico-menu '+ico);        
                        primeiro.parent('div').attr('id', 'menu-'+name);
                    }
                }
            });
            //$('#content-menu-'+name).hide();
        }

        function removeMenuItem(name)
        {
            console.log('removing '+name)
            $('#menu-'+name).children('i').attr('class', 'ico-menu');
            $('#menu-'+name).removeClass('active')
            $('#menu-'+name).attr('id', '');
            $('#content-menu-'+name).hide();
        }

        function checkMenuEvaluability(name)
        {
            var count = 0;
            var encontrado = false;

            $('.ico-menu').each(function(){
                //console.log(name+' '+$(this).parent('div').attr('id')+' '+(name == $(this).parent('div').attr('id')))
                if(name == $(this).parent('div').attr('id')){
                    encontrado = true;
                }
                if($(this).hasClass('fa')){
                    ++count;
                }
            });

            if(encontrado){
                return 'encontrado';
            }

            if(count >= maxMenuItens){
                return 'limite'
            }

            return 'semcorrespondencia';
        }

        function changeColMenu(menuName)
        {
            if(menuName == undefined){ return true; }

            $('.menu-body').hide();
            $('.menu-header-item').removeClass('active');

            $('#content-'+menuName).show();
            $('#'+menuName).addClass('active');
        }
    }
}

