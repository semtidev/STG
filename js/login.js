$(document).ready(function() {
    $(function() {
        var imagenes = ['bg2.jpg'];
        $('body').css({'background-image': 'url(resources/images/bg/' + imagenes[Math.floor(Math.random() * imagenes.length)] + ')'});
    });
    $('#username_id').focus();
    $('#login').show().animate({opacity: 1}, 2000);
    $('.logo').show().animate({opacity: 1, top: '30%'}, 800, function() {
        $('.logo').show().delay(1200).animate({opacity: 1, top: '5%'}, 300, function() {
            $('.formLogin').animate({opacity: 1, left: '0'}, 300);
            $('.userbox').animate({opacity: 0}, 200).hide();
        });
    });
    $(".on_off_checkbox").iphoneStyle();
    $('.tip a ').tipsy({gravity: 'sw'});
    $('.tip input').tipsy({trigger: 'focus', gravity: 'w'});
});
$('.userload').click(function(e) {
    $('.formLogin').animate({opacity: 1, left: '0'}, 300);
    $('.userbox').animate({opacity: 0}, 200, function() {
        $('.userbox').hide();
    });
});


function Login() {

    $("#login").animate({opacity: 1, top: '49%'}, 200, function() {
        $('.userbox').show().animate({opacity: 1}, 500);
        $("#login").animate({opacity: 0, top: '60%'}, 500, function() {
            $(this).fadeOut(200, function() {
                $(".text_success").slideDown();
                $("#successLogin").animate({opacity: 1, height: "200px"}, 500);
            });
        })
    })
    setTimeout("window.location = 'default'", 800);
    //window.location.href='semti.html';
}


$('#alertMessage').click(function() {
    hideTop();
});

function showError(str) {
    $('#alertMessage').addClass('error').html(str)/*.stop(true, true).show().animate({opacity: 1, right: '10'}, 500)*/;

}

function showSuccess(str) {
    $('#alertMessage').removeClass('error').html(str)/*.stop(true, true).show().animate({opacity: 1, right: '10'}, 500)*/;
}

function hideTop() {
    $('#alertMessage').animate({opacity: 0, right: '-20'}, 500, function() {
        $(this).hide();
    });
}
function loading(name, overlay) {
    $('body').append('<div id="overlay"></div><div id="preloader">' + name + '..</div>');
    if (overlay == 1) {
        $('#overlay').css('opacity', 0.1).fadeIn(function() {
            $('#preloader').fadeIn();
        });
        return  false;
    }
    $('#preloader').fadeIn();
}
function unloading() {
    $('#preloader').fadeOut('fast', function() {
        $('#overlay').fadeOut();
    });
}


function SendLogin() {

    if (document.formLogin.username.value == "" || document.formLogin.password.value == "")
    {
        showError("Por favor teclee Usuario y Contrase&ntilde;a");
        $('.inner').jrumble({x: 4, y: 0, rotation: 0});
        $('.inner').trigger('startRumble');
        setTimeout('$(".inner").trigger("stopRumble")', 500);
        setTimeout('hideTop()', 5000);
        return false;
    }
    else
    {
        hideTop();
        loading('Comprobando.', 1);
        setTimeout("unloading()", 2000);

        var values = $('#formLogin').serialize();
        $.ajaxSetup({cache: false});
        $.ajax({
            cache: false,
            url: "./php/sistema/login.php",
            type: "post",
            data: values,
            success: function(msg) {
                console.log(msg);
                var msg = $.parseJSON(msg);
                if ((msg.success == true) && (msg.message == null || msg.message == '')){
                    
                    Login();
                }
                else {
                    showError(msg.message);
                    $('.inner').jrumble({x: 4, y: 0, rotation: 0});
                    $('.inner').trigger('startRumble');
                    setTimeout('$(".inner").trigger("stopRumble")', 500);
                    setTimeout('hideTop()', 5000);
                    return false;
                }
            },
            error: function() {
                showError("Ha ocurrido un error en la operción.");
                $('.inner').jrumble({x: 4, y: 0, rotation: 0});
                $('.inner').trigger('startRumble');
                setTimeout('$(".inner").trigger("stopRumble")', 500);
                setTimeout('hideTop()', 5000);
                return false;
            }
        });
        //setTimeout( "Login()", 2500 );
    }
}

