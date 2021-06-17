<div class="w3-card w3-round w3-white w3-container">
    <h6 class="w3-opacity">Search by Username</h6>
    <form action="" value=userSearch method="post">
        <input type="text" placeholder="Search Username" class="w3-border w3-padding" name="username">
        <button type="submit" name="searchButton" class="w3-button green-theme"><i class="fa fa-search"></i> Search</button>
    </form>
    <?php
    $array = array();
    if (isset($_POST['searchButton'])) {
        echo '<ul id="buddySearch">';
        $scan_response = $dynamodb->scan(array(
            'TableName' => 'profile'
        ));

        foreach ($scan_response['Items'] as $i) {
            $user = $marshaler->unmarshalItem($i);
            $username = $user['username'];

            if ($_POST['username'] == $username) {
                echo '<li>';
                if (!empty($user['image'])) {
                    $src = 'https://studyeasya3.s3.us-east-1.amazonaws.com/' . $user['image'] . '';
                } else {
                    $src = 'https://studyeasya3.s3.us-east-1.amazonaws.com/blank.png';
                }
                $array = checkFriends($username, $dynamodb, $marshaler);
                echo '<img src=' . $src . ' class="w3-circle" style="height:106px;width:106px" alt="Avatar">   ' . $username . '                  
                          <form method="post">
                            <input type="hidden" name="buddyName" value="' . $username . '"> 
                            <input type="submit" id="addButton" class="w3-button w3-center green-theme" name="addButton" value="' . $array[2] . '">
                          </form>
                         </li>';
            }
        }
        echo '</ul>';
    }

    //addBuddy($array, $dynamodb, $marshaler);
    if (isset($_POST['addButton'])) {
        $array = checkFriends($_POST['buddyName'], $dynamodb, $marshaler);
        $key = $marshaler->marshalJson('
        {
            "username1": "' . $array[0] . '", 
            "username2": "' . $array[1] . '"
        }
        ');
        if ($_POST['addButton'] == "Add Buddy") {
            $dynamodb->putItem(array(
                'TableName' => 'friends',
                'Item' => array(
                    'username1'      => array('S' => $array[0]),
                    'username2'    => array('S' => $array[1]),
                    'status'    => array('S' => "pending")
                )
            ));
        } elseif ($_POST['addButton'] == "Cancel Request") {

            $dynamodb->deleteItem(array(
                'TableName' => 'friends',
                'Key' => $key
            ));
        } elseif ($_POST['addButton'] == "Accept Request") {
            $eav = $marshaler->marshalJson('{":stat": "friends"}');

            $dynamodb->updateItem([
                'TableName' => 'friends',
                'Key' => $key,
                'UpdateExpression' => 'set status = :stat',
                'ExpressionAttributeValues' => $eav,
                'ReturnValues' => 'UPDATED_NEW'
            ]);
        }
    }

    ?>
</div>
<br>
<div class="w3-card w3-round w3-white w3-container ">
    <h6 class="w3-opacity">Buddy Advanced Search</h6>
    <form action="" value=userSearch method="post">
        <input type="text" placeholder="Search Location" class="w3-border w3-padding" name="locationSearch">
        <br>
        <br>
        <input type="text" placeholder="Search Subject" class="w3-border w3-padding" name="subjectSearch">
        <br>
        <br>
        <label for="genderSearch">Gender:</label>
        <select name="genderSearch">
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="any" selected>Any</option>
        </select>
        <br>
        <br>
        <button type="submit" name="advancedSearch" class="w3-button green-theme"><i class="fa fa-search"></i> Search</button>
    </form>
    <?php
    if (isset($_POST['advancedSearch'])) {
        echo '<ul id="buddySearch">';
        $locSearched = !empty($_POST["locationSearch"]);
        $subjectSearched = !empty($_POST["subjectSearch"]);
        $genderSearched = $_POST['genderSearch'] != 'any';
        $searchResults = array();

        $scan_response = $dynamodb->scan(array(
            'TableName' => 'profile'
        ));

        foreach ($scan_response['Items'] as $i) {
            $user = $marshaler->unmarshalItem($i);
            $username = $user['username'];


            if ($locSearched && $genderSearched) {
                if ($_POST['locationSearch'] == $user['location'] && $_POST['genderSearch'] == $user['gender']) {
                    array_push($searchResults, $username);
                }
            } else if ($locSearched && !$genderSearched) {
                if ($_POST['locationSearch'] == $user['location']) {
                    array_push($searchResults, $username);
                }
            } else if (!$locSearched && $genderSearched) {
                if ($_POST['genderSearch'] == $user['gender']) {
                    array_push($searchResults, $username);
                }
            }
        }

        $completeSearch = array();

        foreach ($searchResults as $item) {
            if ($subjectSearched) {
                $scan_response = $dynamodb->scan(array(
                    'TableName' => 'subjects'
                ));

                foreach ($scan_response['Items'] as $i) {
                    $subject = $marshaler->unmarshalItem($i);
                    if ($item == $subject['username']) {
                        if (!in_array($item, $completeSearch, true)) {
                            array_push($completeSearch, $item);
                        }
                    }
                }
            } else {
                $completeSearch = $searchResults;
            }
        }
        $notFriends = false;
        foreach ($completeSearch as $a) {
            if ($a !== $_SESSION['username']) {
                $scan_response = $dynamodb->scan(array(
                    'TableName' => 'profile'
                ));

                foreach ($scan_response['Items'] as $i) {
                    $user = $marshaler->unmarshalItem($i);
                    $username = $user['username'];

                    if ($a == $username) {
                        echo '<li>';
                        if (!empty($user['image'])) {
                            $src = 'https://studyeasya3.s3.us-east-1.amazonaws.com/' . $user['image'] . '';
                        } else {
                            $src = 'https://studyeasya3.s3.us-east-1.amazonaws.com/blank.png';
                        }
                        $array = checkFriends($username, $dynamodb, $marshaler);
                        echo '<img src=' . $src . ' class="w3-circle" style="height:106px;width:106px" alt="Avatar">   ' . $username . '                  
                          <form method="post">
                            <input type="hidden" name="buddyName" value="' . $username . '"> 
                            <input type="submit" id="addButton" class="w3-button w3-center green-theme" name="addButton" value="' . $array[2] . '">
                          </form>
                         </li>';
                    }
                }
            }
        }
        echo '</ul>';
    }

    function checkFriends($buddy, $dynamodb, $marshaler)
    {
        $return = array();
        $scan_response = $dynamodb->scan(array(
            'TableName' => 'friends'
        ));

        foreach ($scan_response['Items'] as $i) {
            $friends = $marshaler->unmarshalItem($i);
            $first = $friends['username1'];
            $second = $friends['username2'];
            $status = $friends['status'];
            if ($first == $_SESSION['username'] && $second == $buddy) {
                if ($status == 'pending') {
                    array_push($return, $first, $second, 'Cancel Request');
                    break;
                } elseif ($status == 'friends') {
                    array_push($return, $first, $second, 'Remove Buddy');
                    break;
                }
            } elseif ($first == $buddy && $second == $_SESSION['username']) {
                if ($status == 'pending') {
                    array_push($return, $first, $second, 'Accept Request');
                    break;
                } elseif ($status == 'friends') {
                    array_push($return, $first, $second, 'Remove Buddy');
                }
            }
        }

        if (empty($array)) {
            array_push($return, $_SESSION['username'], $buddy, 'Add Buddy');
        }
        return $return;
    }
    ?>
</div>