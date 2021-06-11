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
  'ProjectionExpression' => 'firstName, lastName, birthDate, #loc, email,gender, phone',
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
            <h4 class="w3-center"><a href="/profile" id="user"><?php echo $_SESSION['firstName'] . "  " . $_SESSION['lastName']; ?></a></h4>
            <p class="w3-center"><img src="/w3images/avatar3.png" class="w3-circle" style="height:106px;width:106px" alt="Avatar"></p>
            <hr>
            <p><i class="fa fa-user fa-fw w3-margin-right w3-text-theme"></i><?php echo $_SESSION['username']; ?></p>
            <p><i class="fa fa-home fa-fw w3-margin-right w3-text-theme"></i> <?php echo $_SESSION['location']; ?></p>
            <p><i class="fa fa-birthday-cake fa-fw w3-margin-right w3-text-theme"></i> <?php echo $_SESSION['birthDate']; ?></p>
            <hr>
            <p class="w3-center"><span name="editProfile" href="/editProfile.php" class="w3-button green-theme">Edit Profile</span></p>
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
                <?php
                if (!empty($pref['location'])) {
                  foreach ($pref['location'] as $i) {
                    echo  $i, " ";
                  }
                } ?></p>
              <p>Gender:
                <?php if (!empty($pref['gender'])) {
                  echo  $pref['gender'];
                } ?></p>
              <p>Subjects:
                <?php if (!empty($pref['subjects'])) {
                  foreach ($pref['subjects'] as $i) {
                    echo  $i, " ";
                  }
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

            // foreach ($scan_response['Items'] as $music)
            // {

            $count = 0;
            foreach ($scan_response['Items'] as $i) {
              $user = $marshaler->unmarshalItem($i);

              if ($user['username'] != $_SESSION['username']) {
                if(!empty($pref)){

                foreach ($pref['location'] as $loc) {
                  if ($user['location'] == $loc) {
                    foreach ($pref['subjects'] as $subject) {
                      foreach ($user['subjects'] as $userSub) {
                        if ($userSub == $subject) {

                          if ($user['gender'] == $pref['gender']) {
                            $count += 1;
                            if ($count < 4) {

            ?>
                              <div class="w3-container w3-card w3-white w3-round w3-margin"><br>

                                <h3> Recommended Buddies</h3><br>


                              </div>

                              <div class="w3-container w3-card w3-white w3-round w3-margin"><br>
                                <img src="/w3images/avatar2.png" alt="Avatar" class="w3-left w3-circle w3-margin-right" style="width:60px">
                                <!-- <span class="w3-right w3-opacity">1 min</span> -->
                                <h4><?php echo $user['firstName'], " ", $user['lastName'] ?> </h4><br>
                                <hr class="w3-clear">
                                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>

                                <button type="button" class="w3-button w3-theme-d1 w3-margin-bottom"><i class="fas fa-user-check"></i> Request</button>
                              </div>

            <?php }
                          }
                        }
                      }
                    }}
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