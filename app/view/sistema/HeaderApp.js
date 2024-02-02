let user_menu = "<ul class='nav navbar-nav' style='height:auto; margin-top:-2px; border:0; float:right'>" + 
                    "<li class='dropdown' style='height:53px; margin:0; border:0'>" +
                        "<a href='#' class='dropdown-toggle' data-toggle='dropdown' style='height:53px;border:0'>" +
                            "<div class='avatar' id='currentuser_avatar'></div>&nbsp;" +
                            "<div id='userbutom'>" +
                                "<span class='name' id='currentuser_name'></span><br><span class='lastname' id='currentuser_lastname'></span>" +
                            "</div>" +
                            "<div style='float:right; width:auto;'><b class='caret' style='margin-top: 10px;'></b></div>" +
                        "</a>" +
                        "<ul class='dropdown-menu'>" +
                            "<li>" +
                                "<a class='links' onClick='userPerfil()' title='Perfil de Usuario'>" +
                                    "<span class='fas fa-user-alt'></span>Mi Perfil" +
                                "</a>" +
                            "</li>" +
                            "<li>" +
                                "<a class='links' onClick='logout()' title='Finalizar Sesi&oacute;n de Usuario'>" +
                                    "<span class='fas fa-sign-out-alt'></span>Cerrar Sesi&oacute;n" +
                                "</a>" +
                            "</li>" +
                        "</ul>" +
                    "</li>" +
                "</ul>",
    polo = (localStorage.getItem('polo_name') != 'Todos') ? localStorage.getItem('polo_name') : "";

Ext.define('SEMTI.view.sistema.HeaderApp', {
    extend: 'Ext.Component',
    id: 'headerapp',
	alias: 'widget.headerapp', 
    height: 55, 
    html:"<table width=100% height='55' border='0' cellspacing='0' cellpadding='0' background='resources/images/topbg.png'><tr><td width='155'><img src='resources/images/logo/logo_topbar.png' border='0'/></td><td class='title_topbar'> A.E.I. UCM - BBI "+ polo +"</td><td class='right_options'>"+ user_menu +"&nbsp;&nbsp;</td></tr></table>"
});