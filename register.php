<head>
   <link rel="stylesheet" href="css/style.css">
</head>
<html>

<body>
   <?php


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
   $userExist = false;



   if (isset($_POST['user_name']) && isset($_POST['email']) && isset($_POST['user_password'])) {
      if (empty($_POST['user_name']) || empty($_POST['email'])  || empty($_POST['user_password'])) {
         echo "<h3>All fields required</h3>";
      } else {
         $eav = $marshaler->marshalJson('{":username": "' . $_POST["user_name"] . '"}');
         $params = [
            'TableName' => $tableName,
            'KeyConditionExpression' => 'username = :username',
            'ExpressionAttributeValues' => $eav
         ];

         $result = $dynamodb->query($params);

         foreach ($result['Items'] as $users) {
            if ($marshaler->unmarshalValue($users['username']) == $_POST['user_name']) {
               $userExist = true;
               echo "The user already exists" . "<br>";
            }
         }

         if (!($userExist)) {
            date_default_timezone_set('Australia/Melbourne');
            $result = $dynamodb->putItem(array(
               'TableName' => $tableName,
               'Item' => array(
                  'username'      => array('S' => $_POST['user_name']),
                  'email'      => array('S' => $_POST['email']),
                  'password'      => array('S' => $_POST['user_password']),
                  'user_created'      => array('S' => date('d/m/Y h:i:s a', time()))
               )
            ));

            $result = $dynamodb->putItem(array(
               'TableName' => 'profile',
               'Item' => array(
                  'username'      => array('S' =>  $_POST['user_name']),
                  'email'      => array('S' => $_POST['email']),
                  'firstName'      => array('S' => $_POST['first_name']),
                  'lastName'      => array('S' =>  $_POST['last_name']),
                  'phone'      => array('S' => $_POST['phone']),
                  'gender' => array('S' => $_POST['gender']),
                  'birthDate'      => array('S' => $_POST['birthday']),
                  'location'      => array('S' => $_POST['location'])
               )
            ));

            echo '<script language=javascript>window.location.href="/"</script>';
            exit();
         }
      }
   }


   ?>


   <div class="forms-wrapper">
      <div class="forms-inner">
         <form action="" method="post" name="registration">
            <h1><b>Sign Up</b></h1><br>
            <div id="fullname">
               <div class="form-group">
                  <input id="namebox" name="first_name" placeholder="First Name" class="form-control" type="text" required />
               </div>
               <div class="form-group">
                  <input id="namebox" name="last_name" placeholder="Last Name" class="form-control" type="text" required />
               </div>
            </div>
            <div class="form-group">
               <input name="user_name" id="input" placeholder="Username" class="form-control" type="text" required>
            </div>
            <div class="form-group">
               <input name="email" id="input" placeholder="E-Mail Address" class="form-control" type="text" required>
            </div>
            <div class="form-group">
               <input name="user_password" id="input" placeholder="Password" class="form-control" type="password" required>
            </div>
            <div class="form-group">
               <input name="confirm_password" id="input" placeholder="Confirm Password" class="form-control" type="password" required>
            </div>
            <div class="form-group">
               <input name="birthday" id="input" type="date" class="form-control" required>
            </div>
            <div class="form-group">
               <input name="phone" id="input" type="number" placeholder="Phone Number" class="form-control" required>
            </div>
            <div class="form-group">
               <select id="input" name="location" class="form-control" required>
                  <?php
                  include 'countries.php';
                  ?>

               </select>
            </div>
            <div class="form-group">
               <p>Gender</p>
               <input type="radio" id="male" name="gender" value="male" checked>
               <label for="male">Male</label><br>
               <input type="radio" id="female" name="gender" value="female">
               <label for="female">Female</label><br>
               <input type="radio" id="other" name="gender" value="other">
               <label for="other">Other</label>
            </div>
            <div class="form-group">
               <a href="/" id="signin">Already have an account?</a>
            </div>
            <br>
            <div class="form-group">
               <button type="submit" class="btn">SUBMIT</button>
            </div>
         </form>
      </div>
   </div>
</body>

</html>