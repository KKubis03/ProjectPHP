<?php
session_start();

// database connection
$connection = mysqli_connect('localhost', 'root', '', 'sportCompetitions');
$_SESSION['compId'] = $compId = $_POST['compId'] ?? '';
// Function to check if all fields of form are valid
function IsFormValid($competition)
{
    if (
        !empty($competition['Name']) && !is_numeric($competition['Name']) && // name should not be a number 
        !empty($competition['Distance']) && is_numeric($competition['Distance']) && $competition['Distance'] > 0 && // distance must be a number and > 0
        !empty($competition['Date']) &&
        !empty($competition['Country']) && !is_numeric($competition['Country']) && // country should not be a number 
        !empty($competition['City']) && !is_numeric($competition['City']) // city should not be a number 
    )
        return true;
    else
        return false;


}
// function that returns table of fields ready to save in database
function GetData()
{
    $competition = [
        'Name' => $_POST['name'] ?? '',
        'Distance' => $_POST['distance'] ?? '',
        'Date' => $_POST['date'] ?? '',
        'Country' => $_POST['country'] ?? '',
        'City' => $_POST['city'] ?? ''
    ];
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
        echo "<td>" . $comp['Distance'] . " (m)" . "</td>";
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
        '" . $competition['Name'] . "', 
        '" . $competition['Distance'] . "', 
        '" . $competition['Date'] . "', 
        '" . $competition['Country'] . "', 
        '" . $competition['City'] . "',
        '" . true . "'
        );";
    mysqli_query($connection, $query) or exit("Query $query failed");
}
function Edit($id, $connection, $competition)
{
    $query = "update competitions set 
            Name = '" . $competition['Name'] . "', 
            Distance = '" . $competition['Distance'] . "', 
            Date = '" . $competition['Date'] . "', 
            Country = '" . $competition['Country'] . "', 
            City = '" . $competition['City'] . "' 
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
function SortBy($key, &$table)
{
    switch ($key) {
        case "Name": {
            $Names = array_column($table, 'Name');
            array_multisort($Names, SORT_ASC, $table);
        }
            break;
        case "Distance": {
            $Distances = array_column($table, 'Distance');
            array_multisort($Distances, SORT_ASC, $table);
        }
            break;
        case "Date": {
            $Dates = array_column($table, 'Date');
            array_multisort($Dates, SORT_ASC, $table);
        }
            break;
        case "Country": {
            $Countries = array_column($table, 'Country');
            array_multisort($Countries, SORT_ASC, $table);
        }
            break;
        case "City": {
            $Cities = array_column($table, 'City');
            array_multisort($Cities, SORT_ASC, $table);
        }
            break;
        default: {
            $Ids = array_column($table, 'Id');
            array_multisort($Ids, SORT_ASC, $table);
        }
    }
}
// query to get competitions
$query = "select * from competitions where IsActive = true;";
$result = mysqli_query($connection, $query);
$competitions = mysqli_fetch_all($result, MYSQLI_ASSOC);
$comp = GetData();
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
$currentSort = $_POST['sortby'] ?? ''; // value of sortedBy
if (isset($_POST['sort'])) {
    if (isset($_POST['sortby'])) {
        $currentSort = $_POST['sortby'];
        SortBy($_POST['sortby'], $competitions);
    }
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
                            <th scope="col">
                                <div class="input-group text-align-center">
                                    <select name="sortby" class="form-select form-select-sm" style="width:10px;">
                                        <?php
                                        $keys = array_keys($competitions[0]);
                                        foreach ($keys as $key) {
                                            if ($key != 'IsActive') {
                                                $selected = $currentSort === $key ? 'selected' : '';
                                                echo '<option value="' . $key . '" ' . $selected . '>' . $key . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                    <button type='submit' class="btn  btn-primary btn-sm " name="sort">Sort</button>
                                </div>
                            </th>
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