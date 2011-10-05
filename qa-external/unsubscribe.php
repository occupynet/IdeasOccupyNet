<?php

require_once("../qa-include/qa-base.php");
require_once (QA_INCLUDE_DIR."qa-app-users.php");

if(qa_get_logged_in_userid() == null)
  $msg = "Tienes que iniciar sesión para dar de baja el usuario en la newsletter";

$confirm = md5(qa_get_logged_in_email());
if((qa_get_logged_in_userid() != null)&&(! isset($_GET['c']))){
 $msg = "El sistema le ha mandado un email para confirmar la baja en la newsletter";
 $subject = "Baja en la newsletter de Propongo Tomar la Plaza";
 $body = qa_get_logged_in_handle().",<br /><br />\n";
 $body = $body."Nos ha llegado una solicitud por parte suya para darse de baja en la newsletter de Propongo. Si realmente usted solicitó la baja puede confirmarla pinchando en el siguiente enlace <a href=\"http://".$_SERVER['SERVER_NAME']."/qa-external/unsubscribe.php?c=".$confirm."\">http://".$_SERVER['SERVER_NAME']."/qa-external/unsubscribe.php?c=".$confirm."</a> ,de lo contrario ignore este mensaje.<br /><br />\n";
 $body = $body."Propongo Tomar la Plaza\nhttp://propongo.tomalaplaza.net/<br />\n";
 $sent = mail(qa_get_logged_in_email(),$subject,$body,'From: noreply.propongo@tomalaplaza.net' . "\r\n"."Content-type: text/html\r\n");
}

if((qa_get_logged_in_userid() != null)&&($_GET['c'] == $confirm)){
  qa_db_query_raw("UPDATE qa_mailing SET subscriber=0 WHERE userid=".qa_get_logged_in_userid());
  $msg = "Su usuario ha sido dado de baja de la newsletter";
}

if((qa_get_logged_in_userid() != null)&&(isset($_GET['c']))&&($_GET['c'] != $confirm))
 $msg = "Su periodo para solicitar la baja ha caducado, vuelva a solicitarla de nuevo";


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
 <meta http-equiv="Content-type" Content="text/html; charset=utf-8">
 <title>Propongo Tomar la Plaza - Newsletter</Title>
 <link rel="stylesheet" TYPE="text/css" HREF="../qa-theme/Quixote/qa-styles.css?1.4">
 <link rel="shortcut icon" href="http://propongo.tomalaplaza.net/favicon.ico" />
</head>
<body>
<DIV CLASS="qa-template-custom">
                <DIV CLASS="qa-body-wrapper">

                        <DIV CLASS="qa-header">
                                <DIV CLASS="qa-logo">
                                        <A HREF="./" CLASS="qa-logo-link" TITLE="Propongo Tomar la Plaza"><IMG SRC="http://propongo.tomalaplaza.net/qa-theme/Quixote/imgs/logo.png" WIDTH="247" HEIGHT="62" BORDER="0"></A>
                                </DIV>

                                <DIV CLASS="qa-search">
                                        <FORM METHOD="GET" ACTION="./search">

                                                <INPUT NAME="q" VALUE="" CLASS="qa-search-field">
                                                <INPUT TYPE="submit" VALUE="Buscar" CLASS="qa-search-button">
                                        </FORM>
                                </DIV>
                                <DIV CLASS="qa-nav-user">

<?php if(qa_get_logged_in_userid() != null){ ?>
                                        <DIV CLASS="qa-logged-in">
                                                <SPAN CLASS="qa-logged-in-pad">Hola </SPAN><SPAN CLASS="qa-logged-in-data"><A HREF="../user/<?php echo qa_get_logged_in_handle();?>" CLASS="qa-user-link"><?php echo qa_get_logged_in_handle();?></A></SPAN>

                                        </DIV>
                                        <UL CLASS="qa-nav-user-list">
                                                <LI CLASS="qa-nav-user-item qa-nav-user-account">
                                                        <A HREF="../account" CLASS="qa-nav-user-link">Mi cuenta</A>
                                                </LI>
                                                <LI CLASS="qa-nav-user-item qa-nav-user-logout">
                                                        <A HREF="../logout" CLASS="qa-nav-user-link">Salir</A>
                                                </LI>

                                        </UL>
                                        <DIV CLASS="qa-nav-user-clear">
                                        </DIV>
<?php }else{ ?>
                                     <UL CLASS="qa-nav-user-list">
						<LI CLASS="qa-nav-user-item qa-nav-user-Facebook Login">

							
						</LI>
						<LI CLASS="qa-nav-user-item qa-nav-user-login">
							<A HREF="../login?to=qa-external/unsubscribe.php" CLASS="qa-nav-user-link">Ingresar</A>
						</LI>
						<LI CLASS="qa-nav-user-item qa-nav-user-register">
							<A HREF="../register?to=" CLASS="qa-nav-user-link">Registro</A>
						</LI>
					</UL>

					<DIV CLASS="qa-nav-user-clear">
					</DIV>
<?php } ?>
                                </DIV>
                                <DIV CLASS="qa-nav-main">
                                       <UL CLASS="qa-nav-main-list">
                                                <LI CLASS="qa-nav-main-item qa-nav-main-Intenciones">
                                                        <A HREF="../Intenciones" CLASS="qa-nav-main-link">Intenciones</A>

                                                </LI>
                                                <LI CLASS="qa-nav-main-item qa-nav-main-árbol de categorías">
                                                        <A HREF="../%C3%A1rbol+de+categor%C3%ADas" CLASS="qa-nav-main-link">Árbol de categorías</A>
                                                </LI>
                                                <LI CLASS="qa-nav-main-item qa-nav-main-questions">
                                                        <A HREF="../questions" CLASS="qa-nav-main-link">Propuestas</A>
                                                </LI>
                                                <LI CLASS="qa-nav-main-item qa-nav-main-tag">

                                                        <A HREF="../tags" CLASS="qa-nav-main-link">Etiquetas</A>
                                                </LI>
<?php if(qa_get_logged_in_userid() != null){ ?>
                                                <LI CLASS="qa-nav-main-item qa-nav-main-ask">
                                                        <A HREF="../ask" CLASS="qa-nav-main-link">Hacer una propuesta</A>
                                                </LI>
<?php } ?>
                                        </UL>
                                        <DIV CLASS="qa-nav-main-clear">
                                        </DIV>
                                </DIV>
                                <DIV CLASS="qa-header-clear">
                                </DIV>
                        </DIV> <!-- END qa-header  -->
<DIV style="min-height: 500px;" CLASS="qa-main">
<br />
<H1>Darse de baja de la newsletter</H1>
<br />
<div style="width: 400px; padding: 20px; border-radius: 8px; -webkit-border-radius: 8px; -moz-border-radius: 8px; background: #D85C30; overflow: hidden; font-size: 14px; line-height: 18px; margin-top:16px; color: #fff;">
<p style="width: 400px;" ><?php echo $msg ?></p>
</div>
</DIV> <!-- END qa-main -->
<DIV CLASS="qa-footer">
                                <DIV CLASS="qa-attribution">
                                        Powered by <A HREF="http://www.question2answer.org/">Question2Answer</A>
                                </DIV>
                                <DIV CLASS="qa-footer-clear">

                                </DIV>
                        </DIV> <!-- END qa-footer -->

                </DIV> <!-- END body-wrapper -->
                <div class="footer">

<div class="contenedor">

<div class="colA">
<h2>El debate y la reflexión se extiende a la red</h2>
<p>Lanzamos "Propongo" como herramienta donde cualquier usuario de internet puede depositar propuestas, que a su vez pueden ser debatidas y generar constructivamente nuevas propuestas. Porque el debate también se debe expandir a nuestras vidas digitales. </p>

<p>Con esta herramienta tomamos también la plazas de Internet.</p>


<div class="copyright">
2011 Licenciado bajo <a href="http://creativecommons.org/licenses/by-sa/3.0/es/" target="_blank">esta licencia Creative Commons</a>. Basado en <a href="http://www.question2answer.org/" target="_blank"; >Questions2Answers</a>. <a style="float: right;" href="http://propongo.tomalaplaza.net/aviso-legal" >Aviso legal</a><a style="float: right; margin-right:10px;" href="mailto:propongo.contacto@gmail.com" >Contacto</a>
</div>

</div>

<div class="colB">
<h2>Participa</h2>
<p style="line-height:20px;">Es hora de que se escuche tu voz.</br>
Propón, comenta, vota, debate... con los usuarios que ya están participando.</p>
<h2 style="width: 150px; float: left;">Síguenos también en:</h2>

<ul class="social">
<li><a href="http://twitter.com/takethesquare/" target="_blank"><img src="http://propongo.tomalaplaza.net/qa-theme/Quixote/imgs/icon-twitter.png" width="24" height="24" alt="Visita nuestro Twitter" title="Visita nuestro Twitter" /></a></li>

<li><a href="http://www.facebook.com/Take.the.Square" target="_blank"><img src="http://propongo.tomalaplaza.net/qa-theme/Quixote/imgs/icon-facebook.png" width="24" height="24" alt="Hazte fan en Facebook" title="Hazte fan en Facebook" /></a></li>
</ul>
</div>

</div>

</div>
</body>
</html>

