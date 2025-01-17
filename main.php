<?php
// Start session for user authentication
session_start();
$server = "localhost";
$user = "root";
$password = "";
$database = 'sportCompetitions';
$connection = mysqli_connect($server, $user, $password, $database);
$error_message = '';
if (isset($_POST['login']) && isset($_POST['password'])) {
  // database connection
  $loginOK = false;
  $login = $_POST['login'];
  $password = $_POST['password'];
  $query = "select * from users where Login = '$login'";
  $result = mysqli_query($connection, $query);
  global $user;
  $user = mysqli_fetch_assoc($result);
  if ($user != null) {
    $_SESSION['userId'] = $user['Id'];
    if ($user['Login'] == $login && $user['Password'] == $password) {
      $loginOK = true;
    }
  }
  if ($loginOK) {
    if ($user['UserType'] == "admin")
      header("Location: admin.php");
    else
      header("Location: client.php");
    exit;
  } else {
    $error_message = "Login or password is incorrect.";
  }
}


?>
<!doctype html>
<html lang="en">

<head>
  <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>KKubis PHP_Project</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>

<body>
  <div class="container d-flex flex-column justify-content-top align-items-center vh-100">
    <h1 class="display-3 text-center fw-bold mb-4">Sports competitions</h1>
    <h1 class="display-3 text-center fw-bold mb-4">Log in</h1>
    <form method="POST" class="text-center">
      <div class="mb-3">
        <label class="form-label">Login</label>
        <input class="form-control" required name="login" style="width: 300px; margin: 0 auto;">
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" required class="form-control" name="password" style="width: 300px; margin: 0 auto;">
      </div>
      <h1 class="h4 primary text-center fw-bold mb-4 text-warning"><?= $error_message ?></h1>
      <div class="mb-3">
        <button type="submit" class="btn btn-primary">Log in</button>
      </div>
    </form>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
</body>

</html>