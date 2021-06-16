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
use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

$bucketName = 's3778713-a2-s3';

// Connect to AWS
$s3 = new S3Client([
	'credentials' => [
		'key' => 'AKIA3QI3KNZZCV7JMUVJ',
		'secret' => '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'
	],
	'version' => 'latest',
	'region'  => 'us-east-1'
]);

$sdk = new Aws\Sdk([
	'credentials' => [
        'key'    => 'AKIA4WTDCA2IYDFWFGRE',
        'secret' => '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',
    ],
	'region'   => 'us-east-1',
	'version'  => 'latest'
]);

$dynamodb = $sdk->createDynamoDb();
$marshaler = new Marshaler();


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
// dynamodb table update (NEW ATTRIBUTE)


?>

<?php
if (isset($_POST['update'])) {
	if (!(empty($_POST['firstName']) || empty($_POST['lastName'])  || empty($_POST['location']) || empty($_POST['birthDate']) || empty($_POST['gender']) || empty($_POST['phone']) || empty($_POST['email']))) {

		$tableName = 'profile';
		$eav = $marshaler->marshalJson('
                                {
                                    ":fN": "' . $_POST['firstName'] . '" ,
                                    ":lN": "' . $_POST['lastName'] . '" ,
                                    ":gender": "' . $_POST['gender'] . '" ,
                                    ":birth": "' . $_POST['birthDate'] . '",
                                    ":phone": "' . $_POST['phone'] . '",
                                    ":email": "' . $_POST['email'] . '" ,
                                    ":loc": "' . $_POST['location'] . '", 
                                    ":desc": "' . $_POST['desc'] . '" 
                                }
                            ');

		$params = [
			'TableName' => $tableName,
			'Key' => array(
				'username'      => array('S' => '' . $_SESSION['username'] . '')
			),
			'UpdateExpression' =>
			'set firstName = :fN, lastName = :lN, gender = :gender,
                                     birthDate = :birth,phone = :phone, email = :email , #loc=:loc, description=:desc',
			'ExpressionAttributeNames' => ['#loc' => 'location'],
			'ExpressionAttributeValues' => $eav,
			'ReturnValues' => 'UPDATED_NEW'
		];
		$result = $dynamodb->updateItem($params);


		$eav = $marshaler->marshalJson('
      {
        ":username": "' . $_SESSION['username'] . '"
      }
  ');

		$params = [
			'TableName' => $tableName,
			'ProjectionExpression' => 'firstName, lastName, birthDate, #loc, subjects, email,gender, phone, description',
			'KeyConditionExpression' => 'username = :username',
			'ExpressionAttributeNames' => ['#loc' => 'location'],
			'ExpressionAttributeValues' => $eav
		];


		try {
			$result = $dynamodb->query($params);
			foreach ($result['Items'] as $i) {
				$user = $marshaler->unmarshalItem($i);
				$_SESSION['firstName'] = $user['firstName'];
				$_SESSION['lastName'] = $user['lastName'];
				$_SESSION['gender'] = $user['gender'];
				$_SESSION['email'] = $user['email'];
				$_SESSION['phone'] = $user['phone'];
				$_SESSION['birthDate'] = $user['birthDate'];
				$_SESSION['location'] = $user['location'];
				$_SESSION['description'] = $user['description'];
			}
		} catch (DynamoDbException $e) {
			echo "Unable to query:\n";
			echo $e->getMessage() . "\n";
		}
	}
}
if (isset($_POST['update2'])) {

	$tableName = 'profile';
	$eav = $marshaler->marshalJson('
							{

								":gender": "' . $_POST['genderPref'] . '" ,
								":loc": "' . $_POST['locPref'] . '", 
								":subject": "' . $_POST['subPref'] . '" 
							}
						');

	$params = [
		'TableName' => 'preferences',
		'Key' => array(
			'username'      => array('S' => '' . $_SESSION['username'] . '')
		),
		'UpdateExpression' =>
		'set  gender = :gender,subject = :subject, #loc=:loc',
		'ExpressionAttributeNames' => ['#loc' => 'location'],
		'ExpressionAttributeValues' => $eav,
		'ReturnValues' => 'UPDATED_NEW'
	];
	$result = $dynamodb->updateItem($params);

}

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
									<?php if (!empty($_SESSION['image'])) {
										echo '<img src="https://studyeasy.s3.us-east-1.amazonaws.com/' . $_SESSION['image'] . '">';
									} else {
										echo '<img src="https://studyeasy.s3.us-east-1.amazonaws.com/blank.png">';
									} ?>
								</div>
								<?php
								if (isset($_FILES['img'])) {
									$split = explode(".", $_FILES['img']['name']);
									$image = $_SESSION['username'] .".". $split[1];
									try {
										$s3->putObject([
											'Bucket' => 'studyeasy',
											'Key' =>  $image,
											'SourceFile' => $_FILES['img']['tmp_name'],
											'ACL'    => 'public-read'
										]);
										// header("Location: editProfile.php");
										// exit();
									} catch (S3Exception $e) {
										echo "There was an error uploading the file.\n";
									}

									$eav = $marshaler->marshalJson('{":img": "'.$image.'"}');

									$dynamodb->updateItem([
										'TableName' => 'profile',
										'Key' => array('username' => array('S' => $_SESSION['username'])),
										'UpdateExpression' => 'set image = :img',
										'ExpressionAttributeValues'=> $eav,
										'ReturnValues' => 'UPDATED_NEW'
									]);
									$_SESSION['image'] = $image;
								}
								?>
								<form action="<?= $_SERVER['PHP_SELF']; ?>" method="post" enctype="multipart/form-data">
									<input type="file" name="img" accept="image/*">
									<input type="submit" name="upload" value="Upload Profile Picture">
								</form>

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
										<input type="text" class="form-control" name="firstName" id="firstName" value=<?php echo $_SESSION['firstName'] ?> required>
									</div>
								</div>
								<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
									<div class="form-group">
										<label for="lastName">Last Name</label>
										<input type="text" class="form-control" name="lastName" id="lastName" value=<?php echo $_SESSION['lastName'] ?> required>
									</div>
								</div>
								<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
									<div class="form-group">
										<label for="phone">Phone</label>
										<input type="tel" pattern="[0-9]{10}" class="form-control" name="phone" id="phone" value=<?php echo $_SESSION['phone'] ?> required>
									</div>
								</div>
								<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
									<div class="form-group">
										<label for="email">Email</label>
										<input type="email" class="form-control" name="email" id="email" value=<?php echo $_SESSION['email'] ?> required>
									</div>
								</div>

								<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
									<div class="form-group">
										<label for="birthday">Birthday</label>
										<input type="date" class="form-control" name="birthDate" id="birthDate" value=<?php echo $_SESSION['birthDate'] ?> required>
									</div>
								</div>

								<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
									<div class="form-group">
										<label for="gender">Gender</label>
										<input type="text" class="form-control" name="gender" id="gender" value=<?php echo $_SESSION['gender'] ?> required>
									</div>
								</div>

								<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
									<div class="form-group">
										<label for="location">Location</label>
										<select class="form-control" name="location" id="location" value=<?php echo $_SESSION['location'] ?> required>

											<?php
											include 'countries.php';
											?>

										</select>
									</div>
								</div>


								<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
									<div class="form-group">
										<label for="desc">Description</label>
										<input type="text" class="form-control" name="desc" id="desc" value=<?php echo $_SESSION['description'] ?>>
									</div>
								</div>
							</div>

							<div class="text-right">
								<input type="submit" id="update" class="w3-button w3-block green-theme w3-left-align  " name="update" value="Update">
							</div>
						</form>
						<form action="" method="post" name="editPref">
						<div class="row gutters">
							<div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
								<h6 class="mt-3 mb-2 text-success">Preferences</h6>
							</div>
							<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
								<div class="form-group">
									<label for="fName">Location</label>
									<input type="text" class="form-control" name="locPref" id="locPref" value=<?php echo $pref['location'] ?>>
								</div>
							</div>

							<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
								<div class="form-group">
									<label for="fName">Subject</label>
									<input type="text" class="form-control" name="subPref" id="subPref" value=<?php echo $pref['subject'] ?>>
								</div>
							</div>

							<div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
								<div class="form-group">
									<label for="fName">Gender</label>
									<input type="text" class="form-control" name="genderPref" id="genderPref" value=<?php echo $pref['gender'] ?>>
								</div>
							</div>
							</div>
							<div class="text-right">
							<input type="submit" id="update2" class="w3-button w3-block green-theme w3-left-align  " name="update2" value="Update">
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