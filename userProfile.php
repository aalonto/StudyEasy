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
        ":username": "' . $_SESSION['viewUser'] . '"
      }
  ');

$params = [
  'TableName' => $tableName,
  'ProjectionExpression' => 'firstName, lastName, birthDate, #loc, friends , subjects, email,gender, phone,description',
  'KeyConditionExpression' => 'username = :username',
  'ExpressionAttributeNames' => ['#loc' => 'location'],
  'ExpressionAttributeValues' => $eav
];


try {
  $result = $dynamodb->query($params);
  foreach ($result['Items'] as $i) {
    $user = $marshaler->unmarshalItem($i);
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

// $key = $marshaler->marshalJson('
//     {
//         "username": ' . $_SESSION['username'] . '
//     }
// ');

// $eav = array(
//   ':s' => array(
//     'First'=> array('S'=>'one')
//   )

// );

// $params = [
//   'TableName' => 'profile',
//   'Key' => $key,
//   'UpdateExpression' => 
// 'set subjects = :s',
//   'ExpressionAttributeValues'=> $eav,
//   'ReturnValues' => 'UPDATED_NEW'
// ];

// $result = $dynamodb->updateItem($params);


?>


<!DOCTYPE html>

<head>
	<link rel="stylesheet" href="css/style.css">
</head>

<html lang="en">

<head>
	<meta charset="utf-8">
	<!--  This file has been downloaded from bootdey.com @bootdey on twitter -->
	<!--  All snippets are MIT license http://bootdey.com/license -->
	<title><?php echo $_SESSION['viewUser']?>Profile</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
<div class="container">
<div class="row gutters">
<div class="col-xl-3 col-lg-3 col-md-12 col-sm-12 col-12">
<div class="card h-100">
	<div class="card-body">
		<div class="account-settings">
			<div class="user-profile">
				<div class="user-avatar">
					<img src="https://bootdey.com/img/Content/avatar/avatar7.png" alt="Maxwell Admin">
				</div>
				<h5 class="user-name"><?php echo $_SESSION['viewUser']?></h5>
				<h6 class="user-email"><?php echo $user['email']?></h6>
			</div>
			<div class="about">
				<h5>About</h5>
				<p><?php echo $user['description']?></p>
			</div>
		</div>
	</div>
</div>
</div>
<div class="col-xl-9 col-lg-9 col-md-12 col-sm-12 col-12">
<div class="card h-100">
	<div class="card-body">
		<div class="row gutters">
			<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
				<h6 class="mb-2 text-success">Personal Details</h6>
			</div>
			<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
				<div class="form-group">
					<label for="fullName">First Name</label>
					<p class="form-control"><?php echo $user['firstName'] ?></p>
				</div>
			</div>
			<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
				<div class="form-group">
				<label for="fullName">Last Name</label>
					<p class="form-control"><?php echo $user['lastName'] ?></p>
				</div>
			</div>
			<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
				<div class="form-group">
				<label for="fullName">Gender</label>
					<p class="form-control"><?php echo $user['gender'] ?></p>
				</div>
			</div>
			<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
				<div class="form-group">
				<label for="fullName">Location</label>
					<p class="form-control"><?php echo $user['location'] ?></p>
				</div>
			</div>


			<!-- <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
				<div class="form-group">
				<label for="fullName">Birthday</label>
					<p class="form-control"><?php echo $user['birthDate'] ?></p>
				</div>
			</div> -->

			<!-- <div class="row gutters"> -->
							<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
								<h6 class="mt-3 mb-2 text-success">Subjects</h6>
							<!-- </div> -->
							<!-- <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12"> -->
								<div class="form-group">
									<?php
									$scan_response = $dynamodb->scan(array(
										'TableName' => 'subjects'
									));

									foreach ($scan_response['Items'] as $i) {
										$subject = $marshaler->unmarshalItem($i);
										if ($_SESSION['viewUser'] == $subject['username']) {

									?>

											<!-- <div class="row gutters"> -->
												<!-- <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12"> -->
							
														<p><i class="fa-fw w3-margin-right w3-text-theme"></i><?php echo $subject['subject'] ?>
														
													</p>
													<!-- </div> -->
													<!-- </div> -->
													<?php }}?>
			

		
		</div>

		<?php

$friends = $dynamodb->scan(array(
	'TableName' => 'friends'
  ));


foreach ($friends['Items'] as $j) {
	$friend = $marshaler->unmarshalItem($j);
	if (($friend['username1'] == $_SESSION['username'] && $friend['username2'] == $user['username']) &&  $friend['status'] == "friends"
	|| ($friend['username2'] == $_SESSION['username'] && $friend['username1'] == $user['username'] &&  $friend['status'] == "friends")) {


?>
		
		<div class="row gutters">
		<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
				<h6 class="mb-2 text-success">Contact Details</h6>
			</div>
			<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
				<div class="form-group">
				<label for="fullName">Phone</label>
					<p class="form-control"><?php echo $user['phone'] ?></p>
				</div>
			</div>
			<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
				<div class="form-group">
				<label for="fullName">Email</label>
					<p class="form-control"><?php echo $user['email'] ?></p>
				</div>
			</div>
			</div>

			<?php } 

if (($friend['username1'] == $_SESSION['username'] && $friend['username2'] == $user['username']) &&  $friend['status'] == "request") {
	?>
<div class="row gutters">
			<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
				<div class="text-right">
					<button type="button" id="submit" name="submit" class="btn btn-secondary">Unrequest</button>
				</div>
			</div>
		</div>
		<?php
}
	
			else{?>
		
		<div class="row gutters">
			<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
				<div class="text-right">
					<button type="button" id="submit" name="submit" class="btn btn-secondary">Request</button>
				</div>
			</div>
		</div>

		<?php }}?>
	</div>
</div>
</div>
</div>
</div>