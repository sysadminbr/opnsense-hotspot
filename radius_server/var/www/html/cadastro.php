<?php
session_start();

if($_SERVER['REQUEST_METHOD'] == "POST"){
    include("db.php");
    $login = $_REQUEST["login"];
    $password = $_REQUEST["password"];

    $stmt = $conn->prepare("SELECT COUNT(*) FROM radcheck WHERE username = ?");

    if ($stmt === false) {
        die('Erro interno');
    }

    $stmt->bind_param('s', $login);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if($count > 0){
        die("usuario ja cadastrado");
    }else{    
        $stmt = $conn->prepare("INSERT INTO radcheck (username, attribute, op, value) VALUES (?, 'Cleartext-Password', ':=', ?)");

        if ($stmt === false) {
            die('Erro interno');
        }
        $stmt->bind_param('ss', $login, $password);
        if (!$stmt->execute()) {
            die('Erro interno');
        }
        $stmt->close();
        $_SESSION['registration'] = 'successful';
        header('Location: /index.php');
    }
}else{
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
    <style>
      @font-face {
        font-family: VivoTypeW05;
        src: url('/_files/VivoTypeW05-Regular.woff2'), url('/_files/VivoTypeW05-Regular.woff')
      }
    </style>
    <link href="/_files/main.css" rel="stylesheet">
    <style data-styled="" data-styled-version="4.4.1"></style>
  </head>
  <body class="dx-device-desktop dx-device-generic" style="overflow: auto;">
    <noscript>Você precisa ter o JavaScript ativado para rodar esse app.</noscript>
    <div id="root">
      <section class="containerLogin">
        <form name="formCadastro" action="cadastro.php" method="POST">
       <div class="container1 container-fluid">
          <div class="row">
            
            <div class="">
              <div class="container-principal">
                <div class="box-login">
                  <div class="box-input">
                    <div class="person-selector">
                      <div class="descricao selected">Registre-se com seu CPF</div>
                    </div>
                    <div class="containerInputLogin">
                      <div class="group material-input documento" style="position: relative;">
                        <label class="sr-only"></label>
                        <input type="text" class="form-control input-lg " id="login" name="login" title="" placeholder="" maxlength="14" required="" autocomplete="new-password" inputmode="numeric" pattern="\d*" value="">
                        <span class="highlight"></span>
                        <span class="bar"></span>
                        <label>CPF:</label>
                      </div>
                      <div class="group material-input telefone" style="position: relative;">
                        <label class="sr-only"></label>
                        <input type="password" class="form-control input-lg " id="password" name="password" title="" placeholder="" maxlength="20" required="" autocomplete="new-password" inputmode="text" value="">
                        <span class="highlight"></span>
                        <span class="bar"></span>
                        <label>Senha:</label>
                      </div>
                      <div class="btnContinue">
                        <input type="submit" class="btn" style="width: 100%;" value="Registrar-se!" />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
       </form>
      </section>
    </div>
  </body>
</html>

<?php
}
