<?php

require_once("../qa-include/qa-base.php");
require_once (QA_INCLUDE_DIR."qa-app-users.php");

//Checking if user have permission. Super Admin Level
if(qa_get_logged_in_level() != 120)
 die("You don't have permission. Get out here!");

// Checking action for giving a message and it shows the right div
$dmsg_display = "none";
$demail_display = "none";
if($_GET['action'] == "sync"){
 $msg = "Los usuarios han sido sincronizados para enviar la newsletter";
 $dmsg_display = "";
 $dsync_display = "none";
 $demail_display = "";
}else
 if($_GET['action'] == "mailed"){
  $msg = "La newsletter ha sido enviada";
  $dmsg_display = "";
 }
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
						<LI CLASS="qa-nav-main-item qa-nav-main-ask">
							<A HREF="../ask" CLASS="qa-nav-main-link">Hacer una propuesta</A>
						</LI>
						<LI CLASS="qa-nav-main-item qa-nav-main-admin">
							<A HREF="../admin" CLASS="qa-nav-main-link">Admin</A>

						</LI>
					</UL>
					<DIV CLASS="qa-nav-main-clear">
					</DIV>
				</DIV>
				<DIV CLASS="qa-header-clear">
				</DIV>
			</DIV> <!-- END qa-header  -->
<DIV CLASS="qa-main">
<H1>Enviar newsletter</H1>
<div id="dmsg" name="dmsg" style="display: <?php echo $dmsg_display ?>; width: 400px; padding: 20px; border-radius: 8px; -webkit-border-radius: 8px; -moz-border-radius: 8px; background: #D85C30; overflow: hidden; font-size: 14px; line-height: 18px; margin-top:13px; margin-bottom: 13px; color: #fff;">
<p style="width: 400px;" ><?php echo $msg ?></p>
</div>
<div id="dsync" name="dsync" style="display: <?php echo $dsync_display ?>;">
<form action="http://<?php echo $_SERVER['SERVER_NAME'] ?>/qa-external/synchronize.php" method="POST">
 <p style="color: #fff;">Sincronizar usuarios de propongo y subscriptores de newsletter</p>
 <input type="submit" id="synchronize" name="synchronize" value="Sincronizar" />
</form>
</div>
<br />
<div id="demail" name="demail" style="display: <?php echo $demail_display ?>;">
<form action="http://<?php echo $_SERVER['SERVER_NAME'] ?>/qa-external/sendEmail.php" method="POST">
<p style="color: #fff; margin-bottom: 5px;">Asunto</p><input type="text" id="subject" name="subject" size="50" />
<br /><br />
<textarea id="notification" name="notification" rows="25" cols="80"></textarea>
<br /><br />
<input type="submit" id="notify" name="notify" value="Enviar newsletter" />
</form>
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
