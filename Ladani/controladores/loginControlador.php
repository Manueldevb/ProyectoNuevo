<?php 
	
	if ($peticionAjax) {
		require_once "../modelos/loginModelo.php";
	}else{
		require_once "./modelos/loginModelo.php";
	}

	class loginControlador extends loginModelo{

		/*----- Controlador para iniciar sesion ------*/
		public function iniciar_sesion_controlador(){
			$usuario=mainModel::limpiar_cadena($_POST['usuario_log']);
			$clave=mainModel::limpiar_cadena($_POST['clave_log']);


			/* ------ comprobar campos vacios -----*/
			if ($usuario=="" || $clave==""){
				echo '<script> 
			Swal.fire({
				title: "Ocurrio un error inesperado",
				text: "No has llenado todos los campos requeridos",
				type: "error",		
				confirmButtonText: "Aceptar"	
			});

				</script>';
				exit ();
			
			}

			/*------- Verificar integridad de los datos ------*/
			if (mainModel::verificar_datos("[a-zA-Z0-9]{1,35}",$usuario)) {
				echo '<script> 
				Swal.fire({
					title: "Ocurrio un error inesperado",
					text: "El nombre de usuario no conincide con el formato solicitado",
					type: "error",		
					confirmButtonText: "Aceptar"	
				});

				</script>';
				exit ();
			
			}
			/*------- Verificar integridad de los datos ------*/
			if (mainModel::verificar_datos("[a-zA-Z0-9$@.-]{7,100}",$clave)) {
				echo '
				<script> 
					Swal.fire({
						title: "Ocurrio un error inesperado",
						text: "La clave no conincide con el formato solicitado",
						type: "error",		
						confirmButtonText: "Aceptar"	
					});
				</script>';
				exit ();
			}
			$clave=mainModel::encryption($clave);

			$datos_login=[
				"Usuario"=>$usuario,
				"Clave"=>$clave
			];

			$datos_cuenta=loginModelo::iniciar_sesion_modelo($datos_login);

			if($datos_cuenta->rowCount()==1){
				$row=$datos_cuenta->fetch();

				session_start(['name'=>'LADANI']);

				$_SESSION['id_ladani']=$row['usuario_id'];
				$_SESSION['nombre_ladani']=$row['usuario_nombre'];
				$_SESSION['apellido_ladani']=$row['usuario_apellido'];
				$_SESSION['usuario_ladani']=$row['usuario_usuario'];
				$_SESSION['privilegio_ladani']=$row['usuario_privilegio'];
				$_SESSION['token_ladani']=md5(uniqid(mt_rand(),true));

				return header("Location: ".SERVERURL."home/");
			}else{
				echo '
				<script> 
					Swal.fire({
						title: "Ocurrio un error inesperado",
						text: "El usuario o clave son incorrectos",
						type: "error",		
						confirmButtonText: "Aceptar"	
					});

				</script>';

			}

		}/* fin controlador */

		/*----- Controlador para forzar el cierre de  sesion ------*/

		public function forzar_cierre_sesion_controlador(){
			session_unset();
			session_destroy();

			if(headers_sent()){
				return "<script>window.location.href='".SERVERURL."login/';</script>";
			}else{
				return header ("Location: ".SERVERURL."login/");
			}
		}/* fin del controlador */

		/*------------------- Controlador cierre de sesion ---------------*/
		public function cerrar_sesion_controlador(){
			session_start(['name' => 'ladani']);
			$token=mainModel::decryption($_POST['token']);
			$usuario=mainModel::decryption($_POST['usuario']);

			if($token==$_SESSION['token_ladani'] && $usuario==$_SESSION['usuario_ladani']){
				session_unset();
				session_destroy();
				$alerta=[
					"Alerta"=>"redireccionar",
					"URL"=>SERVERURL."login/"
				]
			}else{
				$alerta=[
					"Alerta"=>"simple",
					"Titulo"=>"Ocurrio un error inesperado",
					"Texto"=>"No se pudo cerrar la sesion",
					"Tipo"=>"error"
				];
			}
			echo json_encode($alerta);
		}/* fin del controlador */
	}
