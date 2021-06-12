<!DOCTYPE html>

<head>
	<link rel="stylesheet" href="css/style.css">
</head>

<html lang="en">

<head>
	<meta charset="utf-8">
	<!--  This file has been downloaded from bootdey.com @bootdey on twitter -->
	<!--  All snippets are MIT license http://bootdey.com/license -->
	<title>Edit Profile</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/css/bootstrap.min.css" rel="stylesheet">
	<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/js/bootstrap.bundle.min.js"></script>
</head>

</html>

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


// dynamodb table update (NEW ATTRIBUTE)


?>




<html>

<body class="w3-theme-l5">
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
								<h5 class="user-name"><?php echo $_SESSION['username'] ?></h5>
								<h6 class="user-email"><?php echo $_SESSION['email'] ?></h6>
							</div>
							<div class="about">
								<h5>About</h5>
								<?php if (!empty($_SESSION['description'])) ?>
								<p><?php echo $_SESSION['description'] ?></p>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-xl-9 col-lg-9 col-md-12 col-sm-12 col-12">
				<div class="card h-100">
					<div class="card-body">
					<form action="" method="post" name="edit">
						<div class="row gutters">
							<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
								<h6 class="mb-2 text-success">Personal Details</h6>
							</div>
							<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
								<div class="form-group">
									<label for="fName">First Name</label>
									<input type="text" class="form-control" name="firstName" id="firstName" value=<?php echo $_SESSION['firstName'] ?>>
								</div>
							</div>
							<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
								<div class="form-group">
									<label for="lastName">Last Name</label>
									<input type="text" class="form-control" name="lastName" id="lastName" value=<?php echo $_SESSION['lastName'] ?>>
								</div>
							</div>
							<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
								<div class="form-group">
									<label for="phone">Phone</label>
									<input type="tel" class="form-control" name="phone" id="phone" value=<?php echo $_SESSION['phone'] ?>>
								</div>
							</div>
							<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
								<div class="form-group">
									<label for="email">Email</label>
									<input type="email" class="form-control" name="email" id="email" value=<?php echo $_SESSION['email'] ?>>
								</div>
							</div>

							<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
								<div class="form-group">
									<label for="birthday">Birthday</label>
									<input type="date" class="form-control" name="birthDate" id="birthDate" value=<?php echo $_SESSION['birthDate'] ?>>
								</div>
							</div>

							<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
								<div class="form-group">
									<label for="gender">Gender</label>
									<input type="date" class="form-control" name="gender" id="gender" value=<?php echo $_SESSION['gender'] ?>>
								</div>
							</div>

							<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
								<div class="form-group">
									<label for="location">Location</label>
									<select class="form-control" name="location" id="location" value=<?php echo $_SESSION['location'] ?>>

										<?php
										include 'countries.php';
										?>

									</select>
								</div>
							</div>
						</div>
						<?php
						if (isset($_POST['update'])) {
							if (empty($_POST['firstName']) || empty($_POST['lastName'])  || empty($_POST['location']) || empty($_POST['birthDate']) || empty($_POST['gender']) || empty($_POST['phone']) || empty($_POST['email'])) {
								$msg = "You left one or more of the required fields.";
								echo $msg;
							} else {
								// $eav = $marshaler->marshalJson('{":fN": "' . $_POST['firstName'] . '"}');

								// 	// dynamodb table update (NEW ATTRIBUTE)
								// 	$params = [
								// 		'TableName' => 'profile',
								// 		'Key' => ''. $_SESSION['username'] .'',
								// 		'UpdateExpression' => 'set firstName = :fN',
								// 		'ExpressionAttributeValues' => $eav,
								// 		'ReturnValues' => 'UPDATED_NEW'
								// 	];

																}
															}
								// 	$result = $dynamodb->updateItem($params);

						?>
						<div class="text-right">
							<input type="submit" id="update" class="w3-button w3-block green-theme w3-left-align  " name="update" value="Update">
						</div>
						</form>

						<?php if (isset($_POST['subjectSub'])) {

							if (isset($_POST['subject']) && !empty($_POST['subject'])) {

								$result = $dynamodb->putItem(array(
									'TableName' => 'subjects',
									'Item' => array(
										'username'      => array('S' => $_SESSION['username']),
										'subject'    => array('S' => $_POST['subject'])
									)
								));
							}
						}
						?>

						<?php

						if (isset($_POST['delete1'])) {

							$result = $dynamodb->deleteItem(array(
								'TableName' => 'subjects',
								'Key' => array(
									'username'      => array('S' => '' . $_SESSION['username'] . ''),
									'subject'      => array('S' => '' . $_POST['delete'] . '')
								)
							));
						}
						?>
						<div class="row gutters">
							<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
								<h6 class="mt-3 mb-2 text-success">Subjects</h6>
							</div>
							<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
								<div class="form-group">
									<?php
									$scan_response = $dynamodb->scan(array(
										'TableName' => 'subjects'
									));

									foreach ($scan_response['Items'] as $i) {
										$subject = $marshaler->unmarshalItem($i);
										if ($_SESSION['username'] == $subject['username']) {

									?>

											<div class="row gutters">
												<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">


													<form action="" method="post" name="deleteSubject">
														<p><i class="fa-fw w3-margin-right w3-text-theme"></i><?php echo $subject['subject'] ?>
															<input type="hidden" id=" delete" class="w3-button w3-block w3-left-align " name="delete" value=<?php echo $subject['subject'] ?>>
															<input type="submit" id=" delete1" class="w3-button w3-block w3-left-align " name="delete1" value="delete">
													</form>
													</p>
													</form>



												</div>
											</div>
									<?php }
									} ?>


									<div class="form-group">
										<div class="forms-wrapper">
											<form action="" method="post" name="subjects">
												<input type="text" id="subject" name="subject" placeholder="add subject">
												<input type="submit" name="subjectSub" class="w3-button w3-block green-theme w3-left-align " value="add">
												<!-- <button type="button" id="subjectSub" name="subjectSub" class="w3-button w3-block green-theme w3-left-align ">Add</button> -->
											</form>


										</div>
									</div>
								</div>
								<!-- <div class="row gutters">
			<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12"> -->

							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>