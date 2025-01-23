<?php
// Start session for user authentication
session_start();
// database connection
$connection = mysqli_connect('localhost', 'root', '', 'sportCompetitions');
$file = fopen('users.csv', 'r');
// Function to find user in csv File
function FindUser($login, $file)
{
  while ($row = fgetcsv($file, 0, ";")) {
    if ($row[0] == $login) {
      return $row;
    }
  }
}
$_SESSION['error'] = $_SESSION['error'] ?? '';
if (isset($_POST['login']) && isset($_POST['password'])) {
  $loginOK = false;
  $login = $_POST['login'];
  $password = $_POST['password'];
  $user = FindUser($login, $file);
  if ($user != null) {
    if ($user[0] == $login && password_verify($password, $user[1])) {
      $loginOK = true;
    }
  }
  if ($loginOK) {
    $_SESSION['error'] = '';
    if ($user[2] == "admin")
      header("Location: admin.php");
    else
      header("Location: client.php");
    exit;
  } else {
    $_SESSION['error'] = "Login or password is incorrect.";
  }
}
if (isset($_POST['register'])) {
  $_SESSION['error'] = '';
  header("Location: registration.php");
  exit;
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
    <h1 class="display-3 text-center fw-bold mb-4" style="text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);">Sports
      competitions</h1>
    <h1 class="display-3 text-center fw-bold mb-4" style="text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);">Log in</h1>
    <form method="POST" class="text-center">
      <div class="mb-3">
        <label class="form-label">Login</label>
        <input class="form-control" name="login" style="width: 300px; margin: 0 auto;">
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" class="form-control" name="password" style="width: 300px; margin: 0 auto;">
      </div>
      <h1 class="h4 primary text-center fw-bold mb-4 text-warning"><?= $_SESSION['error'] ?></h1>
      <div class="mb-3">
        <button type="submit" class="btn btn-primary">Log in</button>
        <button name="register" class="btn btn-outline-secondary">Register</button>
      </div>
    </form>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
    crossorigin="anonymous"></script>
</body>

</html>