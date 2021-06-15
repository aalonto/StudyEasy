<div class="w3-card w3-round w3-white w3-container">
    <h6 class="w3-opacity">Search by Username</h6>
    <form action="" value=userSearch method="post">
        <input type="text" placeholder="Search Username" class="w3-border w3-padding" name="username">
        <button type="submit" name="searchButton" class="w3-button green-theme"><i class="fa fa-search"></i> Search</button>
    </form>
    <?php
    if (isset($_POST['searchButton'])) {
        echo '<ul>';
        $scan_response = $dynamodb->scan(array(
            'TableName' => 'profile'
        ));

        foreach ($scan_response['Items'] as $i) {
            $user = $marshaler->unmarshalItem($i);
            $username = $user['username'];

            if ($_POST['username'] == $username) {
                echo '<li>' . $username . '                  
                          <form method="post">
                            <input type="hidden" name="buddyName" value="' . $username . '"> 
                            <input type="submit" class="w3-button green-theme" name="addButton" value="Add Buddy">
                          </form>
                         </li>';
            }
        }
        echo '</ul>';
    }

    if (isset($_POST['addButton'])) {
        $result = $dynamodb->putItem(array(
            'TableName' => 'friends',
            'Item' => array(
                'username1'      => array('S' => $_SESSION['username']),
                'username2'    => array('S' => $_POST['buddyName']),
                'status'    => array('S' => "pending")
            )
        ));
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
        echo '<ul>';
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
        foreach ($completeSearch as $a) {
            if ($a !== $_SESSION['username']) {
                echo '<li>' . $a . '                  
                      <form method="post">
                        <input type="hidden" name="buddyName" value="' . $a . '"> 
                        <input type="submit" class="w3-button green-theme" name="addButton" value="Add Buddy">
                      </form>
                     </li>';
            }
        }
    }
    ?>
</div>