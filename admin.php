<?php
session_start();
if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: main.php');
    exit();
}

if (isset($_POST['refresh'])) {
    header("Refresh:0");
    exit();
}
if (isset($_POST['athletes'])) {
    header('Location: athletes.php');
}
if (isset($_POST['competitions'])) {
    header('Location: competitions.php');
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
    <div class="container d-flex flex-column align-items-center">
        <h1 class="display-3" style="text-align: center; margin: 30px; font-weight: bold;">Sports competitions</h1>
        <form method="POST">
            <div class="btn-group" role="group" aria-label="Basic example">
                <button name="refresh" class="btn btn-primary">Refresh</button>
                <button name="logout" class="btn btn-primary">Logout</button>
            </div>
        </form>
        <div class="container-fluid" style="text-align: center; margin:auto">
            <div class="row">
                <div class="col-4">
                    <span class="material-symbols-outlined" style="font-size:200px;">sprint</span>
                </div>
                <div class="col-4">
                    <span class="material-symbols-outlined" style="font-size:200px;">emoji_events</span>
                </div>
                <div class="col-4">
                    <span class="material-symbols-outlined" style="font-size:200px;">scoreboard</span>
                </div>
            </div>
            <form method="POST">
                <div class="row">
                    <div class="col-4">
                        <button name="athletes" class="btn btn-primary btn-lg">Athletes</button>
                    </div>
                    <div class="col-4">
                        <button name="competitions" class="btn btn-primary btn-lg">Competitions</button>
                    </div>
                    <div class="col-4">
                        <button type="button" class="btn btn-primary btn-lg">Results</button>
                    </div>
                </div>
            </form>
            <div class="w-100 mt-3">
                <h2 class="text-center mb-3"></h2>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>