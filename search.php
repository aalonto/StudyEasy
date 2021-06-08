<?php
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
?>
<div class="w3-card w3-round w3-white">
    <div class="w3-container w3-padding">
        <h6 class="w3-opacity">Search by Username</h6>
        <form action="" value=userSearch method="post">
            <input type="text" placeholder="Search Name" class="w3-border w3-padding" name="username">
            <button type="submit" class="w3-button w3-theme"><i class="fa fa-pencil"></i>  Post</button>
        </form>
        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['username'])) {
                $eav = $marshaler->marshalJson('
                        {
                        ":buddy": "' . $_POST['username'] . '"
                        } 
                    ');

                $params = [
                    'TableName' => $tableName,
                    'ProjectionExpression' => 'username',
                    'KeyConditionExpression' => 'username = :buddy',
                    'ExpressionAttributeValues' => $eav
                ];

                try {
                    $result = $dynamodb->query($params);
                    foreach ($result['Items'] as $i) {
                        $user = $marshaler->unmarshalItem($i);
                        if ($user['username'] != $_SESSION['username']) {
                            echo '<p>' . $user['username'] . '<p>';
                        }
                    }
                } catch (DynamoDbException $e) {
                    echo "Unable to query:\n";
                    echo $e->getMessage() . "\n";
                }
            }
        }
        ?>
    </div>
</div>
<div class="w3-card w3-round w3-white">
    <div class="w3-container w3-padding">
        <h6 class="w3-opacity">Buddy Advanced Search</h6>
        <form action="" value=userSearch method="post">
            <input type="text" placeholder="Search Location" class="w3-border w3-padding" name="subject">
            <input type="text" placeholder="Search Subject" class="w3-border w3-padding" name="location">
            <!-- //RADIO BUTTONS FOR GENDER -->
            <button type="submit" class="w3-button w3-theme"><i class="fa fa-pencil"></i>  Post</button>
        </form>
        <?php
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (isset($_POST['subject'])) {
                $eav = $marshaler->marshalJson('
                        {
                        ":": "' . $_POST['username'] . '"
                        } 
                    ');

                $params = [
                    'TableName' => $tableName,
                    'ProjectionExpression' => 'username',
                    'KeyConditionExpression' => 'username = :buddy',
                    'ExpressionAttributeValues' => $eav
                ];

                try {
                    $result = $dynamodb->query($params);
                    foreach ($result['Items'] as $i) {
                        $user = $marshaler->unmarshalItem($i);
                        if ($user['username'] != $_SESSION['username']) {
                            echo '<p>' . $user['username'] . '<p>';
                        }
                    }
                } catch (DynamoDbException $e) {
                    echo "Unable to query:\n";
                    echo $e->getMessage() . "\n";
                }
            }
        }
        ?>
    </div>
</div>