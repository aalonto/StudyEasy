<?php
use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\DynamoDb\Marshaler;

$dynamodb = $sdk->createDynamoDb();
$marshaler = new Marshaler();
$item = $marshaler->marshalJson('
                    {
                        "username1":  "' . $_SESSION["username"] . '",
                        "username2": "' . $_SESSION["addBuddy"] . '",
                        "status": "pending"
                    }
                    ');

                        $params = [
                            'TableName' => 'friends',
                            'Item' => $item
                        ];

                        try {
                            $dynamodb->putItem($params);
                        } catch (DynamoDbException $e) {
                            echo "Unable to update item:\n";
                            echo $e->getMessage() . "\n";
                        }
?>