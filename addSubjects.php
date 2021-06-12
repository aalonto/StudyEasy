<?php

use Aws\DynamoDb\Exception\DynamoDbException;

if (!empty($user['subjects'])) {
    foreach ($user['subjects'] as $i) {
        echo "<p>" . $i . "</p>";
    }
}
?>
<p>
<form action="" method="post">
    <input type="text" placeholder="Add Subject" class="w3-border w3-padding" name="subject">
    <button type="submit" class="w3-button green-theme"><i class="fa fa-plus"></i></button>
    <?php
    if (isset($_POST['subject'])) {
        $key = $marshaler->marshalJson('
        {
            {"username": {"S": "' . $_SESSION['username'] . '"}}
        }
        ');

        $eav = $marshaler->marshalJson('
        {
            {":s":  {"SS": ["' . $_POST['subject'] . '"]}}
        }
        ');

        $params = [
            'TableName' => $tableName,
            'Key' => $key,
            'UpdateExpression' => 'ADD subjects :s',
            'ExpressionAttributeValues' =>  $eav,
            'ReturnValues' => 'UPDATED_NEW'
        ];

        try {
            $result = $dynamodb->updateItem($params);
            // header("Location: main.php");
            // exit();
        } catch (DynamoDbException $e) {
            echo "Unable to update item:\n";
            echo $e->getMessage() . "\n";
        }
    }
    ?>
</form>
</p>