<?php
// Inicializar la sesion activa
session_name('semtiGarantiaSession');
session_start();
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Garant&iacute;a</title>
<link rel="shortcut icon" href="resources/images/icons/semtiGarantia_16x16.png" />

<script type="text/javascript" src="js/extjs42/includes/shared/include-ext.js"></script>
<script type="text/javascript" src="js/extjs42/includes/shared/messages.js"></script>
<script type="text/javascript" src="js/extjs42/locale/ext-lang-es.js"></script>

<link rel="stylesheet" type="text/css" href="js/extjs42/includes/shared/messages.css" />
<link href="resources/css/app.css" type="text/css" rel="stylesheet">
<link href="resources/css/fileproyects.css" type="text/css" rel="stylesheet">
<link href="resources/css/bootstrap.min.css" type="text/css" rel="stylesheet">
<link href="resources/css/jquery-ui-1.10.3.custom.css" type="text/css" rel="stylesheet">

<script type="text/javascript" src="app.js"></script>
<script type="text/javascript" src="js/functions.js"></script>

<script src="js/pace.min.js"></script> 
<link href="resources/css/pace-theme-flash.css" rel="stylesheet" />
<script type="text/javascript">
    function r(f){/in/.test(document.readyState)?setTimeout('r('+f+')',9):f()}
    r(function(){
        Pace.stop();
        Pace.options = {
          ajax: false, 
          document: false, 
          eventLag: false, 
          elements: {
            selectors: ['body'],
          },
           restartOnRequestAfter: false,
           restartOnPushState: false
        };
        Ext.get('loading').remove();
        Ext.fly('loading-mask').animate({
            opacity:0,
            remove:true
        });
    });
</script>

<!-- Font Awesome Icons v5 -->
<link href="resources/fa-563/css/all.css" rel="stylesheet">

<link href="resources/css/semti.aei.css" type="text/css" rel="stylesheet">

</head>
<body>
    <div id="loading-mask" style=""></div>
    <div id="loading">
        <div class="loading-indicator">
            <div id="loading-logo"></div>
            <img src="resources/images/icons/loading.gif"/>
            <div id="loading-msg">Cargando Sistema...</div>
        </div>
    </div>
    <script type="text/javascript" src="js/init.js"></script>
    <script type="text/javascript" src="js/jquery-1.9.1.min.js"></script>
    <script type="text/javascript" src="js/bootstrap.js"></script>

    <script>
        localStorage.setItem('usuario', '<?php echo $_SESSION['usuario']; ?>');
        localStorage.setItem('nombre', '<?php echo $_SESSION['nombre']; ?>');
        localStorage.setItem('apellidos', '<?php echo $_SESSION['apellidos']; ?>');
        localStorage.setItem('cargo', '<?php echo $_SESSION['cargo']; ?>');
        localStorage.setItem('email', '<?php echo $_SESSION['email']; ?>');
        localStorage.setItem('polo_id', '<?php echo $_SESSION['polo']; ?>');
        localStorage.setItem('polo_name', '<?php echo $_SESSION['polo_name']; ?>');
        localStorage.setItem('ipserver', '<?php echo $_SESSION['ipserver']; ?>');
        localStorage.setItem('perfiles', '<?php echo $_SESSION['perfiles']; ?>');
    </script>
</body>
</html>