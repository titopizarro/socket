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
		$nespacio = "";
		$placa = "";
		$espaciosOcupados = consultarGeneral("espacio","estado_espacio","=","OCUPADO");
		$fechaSalida = date("Y-m-d H:i:s");

		$libresA=0;$ocupadosA=0;$reservadosA=0;
		$libresB=0;$ocupadosB=0;$reservadosB=0;
		$libresE=0;$ocupadosE=0;$reservadosE=0;
		$espaciosVacios = consultarGeneral("espacio","estado_espacio","=","LIBRE");
		conexion();
		$nusuario="";
		$usuarios = mysql_query("SELECT * FROM usuario WHERE id_usuario = $id_usuario");
		while ($arr = mysql_fetch_array($usuarios)) {
			$nusuario = $arr["nombre_usuario"];
		}
		$espaciosTorreA = mysql_query("SELECT * FROM espacio WHERE id_piso IN(1,2,3,4,5)");
		while ($espacios = mysql_fetch_array($espaciosTorreA)) {
			$est = $espacios["estado_espacio"];
			if ($est=="LIBRE") {
				$libresA = $libresA + 1;
			}elseif ($est=="OCUPADO") {
				$ocupadosA = $ocupadosA + 1;
			}else{
				$reservadosA = $reservadosA + 1;
			}
		}
		$espaciosTorreB = mysql_query("SELECT * FROM espacio WHERE id_piso IN(6,7,8,9)");
		while ($espacios = mysql_fetch_array($espaciosTorreB)) {
			$est = $espacios["estado_espacio"];
			if ($est=="LIBRE") {
				$libresB = $libresB + 1;
			}elseif ($est=="OCUPADO") {
				$ocupadosB = $ocupadosB + 1;
			}else{
				$reservadosB = $reservadosB + 1;
			}
		}
		$espaciosExteriores = mysql_query("SELECT * FROM espacio WHERE id_piso IN(10)");
		while ($espacios = mysql_fetch_array($espaciosExteriores)) {
			$est = $espacios["estado_espacio"];
			if ($est=="LIBRE") {
				$libresE = $libresE + 1;
			}elseif ($est=="OCUPADO") {
				$ocupadosE = $ocupadosE + 1;
			}else{
				$reservadosE = $reservadosE + 1;
			}
		}
		
		$id_piso="";
		$id_edificio = "";
		$nombre_piso = "";
		$tipo_piso = "";
		$nombre_edificio = "";
		$espacioMAX = consultarMAX("espacio","id_espacio","nombre_espacio","=",$_GET["nespacio"]);
		while ($espacio = mysql_fetch_array($espacioMAX)) {
			$id_espacio = $espacio["id_espacio"];
		}
		$ticketMAX = consultarMAX("ticket","id_ticket","id_espacio","=",$id_espacio);
		while ($ticket = mysql_fetch_array($ticketMAX)) {
			$id_ticket = $ticket["id_ticket"];
		}
		$ticketGNRL = consultarGeneral("ticket","id_ticket","=",$id_ticket);
		while ($ticket = mysql_fetch_array($ticketGNRL)) {
			$placa_ticket = $ticket["placa_ticket"];
		}
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
	}
 ?>
 <html>
 <head>
 	<meta charset="utf-8" />
	<title>OROMALL</title>
	<!-- <link rel="stylesheet" href="css/style.css"> -->
	<script src="../js/jquery-1.7.2.min.js"></script>
	<script src="../js/fancywebsocket.js"></script>
	<style type="text/css">
		@import url(http://fonts.googleapis.com/css?family=Source+Sans+Pro:200,300);
		*{
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}
		.barra{
			/*display: inline-block;
			vertical-align: top;*/
		}
		
		.barralateral-principal{
		/*position: absolute;*/
		/*top: 0;
		left: 0;*/
		padding-top: 15;
		min-height: 100%;
		width: 19%;
		z-index: -1;
		}
		.barralateral{
		background-color: #3E474F;
		color: #fff;
		padding-bottom: 10px;

		}
		.barralateral-menu{
		font-family: 'Source Sans Pro', sans-serif;
		font-size: 18px;
		list-style: none;
	  	margin: 5px;
	  	padding-top: 60px;
	  	/*padding-left: 45px;*/
		}
		.barralateral-menu h3 {
		margin: 0 auto;
		padding: 0 0 18px;
		}
		.barralateral-menu li{
		position: relative;
	  	margin: 5px;
	  	padding: 0;
	  	list-style: none;
	  	/*box-shadow: 0 1px 1px rgba(0, 0, 0, 0.2);*/
		}
		.barralateral-menu a {
		color: #fff;
		margin-left: 10px;
		text-decoration: none;
		}
		.boton{
		background-color: #00AB6B;
		border: 1px solid #fff;
		border-radius: 5px;
		color: #fff;
		display: block;
		margin: 10px auto;
		padding: 10px 5px;
		text-align: center;
		/*vertical-align: top;*/
		width: 160px;
		}
		.cabecera{
		background-color: #FF656D;
		color:#fff;
		/*display: block;*/
		/*float: left;*/
		font-family: 'Source Sans Pro', sans-serif;
		font-size: 20px;
		font-weight: 300;
		height: 65px;
		/*line-height: 50px;*/
		/*overflow: hidden;*/
		/*padding: 0 15px;*/
		position: fixed;
		text-align: left;
		width: 100%;
		z-index: 1;
		}
		.caja{
		background: #ffffff;
		border-radius: 3px;
		border-top: 5px solid #FF656D;
		box-shadow: 0 1px 1px rgba(0, 0, 0, 0.2);
		margin: 10px auto;
		margin-bottom: 20px;
		padding: 15px 15px;
 		/*position: relative;*/
		width: 50%;
		}
		.caja-menu{
		border-radius: 3px;
		border-top: 5px solid #FFF;
		/*box-shadow: 0 1px 1px rgba(0, 0, 0, 0.2);*/
		margin: 0 auto;
		/*margin-bottom: 20px;*/
		padding: 10px 5px;
		}
		.cajatexto{
		border: 1px solid #B8B8B8;
		border-radius: 5px;
		color: #B8B8B8;
		display: block;
		margin: 10px auto;
		padding: 10px 5px;
		vertical-align: top;
		width: 85%;
		}
		.celeste{
		background-color: #39cccc;
		}
		.contenedor, .barralateral-principal{
			display: inline-block;
			vertical-align: top;
		}
		.contenedor{
  		background-color: #ecf0f5;
  		font-family: 'Source Sans Pro', sans-serif;
		/*margin-left: 225px;*/
		min-height: 100%;
		/*padding-top: 50px;*/
		width: 80%;
		/*z-index: 820;*/
		}
		.contenido{
		min-height: 250px;
		padding: 5px;
		margin-left: auto;
		margin-right: auto;
		padding-left: 15px;
		padding-right: 15px;
		padding-top: 79px;
		width: 100%
		}
		.contenido h2{
		text-align: center;
		}
		.espacios:hover {
		background-color: #ff5000;
		}
		.espacios{
		display: inline-block;
		vertical-align: top;
		width: 50px;
		text-align: center;
		background-color: #39cccc;
		border: 1px solid #fff;
		color: #fff;
		cursor: pointer;
		padding: 10px 5px;
		}
		.naranja{
		background-color: #ff5000;
		}
		.tablero{
			padding-top: 15px;
			margin-bottom: 30px;
		}
		.logo{
			display: inline-block;
			width: 19%;
		}
		.logo a{
			color: #fff;
			font-size: 24px;
			float: left;
			margin-left: 45px;
			margin-top: 10px;
		}
		.logo a:hover{
			color: #000;
			font-size: 30px;
			margin-left: 40px;
		}
		
		.valores{
			display: inline-block;
			font-size: 16px;
			margin-left: 25px;
			/*margin-top: 10px;*/
			width: 30%;
		}
		.valores div{
			display: block;
		}
		.valores p{
			display: inline-block;
		}
		.registrar{
			display: inline-block;
			width: 50%;
		}
		.registrar form{
			float: right;
			margin-right: 20px;
			margin-top: 14px;
		}
    </style>
    <script language="javascript">
		function liberar()
		{	

			var nespacio = document.getElementById('espacioOculto').value;
			var placa    = document.getElementById('placaOculta').value;
			var cplaca   = document.getElementById('confirmarPlaca').value;
			var nusuario = "<?php echo $nusuario; ?>";
			var libresA = "<?php echo $libresA ?>";
			var ocupadosA = "<?php echo $ocupadosA ?>";
			var libresB = "<?php echo $libresB ?>";
			var ocupadosB = "<?php echo $ocupadosB ?>";
			var libresE = "<?php echo $libresE ?>";
			var ocupadosE = "<?php echo $ocupadosE ?>";
			if (placa == cplaca) {
				$.ajax({
				async: false,
				type: "POST",
				url: "colocar.php",
				data: "nespacio="+nespacio+"&placa="+placa+"&nusuario="+nusuario+"&libresA="+libresA+"&ocupadosA="+ocupadosA+"&libresB="+libresB+"&ocupadosB="+ocupadosB+"&libresE="+libresE+"&ocupadosE="+ocupadosE,
				dataType:"html",
				success: function(data) 
				{
					console.log(data);
				 	send(data);// array JSON
				 	window.location="../tablero/";
					// document.getElementById("espacioSeleccionado").value = "";
					// document.getElementById("placaVehiculo").value = "";
					// document.getElementById('confirmarPlaca').value = "";
				}
				});
			} else{
				console.log("Las placas no coinciden. "+ placa + " <> " + cplaca);
				document.getElementById('confirmarPlaca').value = "";
			};
			
		}
	</script>
	<script type="text/javascript">
      $(document).on("ready",function(){
        $('th').click(function(){
            
            removerClase('th', 'celeste');
            removerClase('th', 'naranja');
            var clase=$(this).attr('class');
            var valor=$(this).attr('valor');
            
            if (clase=='espacios naranja') {
              //cambio de color espacio
              $(this).addClass('celeste').removeClass('naranja');
              
            }else{
              //cambio de color espacio
              $(this).addClass('naranja').removeClass('celeste');
            };
            document.getElementById("espacioSeleccionado").value = "Espacio: "+valor;
            document.getElementById("espacioOculto").value = valor;
            $.ajax({
              type:"GET",
              url:"consultarPlaca.php",
              data:{nespacio:valor}
            }).done(function(msg){
            	console.log(msg);
            	var JSONdata    = JSON.parse(msg); //parseo la informacion
					var placa = JSONdata[0].placa;
					var nombre_piso = JSONdata[0].nombre_piso;
					var tipo_piso = JSONdata[0].tipo_piso;
					var nombre_edificio = JSONdata[0].nombre_edificio;
				<?php 
					$placa= "<script> document.write(placa) </script>"; 
					$nespacio = "<script> document.write(valor) </script>";
				?>
              	document.getElementById("placaVehiculo").value = "Placa: "+placa;
              	document.getElementById("placaOculta").value = placa;
              	document.getElementById("pisoEspacio").value = "Piso: "+nombre_piso+" / "+tipo_piso;
	            document.getElementById("edificioEspacio").value = "Edificio: "+nombre_edificio;
            });

            console.log(clase+" "+valor);
        });
        
        function removerClase(tag, clase){
          $(tag).removeClass(clase);
        }
      });
    </script>
 </head>
 <body>
 	<header class="cabecera">
 		<div class="barra">
 			<div class="logo">
 				<a  href="../tablero/">
 					<span>Web<b>PARKING</b></span>
 				</a>
 			</div>
 			
 			<div class="valores">
 				<div><p><b>Torre A:</b> Libres = </p><p id="libresA" value="<?php echo $libresA; ?>"><?php echo " ".$libresA; ?></p> <p> / Ocupados = </p><p id="ocupadosA"><?php echo $ocupadosA; ?></p> </div>
 				<div><p><b>Torre B:</b> Libres = </p><p id="libresB" value="<?php echo $libresB; ?>"><?php echo " ".$libresB; ?></p> <p> / Ocupados = </p><p id="ocupadosB"><?php echo $ocupadosB; ?></p> </div>
 				<div><p><b>Exterior:</b> Libres = </p><p id="libresE" value="<?php echo $libresE; ?>"><?php echo " ".$libresE; ?></p> <p> / Ocupados = </p><p id="ocupadosE"><?php echo $ocupadosE; ?></p> </div>
 				<!-- <div>Torre A  [Libres: <?php echo $libresA; ?><p>Torre A</p> Ocupados: <?php echo $ocupadosA; ?> Reservados: <?php echo $reservadosA ?>]</div>
				<div>Torre B  [Libres: <?php echo $libresB; ?> Ocupados: <?php echo $ocupadosB; ?> Reservados: <?php echo $reservadosB ?>]</div>
				<div>Exterior  [Libres: <?php echo $libresE; ?> Ocupados: <?php echo $ocupadosE; ?> Reservados: <?php echo $reservadosE ?>]</div>-->
 			</div>
			
 			<!-- <div class="registrar">
 				<form action="../registrar/" method=GET role="form">
	 				<input name="libresA" type="hidden" value="<?php echo $libresA; ?>"/>
	 				<input name="ocupadosA" type="hidden" value="<?php echo $ocupadosA; ?>"/>
	 				<input name="libresB" type="hidden" value="<?php echo $libresB; ?>"/>
	 				<input name="ocupadosB" type="hidden" value="<?php echo $ocupadosB; ?>"/>
	 				<input name="libresE" type="hidden" value="<?php echo $libresE; ?>"/>
	 				<input name="ocupadosE" type="hidden" value="<?php echo $ocupadosE; ?>"/>
	 				<input class="cajatexto" id="espacioSeleccionado" name="nespacio" type="text" placeholder="Espacio seleccionado..." />
		 			<input class="boton" type="submit" value="Registrar"/>
 				</form>
 			</div> -->
 		</div> 	
 	</header>
 	<aside class="barralateral-principal">
 		<section class="barralateral">
 			<ul class="barralateral-menu">
 				<h3><?php echo($nusuario); ?></h3>
 				<div class="caja-menu">
 					<li>MENU PRINCIPAL</li>
 				</div>
 				<div class="caja-menu">
 					<li>
	 					<i></i><span>TICKETS</span><i></i>
	 					<ul>
	 						<!-- <li><a href="registrar/index.php"><span>Registrar</span></a></li> -->
	 						<li><a href="#"><span>Liberar</span></a></li>
	 						<li><a href="../historial/"><span>Historial</span></a></li>
	 						<li><a href="../vehiculos/"><span>Vehiculos</span></a></li>
	 					</ul>
	 				</li>
 				</div>
 				<div class="caja-menu">
 					<li>
	 					<i></i><span>USUARIOS</span><i></i>
	 					<ul>
	 						<li><a href="../"><span>Login</span></a></li>
	 						<li><a href="../usuario/"><span>Registrar</span></a></li>
	 						<li><a href="../"><span>Salir</span></a></li>
	 					</ul>
	 				</li>
 				</div>
 			</ul>
 		</section>
 	</aside>
 	<div class="contenedor">
 		<section class="contenido">
 			<h2>Seleccione el espacio de parqueo</h2>
		 	<div class="tablero">
				<table cellspacing="0" cellpadding="0">     
		            <tr>        
		                <?php  while($arr = mysql_fetch_array($espaciosOcupados)){ echo "<th class='espacios' id='".$arr['nombre_espacio']."' valor='".$arr['nombre_espacio']."'>".$arr['nombre_espacio']."</th>";}?>
		            </tr>
		        </table>
			</div>
			<h3>Liberar el espacio de parqueo</h3>
			<div class="caja">
				<input name="libresA" type="hidden" value="<?php echo $libresA; ?>"/>
 				<input name="ocupadosA" type="hidden" value="<?php echo $ocupadosA; ?>"/>
 				<input name="libresB" type="hidden" value="<?php echo $libresB; ?>"/>
 				<input name="ocupadosB" type="hidden" value="<?php echo $ocupadosB; ?>"/>
 				<input name="libresE" type="hidden" value="<?php echo $libresE; ?>"/>
 				<input name="ocupadosE" type="hidden" value="<?php echo $ocupadosE; ?>"/>
				<input class="cajatexto" id="usuarioSistema" type="text" placeholder="Usuario..." value="<?php echo "Usuario: ".$nusuario; ?>"/>
				<input class="cajatexto" id="fechaSalida" type="text" placeholder="Usuario..." value="<?php echo "Fecha Salida: ".$fechaSalida; ?>"/>
				<input class="cajatexto" id="edificioEspacio" type="text" placeholder="Edificio espacio..." value='<?php echo "Edificio: ".$nombre_piso; ?>'/>
				<input class="cajatexto" id="pisoEspacio" type="text" placeholder="Piso espacio..."/>
				<input class="cajatexto" id="espacioSeleccionado" type="text" placeholder="Espacio parqueo..."/>
				<input class="cajatexto" id="espacioOculto" type="hidden"/>
				<input class="cajatexto" id="placaVehiculo" type="text" placeholder="Placa vehiculo..."/>
				<input class="cajatexto" id="placaOculta" type="hidden"/>
				<input class="cajatexto" id="confirmarPlaca" type="text" placeholder="Confirmar placa..."/>
				<input class="boton" type="submit" value="Liberar" onclick="liberar();"/>
			</div>
 	  	</section>
 	</div>
 </body>
 </html>