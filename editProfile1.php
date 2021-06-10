<?php
session_start();

echo '<html>';

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

$eav = $marshaler->marshalJson('{":fN": "' . $POST['firstName'] . '"}');

// dynamodb table update (NEW ATTRIBUTE)
$params = [
    'TableName' => 'profile',
    'Key' => ''. $_SESSION['username'] .'',
    'UpdateExpression' => 'set firstName = :fN',
    'ExpressionAttributeValues' => $eav,
    'ReturnValues' => 'UPDATED_NEW'
];

if (isset($_POST['firstName']) && isset($_POST['lastName']) && isset($_POST['birthDate'])) {
  if (empty($_POST['firstName']) || empty($_POST['lastName'])  || empty($_POST['birthDate'])) {
     echo "<h3>All fields required</h3>";
  } else {

    $result = $dynamodb->updateItem($params);
    echo "Updated item.\n";
    print_r($result['Attributes']);}}

?>


?>


      <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
      <form action="" method="post" name="edit">
    <div class="form-group">
        <div class="col-md-4">
            <div class="card user-card">
                <div class="card-header">
                    <h5>Profile</h5>
                </div>
                <div class="card-block">
                    <div class="user-image">
                        <img src="https://bootdey.com/img/Content/avatar/avatar3.png" class="img-radius" alt="User-Profile-Image">
                    </div>
                    <h6> First Name: <input type="text"  class="f-w-600 m-t-25 m-b-10" name = "firstName" value =<?php echo $_SESSION['firstName']?>></h6>
                    <h6>Last Name:<input type="text"  class="f-w-600 m-t-25 m-b-10" value =<?php echo $_SESSION['lastName']?>></h6>
                    <h6>Birthday:<input type="date"  class="f-w-600 m-t-25 m-b-10" value =<?php echo $_SESSION['birthDate']?>></h6>
                    <h6>Location:<input type="text"  class="f-w-600 m-t-25 m-b-10" value =<?php echo $_SESSION['location']?>></h6>
                    <h6>Email:<input type="text"  class="f-w-600 m-t-25 m-b-10" value =<?php echo $_SESSION['email']?>></h6>
                   
    
                    <p class="text-muted m-t-15">Study Buddies
                                <i class="fa fa-user"></i> 10</p>
                            </div>
                    <ul class="list-unstyled activity-leval">
                    <?php
              if(!empty($user['friends'])) {
              foreach ($user['friends'] as $i) {
                echo "<p>" . $i . "</p>";
              }
            }

              ?>
                    </ul>
            
         
                          
             
                    </div>
                    <p class="m-t-15 text-muted">Lorem Ipsum is simply dummy text of the printing and typesetting industry.</p>
                    <hr>
        </div> <h4> Contact </h4>
        <i class="m-t-15 text-muted"></i>Phone Number: <?php echo $_SESSION['phone']?></p>
        <i class="m-t-15 text-muted"></i>Email: <?php echo $_SESSION['email']?></p>
                    <div class="row justify-content-center user-social-link">
                        <div class="col-auto"><a href="#!"><i class="fa fa-facebook text-facebook"></i></a>
                         <class="col-auto"><a href="#!"><i class="fa fa-twitter text-twitter"></i></a>
                        <class="col-auto"><a href="#!"><i class="fa fa-dribbble text-dribbble"></i></a></div>
                    </div>
                </div>
            </div>
            <input  class="text-centre" type="submit" id=" save" name="save" value= "Save" >
        
     