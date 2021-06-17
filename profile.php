<?php
session_start();

echo '<html>';

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
?>
<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" rel="stylesheet">
<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="card user-card">
                <div class="card-header">
                    <h5>Profile</h5>
                </div>
                <div class="card-block">
                    <div class="user-image">
                        <img src="https://bootdey.com/img/Content/avatar/avatar2.png" class="img-radius" alt="User-Profile-Image">
                    </div>
                    <h6 class="f-w-600 m-t-25 m-b-10"><?php echo $_SESSION['firstName'] . "  " . $_SESSION['lastName']; ?></h6>
                    <p class="f-w-600 m-t-25 m-b-10">Born <?php echo $_SESSION['birthDate']. "| Gender"?> <?php echo $_SESSION['gender']. "|Location"?> <?php echo $_SESSION['location']. "|"?></p>
                    <hr>
                    <p class="text-muted m-t-15">Study Buddies
                                <i class="fa fa-user"></i> 10</p>
                            </div>
                    <ul class="list-unstyled activity-leval">
                    <?php
              if(!empty($_SESSION['friends'])) {
              foreach ($_SESSION['friends'] as $i) {
                echo "<p>" . $i . "</p>";
              }
            }

              ?>
                    </ul>
                    <div class="bg-c-blue counter-block m-t-10 p-20">
                        <div class="row">
                          
                        </div>
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
            <a href="/edit"><input  class="text-centre" type="submit" id=" edit" name="edit" value= "Edit" >
        
     