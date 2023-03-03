<?php 

	$peticionAjax=true;
	require_once "../config/APP.php";

	if (isset($_POST['usuario_dni_reg'])) {
		

		}else{
		session_start(['name' => 'ladani']);
		session_unset();
		session_destroy();
		header("Location: ".SERVERURL."login/");
		exit();
	}

 ?>