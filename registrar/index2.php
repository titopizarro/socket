<?php 
	//INICIAMOS LA SESION
	session_start();

	//boton salir desconatamosy borramos la sesion
	if (!empty($_GET['salir'])) {
		//limpiar las varibles de sesion t destruirla
		$_SESSION['id_usuario'] = "";
		session_unset();
		session_destroy();
	}

	//
	if (empty($_SESSION['id_usuario'])) {
		header("Location: /socket/");
	}else{
		require_once("../clases/consultas.php");
		date_default_timezone_set("America/Guayaquil");
		$id_usuario = $_SESSION['id_usuario'];

		$espaciosVacios = consultarGeneral("espacio","estado_espacio","=","LIBRE");
		$nespacio = $_GET['nespacio'];
		// $libresA = $_GET['libresA'];
		// $ocupadosA = $_GET['ocupadosA'];
		// $contas1=$_GET['contas1'];
		// $contas2=$_GET['contas2'];
		// $contap1=$_GET['contap1'];
		// $contap2=$_GET['contap2'];
		// $contap3=$_GET['contap3'];
		// $libresB = $_GET['libresB'];
		// $ocupadosB = $_GET['ocupadosB'];
		// $contbp1=$_GET['contbp1'];
		// $contbp2=$_GET['contbp2'];
		// $contbp3=$_GET['contbp3'];
		// $contbp4=$_GET['contbp4'];
		// $libresE = $_GET['libresE'];
		// $ocupadosE = $_GET['ocupadosE'];
		$id_piso="";
		$id_edificio = "";
		$nombre_piso = "";
		$tipo_piso = "";
		$nombre_edificio = "";
		
		$nusuario=$id_usuario;
		
		$fechaRegistro = date("Y-m-d H:i:s");
		$espacios  = consultarGeneral("espacio","nombre_espacio","=",$_GET['nespacio']);
		while ($espacio=mysql_fetch_array($espacios)) {
			$id_piso=$espacio["id_piso"];
		}
		$pisos = consultarGeneral("piso","id_piso","=",$id_piso);
		while ($piso = mysql_fetch_array($pisos)) {
			$id_edificio= $piso["id_edificio"];
			$nombre_piso = $piso["nombre_piso"];
			$tipo_piso = $piso["tipo_piso"];
		}
		$edificios = consultarGeneral("edificio","id_edificio","=",$id_edificio);
		while ($edificio = mysql_fetch_array($edificios)) {
			$nombre_edificio = $edificio["nombre_edificio"];
		}
		conexion();
		$nusuario="";
		$usuarios = mysql_query("SELECT * FROM usuario WHERE id_usuario = $id_usuario");
		while ($arr = mysql_fetch_array($usuarios)) {
			$nusuario = $arr["nombre_usuario"];
		}
		salir();
	}
 ?>
 
 	
<h3>Registrar el espacio de parqueo</h3>
<div class="caja">
	<input class="cajatexto" id="usuarioSistema" type="text" placeholder="Usuario..." value="<?php echo "Usuario: ".$nusuario; ?>"/>
	<input class="cajatexto" id="fechaRegistro" type="text" placeholder="Usuario..." value="<?php echo "Fecha: ".$fechaRegistro; ?>"/>
	<input class="cajatexto" id="edificioEspacio" type="text" placeholder="Edificio espacio..." value="<?php echo "Edificio: ".$nombre_edificio; ?>"/>
	<input class="cajatexto" id="pisoEspacio" type="text" placeholder="Piso espacio..." value="<?php echo "Piso: ".$nombre_piso. " / ".$tipo_piso; ?>"/>
	<input class="cajatexto" id="espacioSeleccionado" type="text" placeholder="Espacio parqueo..." value="<?php echo "Espacio: ".$nespacio; ?>"/>
	<input class="cajatexto" id="placaVehiculo" type="text" placeholder="Placa vehiculo..." required/>
	<input class="boton" type="submit" value="Registrar" onclick="registrar();"/>	
	<input class="boton" type="submit" value="Cancelar" onclick="cancelarRegistrar();"/>				
</div>