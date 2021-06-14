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
        if (isset($_POST['addButton'])) {
            $_SESSION['addBuddy'] = $_POST['buddyName'];
            $result = $dynamodb->putItem(array(
                'TableName' => 'friends',
                'Item' => array(
                    'username1'      => array('S' => $_SESSION['username']),
                    'username2'    => array('S' => $_SESSION['addBuddy']),
                    'status'    => array('S' => "pending")
                )
            ));
        }
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

    ?>
</div>
<br>
<div class="w3-card w3-round w3-white w3-container ">
    <h6 class="w3-opacity">Buddy Advanced Search</h6>
    <form action="" value=userSearch method="post">
        <input type="text" placeholder="Search Location" class="w3-border w3-padding" name="location">
        <br>
        <br>
        <input type="text" placeholder="Search Subject" class="w3-border w3-padding" name="subject">
        <br>
        <br>
        <label for="gender">Gender:</label>
        <select name="gender">
            <option value="male">Male</option>
            <option value="female">Female</option>
            <option value="any">Any</option>
        </select>
        <br>
        <br>
        <button type="submit" class="w3-button green-theme"><i class="fa fa-search"></i> Search</button>
    </form>
    <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        if (isset($_POST['location'])) {
            echo '<ul id="buddySearch">';
            $eav = $marshaler->marshalJson('
                        {
                        ":loc": "' . $_POST['location'] . '"
                        } 
                    ');

            while (true) {
                $params = [
                    'TableName' => $tableName,
                    'ProjectionExpression' => 'username, firstName, lastName, #loc, gender',
                    'FilterExpression' => '#loc = :loc',
                    'ExpressionAttributeNames' => ['#loc' => 'location'],
                    'ExpressionAttributeValues' => $eav
                ];
                $result = $dynamodb->scan($params);
                foreach ($result['Items'] as $i) {
                    $user = $marshaler->unmarshalItem($i);
                    echo '<li><a href="#">' . $user['username'] . '</a></li>';
                }

                if (isset($result['LastEvaluatedKey'])) {
                    $params['ExclusiveStartKey'] = $result['LastEvaluatedKey'];
                } else {
                    break;
                }

                if (empty($result['Items'])) {
                    echo '<td>No results match query<td>';
                }
            }
        }
        echo '</ul>';
    }
    ?>
</div>