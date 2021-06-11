<?php

use Aws\DynamoDb\Exception\DynamoDbException;

$tableName = 'subjects';


$eav = $marshaler->marshalJson('
      {
        ":username": "' . $_SESSION['username'] . '"
      }
  ');

$params = [
  'TableName' => $tableName,
  'KeyConditionExpression' => 'username = :username',
  'ExpressionAttributeValues' => $eav
];


try {
  $result = $dynamodb->query($params);
  foreach ($result['Items'] as $i) {
    $subject = $marshaler->unmarshalItem($i);
    echo "<p>" . $subject['subject'] . "</p>";
  }
} catch (DynamoDbException $e) {
  echo "Unable to query:\n";
  echo $e->getMessage() . "\n";
}
?>
<p>
<form action="" method="post">
    <input type="text" placeholder="Add Subject" class="w3-border w3-padding" name="subject">
    <button type="submit" class="w3-button green-theme"><i class="fa fa-plus"></i></button>
    <?php
    if (isset($_POST['subject'])) {

        $item = $marshaler->marshalJson('
        {
            "username":  "'.$_SESSION['username'].'",
            "subject": "' . $_POST['subject'] . '"
        }
        ');

        $params = [
            'TableName' => $tableName,
            'Item' => $item
        ];

        try {
            $result = $dynamodb->putItem($params);
            header("Location: main.php");
            exit();
        } catch (DynamoDbException $e) {
            echo "Unable to update item:\n";
            echo $e->getMessage() . "\n";
        }
    }
    ?>
</form>
</p>