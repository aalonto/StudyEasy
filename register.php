<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

<script src="https://cdn.jsdelivr.net/jquery.validation/1.15.1/jquery.validate.min.js"></script>
<link href="https://fonts.googleapis.com/css?family=Kaushan+Script" rel="stylesheet">
      <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">


      <html>
<body>
<?php

// require __DIR__ . '/vendor/autoload.php';

// # Imports the Google Cloud client library
// use Google\Cloud\Datastore\DatastoreClient;

// # Your Google Cloud Platform project ID
// $projectId = 's3781183-cc2021';

// # Instantiates a client
// $datastore = new DatastoreClient([
//     'projectId' => $projectId
// ]);


// $username = $_POST['username'];
// $id= $_POST['id'];
// $password = $_POST['password'];

// $query1 = $datastore->query()
//        ->kind('user');

// $result1 = $datastore->runQuery($query1);


// $query = $datastore->query()
// ->kind('user')
// ->filter('username', '=', $username);


// $result = $datastore->runQuery($query);

// $userExist = false;
// $idExist = false;


if(isset($username) && isset($password) && isset($id)){
   if(empty($username) || empty($password)  || empty($id)){
      echo "<h3>All fields required</h3>";}

   // else{

   //    foreach ($result1 as $properties => $users){
   //       if($users->key()->pathEndIdentifier()==$id){
   //          $idExist = true;
   //          echo "The ID already exists"."<br>";
   //       }

   //       if ( $username == $users['username']) {
   //          $userExist = true;
   //          echo "The username already exists";
   //       }

      // foreach ($result as $properties => $users) {

     
  

   //    if(!($userExist&& $idExist)){
   //       $kind = 'user';
         
   //       # The Cloud Datastore key for the new entity
   //       $userKey = $datastore->key($kind, $id);

   //       # Prepares the new entity
   //       $user= $datastore->entity($userKey, ['username' => $username,'password' => $password]);
   //       $datastore->upsert($user);

   //       echo '<script language=javascript>window.location.href="/"</script>';
   //       exit();
   // }
}
// }}
// }


?>


                        <div class="container">

<form class="well form-horizontal" action="" method="post" ame="registration">
<fieldset>

<!-- Form Name -->
<h2><b>Registration Form</b></h2><br>

<!-- Text input-->

<div class="form-group">
<label class="col-md-4 control-label">First Name</label>  
<div class="col-md-4 inputGroupContainer">
<div class="input-group">
<span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
<input  name="first_name" placeholder="First Name" class="form-control"  type="text">
</div>
</div>
</div>

<!-- Text input-->

<div class="form-group">
<label class="col-md-4 control-label" >Last Name</label> 
<div class="col-md-4 inputGroupContainer">
<div class="input-group">
<span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
<input name="last_name" placeholder="Last Name" class="form-control"  type="text">
</div>
</div>
</div>



<!-- Text input-->

<div class="form-group">
<label class="col-md-4 control-label">Username</label>  
<div class="col-md-4 inputGroupContainer">
<div class="input-group">
<span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
<input  name="user_name" placeholder="Username" class="form-control"  type="text">
</div>
</div>
</div>

<!-- Text input-->

<div class="form-group">
<label class="col-md-4 control-label" >Password</label> 
<div class="col-md-4 inputGroupContainer">
<div class="input-group">
<span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
<input name="user_password" placeholder="Password" class="form-control"  type="password">
</div>
</div>
</div>

<!-- Text input-->

<div class="form-group">
<label class="col-md-4 control-label" >Confirm Password</label> 
<div class="col-md-4 inputGroupContainer">
<div class="input-group">
<span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
<input name="confirm_password" placeholder="Confirm Password" class="form-control"  type="password">
</div>
</div>
</div>

<!-- Text input-->
   <div class="form-group">
<label class="col-md-4 control-label">Email</label>  
<div class="col-md-4 inputGroupContainer">
<div class="input-group">
    <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
<input name="email" placeholder="E-Mail Address" class="form-control"  type="text">
</div>
</div>
</div>


<!-- Text input-->
   
<div class="form-group">
<label class="col-md-4 control-label">Birthday</label>  
<div class="col-md-4 inputGroupContainer">
<div class="input-group">
    <span class="input-group-addon"><i class="glyphicon glyphicon-earphone"></i></span>
<input name="birthday" placeholder="Birthday" class="form-control" type="date">
</div>
</div>
</div>

<!-- Select Basic -->
<div class="col-md-12 ">
                              <div class="form-group">
                                 <p class="col-md-4 control-label"><a href="/" id="signin">Already have an account?</a></p>
                              </div>
<!-- Button -->
<div class="form-group">
<label class="col-md-4 control-label"></label>
<div class="col-md-4"><br>
&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp<button type="submit" class="btn btn-warning" >&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbspSUBMIT <span class="glyphicon glyphicon-send"></span>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp</button>
</div>
</div>

</fieldset>
</form>
</div>
</div><!-- /.container -->
</body>
</html>
