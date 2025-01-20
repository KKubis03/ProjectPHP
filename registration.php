<?php
$user = [];
// Function to check if user with given login already exists
$file = fopen("users.csv", "a");
function UserExists($login)
{
    $file = fopen("users.csv", "r");
    while ($row = fgetcsv($file, 0, ";")) {
        if ($row[0] == $login) {
            return true;
        }
    }
}
function AddUser($file, $user)
{
    flock($file, LOCK_EX);
    fputcsv($file, $user, ";");
    flock($file, LOCK_UN);

}
$error_message = '';
if (isset($_POST['login']) && !empty($_POST['login'])) {
    $login = $_POST['login'];
    if (UserExists($login)) {
        $error_message .= 'User with "' . $login . '" login already exists';
    } else if (isset($_POST['password']) && !empty($_POST['password'])) {
        $password = $_POST['password'];
        $Hashedpassword = password_hash($password, PASSWORD_DEFAULT);
        array_push($user, $login, $Hashedpassword, "client");
        AddUser($file, $user);
    } else
        $error_message = "Password cannot be empty";
} else {
    $error_message = 'Login cannot be empty';
}
if (isset($_POST['create']) && empty($error_message)) {
    header("Location: main.php");
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
        <h1 class="display-3 text-center fw-bold mb-4">Welcome to Sports competitions</h1>
        <h1 class="display-3 text-center fw-bold mb-4">Create Account</h1>
        <form method="POST" class="text-center">
            <div class="mb-3">
                <label class="form-label">Login</label>
                <input class="form-control" name="login" style="width: 300px; margin: 0 auto;">
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" class="form-control" name="password" style="width: 300px; margin: 0 auto;">
            </div>
            <h1 class="h4 primary text-center fw-bold mb-4 text-warning"><?= $error_message ?></h1>
            <div class="mb-3">
                <button name="create" class="btn btn-outline-primary">Create Account</button>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>