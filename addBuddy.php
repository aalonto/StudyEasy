<?php
$result = $dynamodb->putItem(array(
    'TableName' => 'friends',
    'Item' => array(
        'username1'      => array('S' => $_SESSION['username']),
        'username2'    => array('S' => $_SESSION['addBuddy']),
        'status'    => array('S' => "pending")
    )
));
