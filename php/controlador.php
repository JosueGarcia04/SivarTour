<?php
require_once 'vendor/autoload.php';
include 'connection.php';
$error = "";
session_start();


/****** G E T   U R L ******/
//despues del login redirecciono a la pagina donde estaba cuando le dió click al login
if (isset($_POST['goLogin'])) {
    $_SESSION['host'] = $_SERVER["HTTP_HOST"];
    $_SESSION['url'] = $_SERVER["REQUEST_URI"];
    header ('Location: login.php', true, 303);
}




/****** G O O G L E   L O G I N ******/
$clientID = '765073225137-1gggl6d22ovr6ne69sp9qdil8p6hb980.apps.googleusercontent.com';
$clientSecret = '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';
$redirectUri = 'http://localhost/SivarTour/profile.php';

// create Client Request to access Google API
$client = new Google_Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("profile");

//si google me envía 'code' por url
if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    $client->setAccessToken($token['access_token']);

    // get profile info
    $google_oauth = new Google_Service_Oauth2($client);
    $google_account_info = $google_oauth->userinfo->get();

    //array con la info que almacenaré en la db
    $userInfo = [
        'name' => $google_account_info['name'],
        'email' => $google_account_info['email'],
        'img' => $google_account_info['picture'],
        'token' => $google_account_info['id']
    ];

    //validar si ya está registrado
    $sql = "SELECT * FROM users WHERE email = '{$userInfo['email']}'";
    $run = mysqli_query($connection, $sql);

    //si ya está registrado
    if (mysqli_num_rows($run)) {
        $userInfo = mysqli_fetch_assoc($run);
        $token = $userInfo['token'];

    } else {
        //si no está registrado
        $sqll = "INSERT INTO users (name, email, password, img, token) VALUES ('{$userInfo['name']}', '{$userInfo['email']}', '', '{$userInfo['img']}', '{$userInfo['token']}')";
        $run = mysqli_query($connection, $sqll);

        if ($run) {
            $token = $userInfo['token'];
        }else {
            $error = "No se ha podido crear el usuario";
            die();
        }
    }

    $_SESSION['user_token'] = $token;
    $_SESSION['isLogin'] = 'si';
}




/****** L O G I N ******/
if(isset($_POST['login'])) {

    $correo = $_POST['email']; 
    $contraseña = $_POST['password'];

    $consulta_sql = mysqli_query($connection, "SELECT * FROM users WHERE email = '$correo' AND password = '$contraseña'");

    $nums = mysqli_num_rows($consulta_sql);
    $data = mysqli_fetch_assoc($consulta_sql);
    
    if($nums == 1){
        $token = $data['token'];
        $_SESSION['isLogin'] = 'si';
        $_SESSION['user_token'] = $token;
        header('Location: ' . $_SESSION['url']);
    }else if ($nums == 0){
        $error = "Los datos no coinciden";
    }

}




/****** R E G I S T R O ******/
if(isset($_POST['register'])) {

    $nombre = $_POST['name']; 
    $correo = $_POST['email']; 
    $contraseña = $_POST['password'];
    $token = substr(str_shuffle(str_repeat('0123456789', 3)), 0, 21);

    //preparo mi consulta sql para el ingreso de los valores en la base de datos
    $consulta_sql =  "INSERT INTO users (name, email, password, token) VALUES('$nombre', '$correo', '$contraseña', '$token')";

    //verifico que correo no se repita en la base de datos
    $validarCorreo = mysqli_query($connection, "SELECT * FROM users WHERE email = '$correo'"); 
    
    //validacion de correo existente
    if(mysqli_num_rows($validarCorreo) == 1){
        $error = "Correo electrónico ya registrado";

    } elseif (!preg_match('/^[a-zA-Z0-9]+$/', $contraseña)) {
        $error = "Solo se permiten letras y números";

    } elseif (strlen($contraseña) > 15) {
        $error = "El máximo de caracteres es 15";

    } elseif (strlen($contraseña) < 5){
        $error = "El mínimo de caracteres es 5";

    } else {
        $resultado = mysqli_query($connection, $consulta_sql);
        
        if($resultado){
            $_SESSION['isLogin'] = 'si';
            $_SESSION['user_token'] = $token;
            header('Location: ' . $_SESSION['url']);
        }else{
            $error = "Ha ocurrido un error";
        }
    }
}


?>