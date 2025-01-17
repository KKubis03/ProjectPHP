<?php
session_start();
// database connection
$server = "localhost";
$user = "root";
$password = "";
$database = 'sportCompetitions';
$connection = mysqli_connect($server, $user, $password, $database);

if (isset($_SESSION['userId'])) {
    $userId = $_SESSION['userId'];
}

// query to get athletes
$query = "select* from athletes where id = '$userId'";
$result = mysqli_query($connection, $query);
$athlete = mysqli_fetch_assoc($result);
$athleteId = $athlete["Id"];

// query to get results
$query2 = "select* from results where athleteId = '$athleteId'";
$result2 = mysqli_query($connection, $query2);
$results = mysqli_fetch_all($result2, MYSQLI_ASSOC); // Fetch all results

// query to get competitions
$competitionIds = array_map(function ($result) {
    return $result["CompetitionId"]; }, $results); // Extract competition IDs from results
$competitionIdsStr = implode(",", $competitionIds); // Create a comma-separated string of competition IDs
$query3 = "select * from competitions where id IN ($competitionIdsStr)";
$result3 = mysqli_query($connection, $query3);
$competitions = mysqli_fetch_all($result3, MYSQLI_ASSOC); // Fetch all competitions

if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: main.php');
    exit();
}

if (isset($_POST['refresh'])) {
    header("Refresh:0");
    exit();
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
    <div class="container d-flex flex-column align-items-center vh-100 py-4">
        <!-- Header -->
        <h1 class="display-3 text-center fw-bold mb-4">Sports Competitions</h1>
        <form method="POST">
            <div class="btn-group" role="group" aria-label="Basic example">
                <button name="refresh" class="btn btn-primary">Refresh</button>
                <button name="logout" class="btn btn-primary">Logout</button>
            </div>
        </form>
        <!-- Personal Details -->
        <div class="w-100 mb-5 align-items-center">
            <h2 class="text-center mb-3">Personal Details</h2>
            <div class="container text-center">
                <p class="fw-bold">Name: <?= $athlete['FirstName'] ?></p>
                <p class="fw-bold">Surname: <?= $athlete['LastName'] ?></p>
                <p class="fw-bold">Gender: <?= $athlete['Sex'] ?></p>
                <p class="fw-bold">Country: <?= $athlete['Country'] ?></p>
                <p class="fw-bold">City: <?= $athlete['City'] ?></p>
            </div>
        </div>
        <!-- Competitions -->
        <div class="w-100">
            <h2 class="text-center mb-3">Competitions</h2>
            <table class="table table-bordered text-center">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Competition Name</th>
                        <th scope="col">Distance</th>
                        <th scope="col">Date</th>
                        <th scope="col">Country</th>
                        <th scope="col">City</th>
                        <th scope="col">Time (h:m:s)</th>
                    </tr>
                </thead>
                <tbody>


                    <?php
                    $row = 0;
                    foreach ($competitions as $competition) {
                        $row++;
                        echo "<tr>";
                        echo "<th scope='row'>$row</th>";
                        echo "<td>" . $competition['Name'] . "</td>";
                        echo "<td>" . $competition['Distance'] . " m</td>";
                        echo "<td>" . $competition['Date'] . "</td>";
                        echo "<td>" . $competition['Country'] . "</td>";
                        echo "<td>" . $competition['City'] . "</td>";
                        $competitionId = $competition['Id'];
                        $time = null;
                        foreach ($results as $result) {
                            if ($result['CompetitionId'] == $competitionId) {
                                $time = $result['Time'];
                                break;
                            }
                        }
                        echo "<td>" . $time . "</td>";
                        echo "</tr>";
                    }
                    ?>


                </tbody>
            </table>
        </div>
        <!-- Progress -->
        <div class="w-100">
            <h2 class="text-center mb-3">Progress bar</h2>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>