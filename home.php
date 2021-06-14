<?php
session_start();

echo '<html>
    <body>';

require 'vendor/autoload.php';

date_default_timezone_set('UTC');

use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\DynamoDb\Marshaler;

$sdk = new Aws\Sdk([
    'region'   => 'us-east-1',
    'version'  => 'latest'
]);

$dynamodb = $sdk->createDynamoDb();
$marshaler = new Marshaler();

$tableName = 'users';

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
    'ProjectionExpression' => 'username, password, image',
    'KeyConditionExpression' => 'username = :username',
    'ExpressionAttributeValues'=> $eav
];

if(isset($_POST['username']) && isset($_POST['password'])) {
  $username = $_POST['username'];
  $password = $_POST['password'];
    try {
        $result = $dynamodb->query($params);
        foreach ($result['Items'] as $i) {
            $user = $marshaler->unmarshalItem($i);
            $userExists = true;
            if($user['password'] == $password) {
                $_SESSION['username'] = $user['username'];
                $_SESSION['image'] =  $user['image'];
                header('Location: main.php');
                exit();
            } else {
                echo 'Password Incorrect. Try Again';
            }  
        }
        if(!$userExists) {
          echo "Please enter a valid username.";
        }        
    } catch (DynamoDbException $e) {
    echo "Unable to query:\n";
    echo $e->getMessage() . "\n";
    }
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