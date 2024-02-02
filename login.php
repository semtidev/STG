<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8" />
<title>Garant&iacute;a</title>
<link rel="shortcut icon" href="resources/images/icons/semtiGarantia_16x16.png" />
        <!--[if lt IE 9]>
          <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->
<link href="resources/css/zice.style.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="resources/css/tipsy.css" media="all"/>
<style type="text/css">
#versionBar {
	background-color:#212121;
	position:fixed;
	width:100%;
	height:35px;
	bottom:0;
	left:0;
	text-align:center;
	line-height:35px;
}
body {
	background-color: #FFF;
}
</style> 

<script type="text/javascript" src="js/jquery.min.js"></script>
<script type="text/javascript" src="js/jquery-jrumble.js"></script>
<script type="text/javascript" src="js/jquery.tipsy.js"></script>
<script type="text/javascript" src="js/iphone.check.js"></script>

</head>
<body>
         
<div id="alertMessage" class="error"></div>
<div id="successLogin"></div>
<div class="text_success"><!--<img src="resources/images/loadder/loader_green.gif"  alt="ziceAdmin" /><span>Por favor espere</span>--></div>

<div id="login" >
  <!--<div class="ribbon"></div>-->
  <div class="inner">
  <div class="logo" ><img src="resources/images/logo/logo.png" alt="SEMTI" /></div>
  <div class="userbox"></div>
  <div class="formLogin">
   <form name="formLogin" id="formLogin" action="#">
      <input type="hidden" name="action" value="login"/>
      <div class="login" style="margin-bottom:10px; margin-top: 10px;">&nbsp;</div>
      <div class="tip">
      <input name="username" type="text"  id="username_id" autocomplete="off"  title="Usuario" onKeyDown="javascript: if(event.keyCode == 13) document.formLogin.password.focus();" />
      </div>
      <div class="tip">
      <input name="password" type="password" id="password_id"   title="Contrase&ntilde;a" onKeyDown="javascript: if(event.keyCode == 13) SendLogin();" />
      </div>
      <div style="padding:10px 0px 0px 0px ;">
         <div style="float:right;padding:2px 0px ;">
           
            <ul class="uibutton-group">
              <!--<input type="button" value="Entrar" class="uibutton normal" id="but_login" style="color:#666;" />
               <input type="button" value="Olvid&eacute; mi Contrase&ntilde;a" onclick="mailto:semti@nauta.com" class="uibutton normal" id="forgetpass" style="color:#666" />-->
              <li><a class="uibutton normal" id="but_login" onClick="SendLogin();" />Entrar</a></li>
              <li><a class="uibutton  normal" href="mailto:semti@nauta.com" title="Notificar al Administrador del Sistema" id="forgetpass" />Olvid&eacute; mi Contrase&ntilde;a</a></li>
            </ul>
        </div>
      </div>
    </form>
  </div>
  
</div>
  <div class="clear"></div>
  <div class="shadow"></div>
  <div id="polo">
    <center>
      <img src="resources/images/logo/login-aei.png" class="login-aei-logos"><br><strong>LA HABANA, CUBA</strong>
    </center>
  </div>
</div>

<!--Login div-->
<div class="clear"></div>
<div id="versionBar" >
  &copy; Copyright <?php echo date('Y'); ?>.  AEI UCM - BBI La Habana, Cuba </span>
  <!-- // copyright-->
</div>

<!-- Link JScript-->
<script type="text/javascript" src="js/login.js"></script>

</body>
</html>