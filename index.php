<head>
<link rel="stylesheet" href="css/style.css">
</head>
<?php

switch (@parse_url($_SERVER['REQUEST_URI'])['path']) {
    case '/':
        require 'home.php';
        break;
    case '/register.php':
        require 'register.php';
        break;
    case '/main.php':
        require 'main.php';
        break;
    case '/editProfile.php':
        require 'editProfile.php';
        break;
    default:
        http_response_code(404);
        exit('Not Found');
}

?>

