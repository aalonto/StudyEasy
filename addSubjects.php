<?php
use Aws\DynamoDb\Exception\DynamoDbException;
$scan_response = $dynamodb->scan(array(
    'TableName' => 'subjects'
));

foreach ($scan_response['Items'] as $i) {
    $subject = $marshaler->unmarshalItem($i);
    if ($_SESSION['username'] == $subject['username']) {

        echo "<p>" . $subject['subject'] . "</p>";
    }
}
?>
<p>
<form action="" method="post">
    <input type="text" placeholder="Add Subject" class="w3-border w3-padding" name="subject">
    <button type="submit" class="w3-button green-theme"><i class="fa fa-plus"></i></button>
    <?php
    if (isset($_POST['subject'])) {

        $result = $dynamodb->putItem(array(
            'TableName' => 'subjects',
            'Item' => array(
                'username'      => array('S' => $_SESSION['username']),
                'subject'    => array('S' => $_POST['subject'])
            )
        ));
    }
    ?>
</form>
</p>