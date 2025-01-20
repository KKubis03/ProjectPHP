<?php
session_start();

// database connection
$connection = mysqli_connect('localhost', 'root', '', 'sportCompetitions');
$_SESSION['compId'] = $compId = $_POST['compId'] ?? '';
// function to check if field isset and is not empty
function IsNotEmpty($item)
{
    return !empty($item) ? true : false;
}
// Function to check if all fields of form are valid
function IsFormValid($competition)
{
    if (
        IsNotEmpty($competition[0]) &&
        IsNotEmpty($competition[1]) && is_numeric($competition[1]) && // distance must be a number
        IsNotEmpty($competition[2]) &&
        IsNotEmpty($competition[3]) &&
        IsNotEmpty($competition[4])
    )
        return true;
    else
        return false;


}
// function that returns table of fields ready to save in database
function GetData()
{
    $competition = [];
    array_push($competition, $_POST['name'], $_POST['distance'], $_POST['date'], $_POST['country'], $_POST['city']);
    return $competition;
}
function Refresh()
{
    header("Refresh:0");
    exit();
}
function FillTable($competitions)
{
    foreach ($competitions as $comp) {
        echo "<tr>";
        echo "<td>" . $comp['Id'] . "</td>";
        echo "<td>" . $comp['Name'] . "</td>";
        echo "<td>" . $comp['Distance'] . "</td>";
        echo "<td>" . $comp['Date'] . "</td>";
        echo "<td>" . $comp['Country'] . "</td>";
        echo "<td>" . $comp['City'] . "</td>";
        echo '<td> <div class="btn-group"><button class="btn btn-outline-primary btn-sm" name="edit' . $comp['Id']
            . '"><span class="material-symbols-outlined" style="font-size:20px;">edit</span></button>'
            . '<button class="btn btn-outline-danger btn-sm" name="delete' . $comp['Id']
            . '"><span class="material-symbols-outlined" style="font-size:20px;">delete</span></button></div>' . '</td>'
        ;
        echo "</tr>";
    }
}
function Save($compId, $connection)
{
    $compExists = mysqli_query($connection, "select * from competitions where Id = '" . $compId . "';");
    $competition = GetData();
    if (IsFormValid($competition)) {
        if (mysqli_num_rows($compExists) == 0) {
            Insert($connection, $competition);
        } else {
            Edit($compId, $connection, $competition);
        }
    } else
        echo "Invalid data cannot save to database";
}
function Insert($connection, $competition)
{
    $query = "insert into competitions (Name, Distance, Date, Country, City, IsActive) values (
        '" . $competition[0] . "', 
        '" . $competition[1] . "', 
        '" . $competition[2] . "', 
        '" . $competition[3] . "', 
        '" . $competition[4] . "',
        '" . true . "'
        );";
    mysqli_query($connection, $query) or exit("Query $query failed");
}
function Edit($id, $connection, $competition)
{
    $query = "update competitions set 
            Name = '" . $competition[0] . "', 
            Distance = '" . $competition[1] . "', 
            Date = '" . $competition[2] . "', 
            Country = '" . $competition[3] . "', 
            City = '" . $competition[4] . "' 
            where Id = '" . $id . "';";
    mysqli_query($connection, $query) or exit("Query $query failed");
}
function Remove($compId, $connection)
{
    // instead of removing record from database im setting IsActive value = false
    $query = "update competitions set IsActive = false where Id = '$compId'";
    mysqli_query($connection, $query) or exit("failed");
    Refresh();
}
// query to get competitions
$query = "select * from competitions where IsActive = true;";
$result = mysqli_query($connection, $query);
$competitions = mysqli_fetch_all($result, MYSQLI_ASSOC);
$comp = [
    'Name' => $_POST['name'] ?? '',
    'Distance' => $_POST['distance'] ?? '',
    'Date' => $_POST['date'] ?? '',
    'Country' => $_POST['country'] ?? '',
    'City' => $_POST['city'] ?? ''
];
// BUTTONS HANDLING
if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: main.php');
    exit();
}
if (isset($_POST['refresh'])) {
    Refresh();
}
if (isset($_POST['back'])) {
    header('Location: admin.php');
    exit();
}
if (isset($_POST['save'])) {
    Save($_POST['compId'], $connection);
    Refresh();
}
if (isset($_POST['cancel'])) {
    Refresh();
}
// EDIT buttons handle
foreach ($competitions as $item) {
    $i = $item['Id'];
    if (isset($_POST["edit$i"])) {
        $comp['Id'] = $i;
        $compId = $comp['Id'];
        $comp['Name'] = $item['Name'];
        $comp['Distance'] = $item['Distance'];
        $comp['Date'] = $item['Date'];
        $comp['Country'] = $item['Country'];
        $comp['City'] = $item['City'];
        break;
    }
}
// REMOVE buttons handle
foreach ($competitions as $c) {
    $i = $c['Id'];
    if (isset($_POST["delete$i"])) {
        $comp['Id'] = $i;
        $competitionId = $comp['Id'];
        Remove($competitionId, $connection);
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
                <button name="back" class="btn btn-primary">Back</button>
                <button name="refresh" class="btn btn-primary">Refresh</button>
                <button name="logout" class="btn btn-primary">Logout</button>
            </div>
        </form>
        <!-- ADD -->
        <div class="w-100 mt-3 d-flex flex-column align-items-center">
            <form method="POST">
                <div class="row">
                    <div class="col mb-3">
                        <input type="hidden" name="compId" value="<?= $compId ?>">
                        <!-- hidden form to store Id -->
                        <label class="form-label">Name:</label>
                        <input type="text" class="form-control" name="name" value="<?= $comp['Name'] ?>">
                    </div>
                    <div class="col mb-3">
                        <label class="form-label">Distance:</label>
                        <input type="text" class="form-control" name="distance" value="<?= $comp['Distance'] ?>">
                    </div>
                    <div class="col mb-3">
                        <label class="form-label">Date:</label><br>
                        <input type="date" name="date" class="form-control" value="<?= $comp['Date'] ?>">
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label class="form-label">Country:</label>
                        <input type="text" class="form-control" name="country" value="<?= $comp['Country'] ?>">
                    </div>
                    <div class="col mb-3">
                        <label class="form-label">City:</label>
                        <input type="text" class="form-control" name="city" value="<?= $comp['City'] ?>">
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
            <h2 class="text-center mb-3">Competitions</h2>
            <form method="POST">
                <table class="table table-bordered text-center">
                    <thead class="table-dark">
                        <tr>
                            <th scope="col">Id</th>
                            <th scope="col">Name</th>
                            <th scope="col">Distance</th>
                            <th scope="col">Date</th>
                            <th scope="col">Country</th>
                            <th scope="col">City</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Filling table with records -->
                        <?php FillTable($competitions); ?>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
    </div>
    <!-- Bootstrap scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>