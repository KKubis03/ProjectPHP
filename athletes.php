<?php
session_start();
// database connection
$server = "localhost";
$user = "root";
$password = "";
$database = 'sportCompetitions';
$connection = mysqli_connect($server, $user, $password, $database);

// FUNCTIONS
// Refresh function 
function refresh()
{
    header("Refresh:0");
    exit();
}
// function to fill the table
function FillTable($athletes)
{
    foreach ($athletes as $athlete) {
        echo "<tr>";
        echo "<td>" . $athlete['Id'] . "</td>";
        echo "<td>" . $athlete['FirstName'] . "</td>";
        echo "<td>" . $athlete['LastName'] . "</td>";
        echo "<td>" . $athlete['Sex'] . "</td>";
        echo "<td>" . $athlete['Country'] . "</td>";
        echo "<td>" . $athlete['City'] . "</td>";
        echo '<td> <div class="btn-group"><button class="btn btn-outline-primary btn-sm" name="edit' . $athlete['Id']
            . '"><span class="material-symbols-outlined" style="font-size:20px;">edit</span></button>'
            . '<button class="btn btn-outline-danger btn-sm" name="delete' . $athlete['Id']
            . '"><span class="material-symbols-outlined" style="font-size:20px;">delete</span></button></div>' . '</td>'
        ;
        echo "</tr>";
    }
}
// Save function
function Save($athleteId, $connection)
{
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $gender = $_POST['sex'];
    $country = $_POST['country'];
    $city = $_POST['city'];
    $query = "select * from athletes where Id = '" . $athleteId . "';";
    $athleteExists = mysqli_query($connection, $query);
    if (mysqli_num_rows($athleteExists) == 0) {
        $query = "insert into athletes (FirstName, Lastname, Country, Sex, City) values (
            '" . $name . "', 
            '" . $surname . "', 
            '" . $country . "', 
            '" . $gender . "', 
            '" . $city . "'
            );";
    } else {
        $query = "update athletes set 
            FirstName = '" . $name . "', 
            LastName = '" . $surname . "', 
            Country = '" . $country . "', 
            Sex = '" . $gender . "', 
            City = '" . $city . "' 
            where Id = '" . $athleteId . "';";
    }
    mysqli_query($connection, $query) or exit("Query $query failed");
}
// Remove function
function remove($athleteId, $connection)
{
    $query = "delete from athletes where Id = '$athleteId'";
    echo $query;
    mysqli_query($connection, $query) or exit("failed");
    refresh();
}
// query to get athletes
$query = "select * from athletes";
$result = mysqli_query($connection, $query);
$athletes = mysqli_fetch_all($result, MYSQLI_ASSOC);

// BUTTONS HANDLING
// logout button
if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: main.php');
    exit();
}
// refresh button handling
if (isset($_POST['refresh'])) {
    refresh();
}
// Save button handling
if (isset($_POST['save'])) {
    Save($athleteId, $connection);
    refresh();
}
// Cancel button handling
if (isset($_POST['cancel'])) {
    refresh();
}
$athlete = [
    'Name' => $_POST['name'] ?? '',
    'Surname' => $_POST['surname'] ?? '',
    'Gender' => $_POST['gender'] ?? '',
    'Country' => $_POST['country'] ?? '',
    'City' => $_POST['city'] ?? ''
];
// edit buttons handle
$athleteId = $_POST['athleteid'] ?? 0;
foreach ($athletes as $item) {
    $i = $item['Id'];
    if (isset($_POST["edit$i"])) {
        $athlete['Id'] = $item['Id'];
        $athleteId = $athlete['Id'];
        $athlete['Name'] = $item['FirstName'];
        $athlete['Surname'] = $item['LastName'];
        $athlete['Gender'] = $item['Sex'];
        $athlete['Country'] = $item['Country'];
        $athlete['City'] = $item['City'];
        break;
    }
}
foreach ($athletes as $a) {
    $i = $a['Id'];
    if (isset($_POST["delete$i"])) {
        $athlete['Id'] = $a['Id'];
        $athleteId = $athlete['Id'];
        remove($athleteId, $connection);
        break;
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
    <div class="container d-flex flex-column align-items-center">
        <h1 class="display-3" style="text-align: center; margin: 30px; font-weight: bold;">Sports competitions</h1>
        <form method="POST">
            <div class="btn-group" role="group" aria-label="Basic example">
                <button name="refresh" class="btn btn-primary">Refresh</button>
                <button name="logout" class="btn btn-primary">Logout</button>
            </div>
        </form>
        <!-- ADD -->
        <div class="w-100 mt-3 d-flex flex-column align-items-center">
            <form method="POST">
                <div class="row">
                    <div class="col mb-3">
                        <input type="hidden" name="athleteid" value="<?= $athleteId ?>">
                        <!-- hidden form to store Id -->
                        <label class="form-label">Name:</label>
                        <input type="text" class="form-control" name="name" required value="<?= $athlete['Name'] ?>">
                    </div>
                    <div class="col mb-3">
                        <label class="form-label">Surname:</label>
                        <input type="text" class="form-control" name="surname" required
                            value="<?= $athlete['Surname'] ?>">
                    </div>
                    <div class="col mb-3">
                        <label class="form-label mb-3">Gender:</label><br>
                        <input type="radio" name="sex" class="mx-2" required value="Male" <?= $athlete['Gender'] == 'Male' ? 'checked' : '' ?>>Male
                        <input type="radio" name="sex" class="mx-2" value="Female" <?= $athlete['Gender'] == 'Female' ? 'checked' : '' ?>>Female
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label class="form-label">Country:</label>
                        <input type="text" class="form-control" name="country" required
                            value="<?= $athlete['Country'] ?>">
                    </div>
                    <div class="col mb-3">
                        <label class="form-label">City:</label>
                        <input type="text" class="form-control" name="city" required value="<?= $athlete['City'] ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="btn-group">
                        <button type="submit" name="save" class="btn btn-outline-primary">Save</button>
                        <button name="cancel" class="btn btn-outline-danger">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
        <!-- Athletes table -->
        <div class="w-100 mt-3">
            <h2 class="text-center mb-3">Athletes</h2>
            <form method="POST">
                <table class="table table-bordered text-center">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Name</th>
                            <th scope="col">Surname</th>
                            <th scope="col">Gender</th>
                            <th scope="col">Country</th>
                            <th scope="col">City</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Filling table with records -->
                        <?php FillTable($athletes); ?>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>