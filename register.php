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
   $userExist = false;

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
      curl_setopt($curl, CURLOPT_URL, $url);
      curl_setopt($curl, CURLOPT_HTTPHEADER, array(
         'Content-Type: application/json',
      ));
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
      $result = curl_exec($curl);
      if(!$result){die("Connection Failure");}
      curl_close($curl);
      return $result;
    }

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
            $data_array =  array(
               "username"        => $_POST['user_name'],
               "password"        => $_POST['user_password'],
               "email"        => $_POST['email'],
               "user_created" => date('d/m/Y h:i:s a', time())
         );
         $make_call = callAPI('POST', 'https://cer052quse.execute-api.us-east-1.amazonaws.com/dev/user/', json_encode($data_array));

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
                  'location'      => array('S' => $_POST['location']),
                  'description'      => array('S' => ''),
                  'image'      => array('S' => '')
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
            <label for="phone">Birthday</label>
               <input name="birthday" id="input" type="date" class="form-control" placeholder="Birthday"required>
            </div>
            <div class="form-group">
               <input name="phone" id="input" type="tel" placeholder="Phone Number" class="form-control" required>
            </div>
            <div class="form-group">
            <label for="location">Location</label>
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