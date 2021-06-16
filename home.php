<?php
session_start();

echo '<html>
    <body>';

require 'vendor/autoload.php';

date_default_timezone_set('UTC');

use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\DynamoDb\Marshaler;

$sdk = new Aws\Sdk([
  'credentials' => [
    'key'    => 'AKIA4WTDCA2IYDFWFGRE',
    'secret' => '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',
],
    'region'   => 'us-east-1',
    'version'  => 'latest'
]);

$dynamodb = $sdk->createDynamoDb();
$marshaler = new Marshaler();

$tableName = 'users';


function callAPI($method, $url, $data){
  $curl = curl_init();
  switch ($method){
     case "POST":
        curl_setopt($curl, CURLOPT_POST, 1);
        if ($data)
           curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        break;
     case "PUT":
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
        if ($data)
           curl_setopt($curl, CURLOPT_POSTFIELDS, $data);			 					
        break;
     default:
        if ($data)
           $url = sprintf("%s?%s", $url, http_build_query($data));
  }
  // OPTIONS:
  curl_setopt($curl, CURLOPT_URL, $url);
  curl_setopt($curl, CURLOPT_HTTPHEADER, array(
    //  'APIKEY: 111111111111111111111',
     'Content-Type: application/json',
  ));
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
  // EXECUTE:
  $result = curl_exec($curl);
  if(!$result){die("Connection Failure");}
  curl_close($curl);
  return $result;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $username = $_POST['username'];
  $password = $_POST['password'];
  $userExists = false;

  $eav = $marshaler->marshalJson('
      {
        ":username": "'.$_POST['username'].'"
      }
  ');

$params = [
    'TableName' => $tableName,
    'ProjectionExpression' => 'username, password',
    'KeyConditionExpression' => 'username = :username',
    'ExpressionAttributeValues'=> $eav
];

if(isset($_POST['username']) && isset($_POST['password'])) {
  $username = $_POST['username'];
  $password = $_POST['password'];

        $get_data = callAPI('GET', 'https://cer052quse.execute-api.us-east-1.amazonaws.com/dev/user/'.$username, false);
        $result = json_decode($get_data, true);

            $userExists = true;
            if($result == null){
              $userExists = false;
              echo "not right";
            }

            else{
              if($result['Item']['password'] == $password) {
                $_SESSION['username'] = $result['Item']['username'] ;
                    header('Location: main.php');
                    exit();
            }
            else{
              echo '
            Password Incorrect. Try Again';
            }}
}
}
?>
<title>Welcome to StudyEasy</title>
<div id="wrapper">
  <div class="main-content">
    <div class="header">
        <img src="logo.png">
    </div>
    <form action="" method="post">
      <div class="l-part">
        <input type="text" placeholder="Username" class="input-1" name="username" required/>
        <input type="password" placeholder="Password" name="password" class="input-2" required/>
        <button type="submit" class="btn">Log In</button>
      </div>
    </form>
  </div>
  <div class="sub-content">
    <div class="s-part">
      Don't have an account?<a href="/register.php"> Sign up</a>
    </div>
  </div>
</div>
</body>
</html>