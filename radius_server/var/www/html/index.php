<?php
session_start();

if(empty($_SESSION['return_form'])){
	$_SESSION['return_form'] = $_GET['targetOrigin'];
}

if($_SESSION['zone'] == null){
	$_SESSION['zone'] = $_GET['zone'];
}

?>
<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
    <meta name="theme-color" content="#660096">
    <meta name="apple-mobile-web-app-status-bar-style" content="#660096">
    <meta http-equiv="cache-control" content="max-age=0">
    <meta http-equiv="cache-control" content="no-cache">
    <meta http-equiv="expires" content="0">
    <meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT">
    <meta http-equiv="pragma" content="no-cache">
    <meta name="description" content="Autenticação Hotspot">
    <meta property="og:locale" content="pt_BR">
    <meta property="og:title" content="Hotspot Wi-Fi">
    <meta property="og:site_name" content="Hotspot Wi-Fi CitraIT">
    <meta property="og:description" content="Autenticação Hotspot Wi-Fi">
    <link id="favicon" rel="shortcut icon" href="/fav.png">
    <script src="/_files/jquery.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com/">
    <link rel="preconnect" href="https://fonts.gstatic.com/" crossorigin="">
    <link href="/_files/roboto.css" rel="stylesheet">
    <title>Autenticação Hotspot Wi-Fi</title>
    <script>
      ! function(e, t, n, a) {
        ! function(e, n, a) {
          if (e) {
            var i = t.createElement("style");
            i.id = "at-body-style", i.innerHTML = a, e.appendChild(i)
          }
        }(t.getElementsByTagName("head")[0], 0, "body {opacity: 0 !important}"), setTimeout((function() {
          var e = t.getElementsByTagName("head")[0];
          if (e) {
            var n = t.getElementById("at-body-style");
            n && e.removeChild(n)
          }
        }), 3e3)
      }(window, document)
    </script>
    <style>
      @font-face {
        font-family: VivoTypeW05;
        src: url('/_files/VivoTypeW05-Regular.woff2'), url('/_files/VivoTypeW05-Regular.woff')
      }
    </style>
    <link href="/_files/main.css" rel="stylesheet">
    <style data-styled="" data-styled-version="4.4.1"></style>

<script>
function sendForm(){
        let login = document.getElementById("login").value;
	let password = document.getElementById("password").value;
	var x = new XMLHttpRequest();
	x.open("POST", "<?=$_SESSION['return_form']?>/api/captiveportal/access/logon/"+<?=$_SESSION['zone'];?>+"/");
	x.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	x.onloadend = (e)=>{
		console.log(x.responseText);
		let response = JSON.parse(x.responseText);
		if(response["clientState"]=="AUTHORIZED"){
			document.location.href="https://www.google.com/";
		}else if(response["clientState"]=="NOT_AUTHORIZED"){
			alert("usuario ou senha invalidos!");
			document.querySelector("#password").value = "";
		}
	};
	x.send("user="+login+"&password="+password);
}
document.addEventListener('DOMContentLoaded', ()=>{
        // usuario registrado?
	<?php
	if(!empty($_SESSION['registration']) && $_SESSION['registration'] == 'successful'){
		unset($_SESSION['registration']);
	?>
		alert("Acesso registrado com sucesso! Você agora pode realizar o login.");
	<?php
	}
	?>
});
</script>


  </head>
  <body class="dx-device-desktop dx-device-generic" style="overflow: auto;">
    <noscript>Você precisa ter o JavaScript ativado para rodar esse app.</noscript>
    <div id="root">
      <section class="containerLogin">
        <div class="fundoB2C active"></div>
        <div class="fundoB2B "></div>
        <div class="container1 container-fluid">
          <div class="row">
            <div class="col-md-4 col-sm-12">
              <div class="container2">
                <div class="container-logo">
                  <h1 style="color: #333" class="logo-vivo-em-dia d-none d-sm-none d-md-none d-lg-block">Portal Wi-Fi</h1>
				  <h1 style="color: #fff" class="logo-vivo-em-dia d-block d-sm-block d-md-block d-lg-none">Portal Wi-Fi</h1>
                  <div class="container-texto">Caso tenha cadastro, basta inserir seus dados. <br>
                    <br>Se ainda não tiver, clique em registrar-se.
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-4 col-12 col-sm-12 mobileMrAutoMlAuto mobilePadding0">
              <div class="container-principal">
                <div class="box-login">
                  <div class="box-input">
                    <div class="person-selector">
                      <div class="descricao selected">Insira suas Credenciais de Acesso</div>
                    </div>
                    <div class="containerInputLogin">
                      <div class="group material-input documento" style="position: relative;">
                        <label class="sr-only"></label>
                        <input type="text" class="form-control input-lg " id="login" title="" placeholder="" maxlength="14" required="" autocomplete="new-password" inputmode="numeric" pattern="\d*" value="">
                        <span class="highlight"></span>
                        <span class="bar"></span>
                        <label>CPF:</label>
                      </div>
                      <div class="group material-input telefone" style="position: relative;">
                        <label class="sr-only"></label>
                        <input type="password" class="form-control input-lg " id="password" title="" placeholder="" maxlength="20" required="" autocomplete="new-password" inputmode="text" value="">
                        <span class="highlight"></span>
                        <span class="bar"></span>
                        <label>Senha:</label>
                      </div>
                      <div class="btnContinue">
                        <button class="btn" onclick="sendForm()" style="width: 100%;">Continuar</button>
			<button class="btn" onclick="javascript:document.location.href='cadastro.php?zone=<?=$_SESSION['zone']?>'" style="width: 100%;margin-top:10px;">Registrar-se</button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </div>
  </body>
</html>
