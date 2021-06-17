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

$tableName = 'profile';


$eav = $marshaler->marshalJson('
      {
        ":username": "' . $_SESSION['username'] . '"
      }
  ');

$params = [
  'TableName' => $tableName,
  'ProjectionExpression' => 'firstName, lastName, birthDate, #loc, email,gender, phone, description, image',
  'KeyConditionExpression' => 'username = :username',
  'ExpressionAttributeNames' => ['#loc' => 'location'],
  'ExpressionAttributeValues' => $eav
];


try {
  $result = $dynamodb->query($params);
  foreach ($result['Items'] as $i) {
    $user = $marshaler->unmarshalItem($i);
    $_SESSION['firstName'] = $user['firstName'];
    $_SESSION['lastName'] = $user['lastName'];
    $_SESSION['gender'] = $user['gender'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['phone'] = $user['phone'];
    $_SESSION['birthDate'] = $user['birthDate'];
    $_SESSION['location'] = $user['location'];
    $_SESSION['description'] = $user['description'];
    $_SESSION['image'] = $user['image'];
  }
} catch (DynamoDbException $e) {
  echo "Unable to query:\n";
  echo $e->getMessage() . "\n";
}


$eav1 = $marshaler->marshalJson('
      {
        ":username": "' . $_SESSION['username'] . '"
      }
  ');

$params1 = [
  'TableName' => 'preferences',
  'KeyConditionExpression' => 'username = :username',
  'ExpressionAttributeValues' => $eav1
];


try {
  $result1 = $dynamodb->query($params1);
  foreach ($result1['Items'] as $i) {
    $pref = $marshaler->unmarshalItem($i);
  }
} catch (DynamoDbException $e) {
  echo "Unable to query:\n";
  echo $e->getMessage() . "\n";
}



?>
<title>StudyEasy</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Open+Sans'>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<style>
  html,
  body,
  h1,
  h2,
  h3,
  h4,
  h5 {
    font-family: "Open Sans", sans-serif
  }
</style>

<body class="w3-theme-l5">

  <!-- Page Container -->
  <div class="w3-container w3-content" style="max-width:1400px;margin-top:80px">
    <!-- The Grid -->
    <div class="w3-row">
      <!-- Left Column -->
      <div class="w3-col m3">
        <!-- Profile -->
        <!---HERE -->
        <div class="w3-card w3-round w3-white">
          <div class="w3-container">
            <h4 class="w3-center"><a id="user"><?php echo $_SESSION['firstName'] . "  " . $_SESSION['lastName']; ?></a></h4>
            <?php
            if (!empty($_SESSION['image'])) {
              $src = 'https://studyeasya3.s3.us-east-1.amazonaws.com/' . $_SESSION['image'] . '';
            } else {
              $src = 'https://studyeasya3.s3.us-east-1.amazonaws.com/blank.png';
            }
            ?>
            </p>
            <p class="w3-center"><img src=<?php echo $src; ?> class="w3-circle" style="height:106px;width:106px" alt="Avatar">
              <hr>
            <p><i class="fa fa-user fa-fw w3-margin-right w3-text-theme"></i><?php echo $_SESSION['username']; ?></p>
            <p><i class="fa fa-home fa-fw w3-margin-right w3-text-theme"></i> <?php echo $_SESSION['location']; ?></p>
            <p><i class="fa fa-birthday-cake fa-fw w3-margin-right w3-text-theme"></i> <?php echo $_SESSION['birthDate']; ?></p>
            <hr>
            <p><a class="w3-center" href="/editProfile.php"><input class="w3-button green-theme" type="submit" id=" edit" name="edit" value="Edit Profile"></a></p>
          </div>
        </div>
        <br>

        <!-- Accordion -->
        <div class="w3-card w3-round">
          <div class="w3-white">
            <button onclick="myFunction('Demo1')" class="w3-button w3-block green-theme w3-left-align"><i class="fa fa-circle-o-notch fa-fw w3-margin-right"></i> My Subjects</button>
            <div id="Demo1" class="w3-hide w3-container">
              <p>
                <?php
                include 'addSubjects.php'
                ?>
              </p>
            </div>
            <button onclick="myFunction('Demo2')" class="w3-button w3-block green-theme w3-left-align"><i class="fa fa-calendar-check-o fa-fw w3-margin-right"></i> My Buddies</button>
            <div id="Demo2" class="w3-hide w3-container">
            <br>
              <?php
              $scan_response = $dynamodb->scan(array(
                'TableName' => 'friends'
              ));

              foreach ($scan_response['Items'] as $i) {
                $friends = $marshaler->unmarshalItem($i);
                if ($friends['username1'] == $_SESSION['username'] && $friends['status'] == 'friends') {
                  echo '<p>' . $friends['username2'] . '</p>';
                } elseif ($friends['username2'] == $_SESSION['username'] && $friends['status'] == 'friends') {
                  echo '<p>' . $friends['username1'] . '</p>';
                }
              }

              ?>
            </div>
            <button onclick="myFunction('Demo4')" class="w3-button w3-block green-theme w3-left-align"><i class="fa fa-calendar-check-o fa-fw w3-margin-right"></i> My Buddy Preferences</button>
            <div id="Demo4" class="w3-hide w3-container">
              <p>Location:
                <?php if (!empty($pref['location'])) {
                  echo  $pref['location'], " ";
                } ?></p>
              <p>Gender:
                <?php if (!empty($pref['gender'])) {
                  echo  $pref['gender'];
                } ?></p>
              <p>Subjects:
                <?php if (!empty($pref['subject'])) {
                  echo $pref['subject'], " ";
                } ?></p>

<p><a class="w3-center" href="/editProfile.php"><input class="w3-button green-theme" type="submit" id=" edit" name="edit" value="Edit Preferences"></a></p>

              </p>
            </div>
            <button onclick="myFunction('Demo3')" class="w3-button w3-block green-theme w3-left-align"><i class="fa fa-users fa-fw w3-margin-right"></i> My Buddy Requests</button>
            <div id="Demo3" class="w3-hide w3-container">
              <div class="w3-row-padding">
                <br>
                <?php
                if (isset($_POST['accept'])) {
                  $key = $marshaler->marshalJson('
                                {
                                    "username1": "' . $_POST['accept'] . '", 
                                    "username2": "' . $_SESSION['username'] . '"
                                }
                                ');

                  $eav = $marshaler->marshalJson('{":stat": "friends"}');

                  $dynamodb->updateItem([
                    'TableName' => 'friends',
                    'Key' => $key,
                    'UpdateExpression' => 'set status = :stat',
                    'ExpressionAttributeValues' => $eav,
                    'ReturnValues' => 'UPDATED_NEW'
                  ]);
                }

                if (isset($_POST['decline'])) {
                  $key = $marshaler->marshalJson('
                                {
                                    "username1": "' . $_POST['decline'] . '", 
                                    "username2": "' . $_SESSION['username'] . '"
                                }
                                ');

                  $dynamodb->deleteItem(array(
                    'TableName' => 'friends',
                    'Key' => $key
                  ));
                }
                ?>
                <?php
                $scan_response = $dynamodb->scan(array(
                  'TableName' => 'friends'
                ));

                foreach ($scan_response['Items'] as $i) {
                  $friends = $marshaler->unmarshalItem($i);
                  $request = $friends['username1'];

                  if ($friends['username2'] == $_SESSION['username'] && $friends['status'] == 'pending') {
                    echo '<div class="w3-half">
                        <p>' . $request . '</p>
                          </div>
                        <div class="w3-half">
                          <div class="w3-half">
                          <form method="post">
                            <button class="w3-button w3-block w3-green w3-section" value="' . $request . '" name="accept"><i class="fa fa-check"></i></button></div></form>
                          <div class="w3-half">
                          <form method="post">
                            <button class="w3-button w3-block w3-red w3-section" value="' . $request . '" name="decline"><i class="fa fa-remove"></i></button></div></form>
                        </div>';
                  }
                }
                ?>
              </div>
            </div>
          </div>
        </div>
        <br>

        <div class="w3-card w3-round w3-white w3-hide-small">
          <form action="/" action="post">
            <input type="submit" name="logOut" class="w3-button w3-block btn" value="Log Out">
            <?php
            if (isset($_POST['logOut'])) {
              session_start();
              $_SESSION = array();
              session_destroy();
              header("Location: home.php");
              exit();
            }
            ?>
          </form>
        </div>
        <br>

        <!-- End Left Column -->
      </div>

      <!-- Middle Column -->
      <div class="w3-col m9">
        <div class="w3-row-padding">
          <div class="w3-col m12">
            <?php include 'search.php'; ?>
          </div>
        </div>
        <div class="w3-container w3-card w3-white w3-round w3-margin"><br>

          <h3> Recommended Buddies</h3><br>


        </div>
        
        <?php
        $scan_response = $dynamodb->scan(array(
          'TableName' => 'profile'
        ));

        $friends = $dynamodb->scan(array(
          'TableName' => 'friends'
        ));

        $subjects = $dynamodb->scan(array(
          'TableName' => 'subjects'
        ));
        
        $count = 0;
        if (isset($_POST['view'])) {

          $_SESSION['viewUser'] = $_POST['view1'];
          echo "<script>
              window.location.href = 'userProfile.php';
              </script>";
        }

        if (!empty($pref)) {
          foreach ($scan_response['Items'] as $i) {
            $user = $marshaler->unmarshalItem($i);
            if ($user['username'] != $_SESSION['username']) {
              $notFriends = false;
              foreach ($friends['Items'] as $j) {
                $friend = $marshaler->unmarshalItem($j);
                if ($friend['username1'] != $_SESSION['username'] || $friend['username2'] != $user['username']) {

                  if ($friend['username2'] != $_SESSION['username'] || $friend['username1'] != $user['username']) {
                    $notFriends = true;
                  }}}
              if($notFriends ){
                    if ($user['location'] == $pref['location']) {
                      foreach ($subjects['Items'] as $x) {
                        $subject = $marshaler->unmarshalItem($x);
                        if ($subject['username'] == $user['username'] && $subject['subject'] == $pref['subject']) {
                          if ($user['gender'] == $pref['gender']) {
                            $count += 1;
                            if ($count < 4) {
                              $eav = $marshaler->marshalJson('
                                    {
                                      ":username": "' . $user['username'] . '"
                                    }
                                ');

                              $params = [
                                'TableName' => 'users',
                                'ProjectionExpression' => 'user_created',
                                'KeyConditionExpression' => 'username = :username',
                                'ExpressionAttributeValues' => $eav
                              ];


        ?>


                              <form action="" method="post" name="newUser">
                                <div class="w3-container w3-card w3-white w3-round w3-margin"><br>
                                  <img src=<?php
                                            if (!empty($user['image'])) {
                                              echo  'https://studyeasya3.s3.us-east-1.amazonaws.com/' . $user['image'] . '';
                                            } else {
                                              echo 'https://studyeasya3.s3.us-east-1.amazonaws.com/blank.png';
                                            } ?> alt="Avatar" class="w3-left w3-circle w3-margin-right" style="width:60px">
                                  <span class="w3-right w3-opacity">User Joined On: <?php $date ?></span>
                                  <h4><?php echo $user['firstName'], " ", $user['lastName'] ?> </h4><br>
                                  <p> <?php echo $user['description'] ?> </p>
                                  <hr class="w3-clear">
                                  <p>Gender: <?php echo $user['gender'] ?> </p>

                                  <form method="post">
                                    <input type="hidden" name="buddyName" value=<?php echo $user['username'] ?>>
                                    <input type="submit" class="w3-button green-theme" name="addButton" value="Add Buddy">
                                  </form>

                                  <input type="hidden" id=" view1" class="w3-button w3-block w3-left-align " name="view1" value=<?php echo $user['username'] ?>>
                                  <input type="submit" id=" view" class="w3-button green-theme " name="view" value="View Profile">
                                </div>
                              </form>

        <?php }
                          }
                        }
                      }
                    
                    }
                }
              
            }
          }
        }
        ?>
      </div>
    </div>
  </div>

  </div>

  </div>
  <br>


  <script>
    // Accordion
    function myFunction(id) {
      var x = document.getElementById(id);
      if (x.className.indexOf("w3-show") == -1) {
        x.className += " w3-show";
        x.previousElementSibling.className += " w3-theme-d1";
      } else {
        x.className = x.className.replace("w3-show", "");
        x.previousElementSibling.className =
          x.previousElementSibling.className.replace(" w3-theme-d1", "");
      }
    }
  </script>

</body>

</html>