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

$tableName = 'profile';


$eav = $marshaler->marshalJson('
      {
        ":username": "' . $_SESSION['username'] . '"
      }
  ');

$params = [
  'TableName' => $tableName,
  'ProjectionExpression' => 'firstName, lastName, birthDate, #loc, email,gender, phone, description',
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

// $key = $marshaler->marshalJson('
//     {
//         "username": ' . $_SESSION['username'] . '
//     }
// ');

// $eav = array(
//   ':s' => array(
//     'First'=> array('S'=>'one')
//   )

// );

// $params = [
//   'TableName' => 'profile',
//   'Key' => $key,
//   'UpdateExpression' => 
// 'set subjects = :s',
//   'ExpressionAttributeValues'=> $eav,
//   'ReturnValues' => 'UPDATED_NEW'
// ];

// $result = $dynamodb->updateItem($params);


?>
<title>StudyEasy</title>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="css/style.css">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<!-- <link rel="stylesheet" href="https://www.w3schools.com/lib/w3-theme-blue-grey.css"> -->
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
              $src = 'https://studyeasy.s3.us-east-1.amazonaws.com/'. $_SESSION['image'].'';
            } else {
              $src = 'https://studyeasy.s3.us-east-1.amazonaws.com/blank.png';
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
                // if (!empty($user['subjects'])) {
                //   foreach ($user['subjects'] as $i) {
                //     echo "<p>" . $i . "</p>";
                //   }
                // }
                include 'addSubjects.php'
                ?>
              </p>
            </div>
            <button onclick="myFunction('Demo2')" class="w3-button w3-block green-theme w3-left-align"><i class="fa fa-calendar-check-o fa-fw w3-margin-right"></i> My Buddies</button>
            <div id="Demo2" class="w3-hide w3-container">
              <p>Some other text.. </p>
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

              </p>
            </div>
            <button onclick="myFunction('Demo3')" class="w3-button w3-block green-theme w3-left-align"><i class="fa fa-users fa-fw w3-margin-right"></i> My Buddy Requests</button>
            <div id="Demo3" class="w3-hide w3-container">
              <div class="w3-row-padding">
                <br>
                <div class="w3-half">
                  <img src="/w3images/lights.jpg" style="width:100%" class="w3-margin-bottom">
                </div>
                <div class="w3-half">
                  <img src="/w3images/nature.jpg" style="width:100%" class="w3-margin-bottom">
                </div>
                <div class="w3-half">
                  <img src="/w3images/mountains.jpg" style="width:100%" class="w3-margin-bottom">
                </div>
                <div class="w3-half">
                  <img src="/w3images/forest.jpg" style="width:100%" class="w3-margin-bottom">
                </div>
                <div class="w3-half">
                  <img src="/w3images/nature.jpg" style="width:100%" class="w3-margin-bottom">
                </div>
                <div class="w3-half">
                  <img src="/w3images/snow.jpg" style="width:100%" class="w3-margin-bottom">
                </div>
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
            <?php include 'search.php';
            $scan_response = $dynamodb->scan(array(
              'TableName' => 'profile'
            ));

            $friends = $dynamodb->scan(array(
              'TableName' => 'friends'
            ));

            $subjects = $dynamodb->scan(array(
              'TableName' => 'subjects'
            ));

            // foreach ($scan_response['Items'] as $music)
            // {


            $count = 0;
            if (isset($_POST['view'])) {

              $_SESSION['viewUser'] = $_POST['view1'];
              echo "<script>
              window.location.href = 'userProfile.php';
              </script>";
						}
            if(!empty($pref)){
            foreach ($scan_response['Items'] as $i) {
              $user = $marshaler->unmarshalItem($i);
              if ($user['username'] != $_SESSION['username']) {
                foreach ($friends['Items'] as $j) {
                  $friend = $marshaler->unmarshalItem($j);
                  if ($friend['username1'] != $_SESSION['username'] || $friend['username2'] != $user['username']) {

                    if ($friend['username2'] != $_SESSION['username'] || $friend['username1'] != $user['username']) {

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
                                  <div class="w3-container w3-card w3-white w3-round w3-margin"><br>

                                    <h3> Recommended Buddies</h3><br>


                                  </div>
                                  <form action="" method="post" name="newUser">
                                    <div class="w3-container w3-card w3-white w3-round w3-margin"><br>
                                      <img src="/w3images/avatar2.png" alt="Avatar" class="w3-left w3-circle w3-margin-right" style="width:60px">
                                      <span class="w3-right w3-opacity">User Joined On: <?php $date ?></span>
                                      <h4><?php echo $user['firstName'], " ", $user['lastName'] ?> </h4><br>
                                      <p> <?php echo $user['description'] ?> </p>
                                      <hr class="w3-clear">
                                      <p>Gender: <?php echo $user['gender'] ?> </p>

                                      <button type="button"><i class="fa fa-user-plus fa-fw w3-margin-right w3-text-theme"></i>Request</button>

                                      <!-- <button type="button2"><i class="fa fa-user-plus fa-fw w3-margin-right w3-text-theme"></i>View Profile</button>
                                  <input type="hidden" name="view" value=<?php echo $user['username'] ?>> -->

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
              }
            }
            ?>

            <!-- End Middle Column -->
          </div>
        </div>
      </div>

      <!-- Right Column
      <div class="w3-col m2">
        <div class="w3-card w3-round w3-white w3-center">
          <div class="w3-container">
            <p>Upcoming Events:</p>
            <img src="/w3images/forest.jpg" alt="Forest" style="width:100%;">
            <p><strong>Holiday</strong></p>
            <p>Friday 15:00</p>
            <p><button class="w3-button w3-block w3-theme-l4">Info</button></p>
          </div>
        </div>
        <br> -->

      <!-- <div class="w3-card w3-round w3-white w3-center">
          <div class="w3-container">
            <p>Friend Request</p>
            <img src="/w3images/avatar6.png" alt="Avatar" style="width:50%"><br>
            <span>Jane Doe</span>
            <div class="w3-row w3-opacity">
              <div class="w3-half">
                <button class="w3-button w3-block w3-green w3-section" title="Accept"><i class="fa fa-check"></i></button>
              </div>
              <div class="w3-half">
                <button class="w3-button w3-block w3-red w3-section" title="Decline"><i class="fa fa-remove"></i></button>
              </div>
            </div>
          </div>
        </div>
        <br> -->

      <!-- End Grid -->
    </div>

    <!-- End Page Container -->
  </div>
  <br>

  <!-- Footer -->
  <footer class="w3-container w3-theme-d3 w3-padding-16">
    <h5>Footer</h5>
  </footer>

  <footer class="w3-container w3-theme-d5">
    <p>Powered by <a href="https://www.w3schools.com/w3css/default.asp" target="_blank">w3.css</a></p>
  </footer>

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