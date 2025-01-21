<?php
session_start();

// database connection
$connection = mysqli_connect('localhost', 'root', '', 'sportCompetitions');
$_SESSION['athleteId'] = $athleteId = $_POST['athleteid'] ?? '';

// FUNCTIONS
function GetData()
{
    $athlete = [
        'Name' => $_POST['name'] ?? '',
        'Surname' => $_POST['surname'] ?? '',
        'Gender' => $_POST['sex'] ?? '',
        'Country' => $_POST['country'] ?? '',
        'City' => $_POST['city'] ?? ''
    ];
    return $athlete;
}
// Function to check if all fields of form are valid
function IsFormValid($athlete)
{
    if (
        !empty($athlete['Name']) && !is_numeric($athlete['Name']) && // name should not be a number 
        !empty($athlete['Surname']) && !is_numeric($athlete['Surname']) && // surname should not be a number
        !empty($athlete['Gender']) &&
        !empty($athlete['Country']) && !is_numeric($athlete['Country']) && // country should not be a number 
        !empty($athlete['City']) && !is_numeric($athlete['City']) // city should not be a number 
    )
        return true;
    else
        return false;
}
function Refresh()
{
    header("Refresh:0");
    exit();
}
// function to sort table
function SortBy($key, &$table)
{
    switch ($key) {
        case "FirstName": {
            $FNames = array_column($table, 'FirstName');
            array_multisort($FNames, SORT_ASC, $table);
        }
            break;
        case "LastName": {
            $LNames = array_column($table, 'LastName');
            array_multisort($LNames, SORT_ASC, $table);
        }
            break;
        case "Sex": {
            $Sex = array_column($table, 'Sex');
            array_multisort($Sex, SORT_ASC, $table);
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
function Save($athleteId, $connection)
{
    $athleteExists = mysqli_query($connection, "select * from athletes where Id = '" . $athleteId . "';");
    $athlete = GetData();
    if (IsFormValid($athlete)) {
        if (mysqli_num_rows($athleteExists) == 0) {
            Insert($connection, $athlete);
        } else {
            Edit($athleteId, $connection, $athlete);
        }
    } else
        echo "Invalid data cannot save to database";

}
function Insert($connection, $athlete)
{
    $query = "insert into athletes (FirstName, Lastname, Country, Sex, City, IsActive) values (
        '" . $athlete['Name'] . "', 
        '" . $athlete['Surname'] . "', 
        '" . $athlete['Country'] . "', 
        '" . $athlete['Gender'] . "', 
        '" . $athlete['City'] . "',
        '" . true . "'
        );";
    mysqli_query($connection, $query) or exit("Query $query failed");
}
function Edit($id, $connection, $athlete)
{
    $query = "update athletes set 
            FirstName = '" . $athlete['Name'] . "', 
            LastName = '" . $athlete['Surname'] . "', 
            Country = '" . $athlete['Country'] . "', 
            Sex = '" . $athlete['Gender'] . "', 
            City = '" . $athlete['City'] . "' 
            where Id = '" . $id . "';";
    mysqli_query($connection, $query) or exit("Query $query failed");
}
function Remove($athleteId, $connection)
{
    // instead of removing record from database im setting IsActive value = false
    $query = "update athletes set IsActive = false where Id = '$athleteId'";
    mysqli_query($connection, $query) or exit("failed");
    Refresh();
}

// query to get athletes
$query = "select * from athletes where IsActive = true;";
$result = mysqli_query($connection, $query);
$athletes = mysqli_fetch_all($result, MYSQLI_ASSOC);
$athlete = GetData();

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
    Save($_POST['athleteid'], $connection);
    Refresh();
}
if (isset($_POST['cancel'])) {
    Refresh();
}
$currentSort = $_POST['sortby'] ?? ''; // value of sortedBy
if (isset($_POST['sort'])) {
    if (isset($_POST['sortby'])) {
        $currentSort = $_POST['sortby'];
        SortBy($_POST['sortby'], $athletes);
    }
}
// EDIT buttons handle
foreach ($athletes as $item) {
    $i = $item['Id'];
    if (isset($_POST["edit$i"])) {
        $athlete['Id'] = $i;
        $athleteId = $athlete['Id'];
        $athlete['Name'] = $item['FirstName'];
        $athlete['Surname'] = $item['LastName'];
        $athlete['Gender'] = $item['Sex'];
        $athlete['Country'] = $item['Country'];
        $athlete['City'] = $item['City'];
        break;
    }
}
// REMOVE buttons handle
foreach ($athletes as $a) {
    $i = $a['Id'];
    if (isset($_POST["delete$i"])) {
        $athlete['Id'] = $i;
        $athleteId = $athlete['Id'];
        Remove($athleteId, $connection);
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
                        <input type="hidden" name="athleteid" value="<?= $athleteId ?>">
                        <!-- hidden form to store Id -->
                        <label class="form-label">Name:</label>
                        <input type="text" class="form-control" name="name" value="<?= $athlete['Name'] ?>">
                    </div>
                    <div class="col mb-3">
                        <label class="form-label">Surname:</label>
                        <input type="text" class="form-control" name="surname" value="<?= $athlete['Surname'] ?>">
                    </div>
                    <div class="col mb-3">
                        <label class="form-label mb-3">Gender:</label><br>
                        <input type="radio" name="sex" class="mx-2" value="Male" <?= $athlete['Gender'] == 'Male' ? 'checked' : '' ?>>Male
                        <input type="radio" name="sex" class="mx-2" value="Female" <?= $athlete['Gender'] == 'Female' ? 'checked' : '' ?>>Female
                    </div>
                </div>
                <div class="row">
                    <div class="col mb-3">
                        <label class="form-label">Country:</label>
                        <input type="text" class="form-control" name="country" value="<?= $athlete['Country'] ?>">
                    </div>
                    <div class="col mb-3">
                        <label class="form-label">City:</label>
                        <input type="text" class="form-control" name="city" value="<?= $athlete['City'] ?>">
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
                            <th scope="col">
                                <div class="input-group text-align-center">
                                    <select name="sortby" class="form-select form-select-sm" style="width:10px;">
                                        <?php
                                        $keys = array_keys($athletes[0]);
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
                        <?php FillTable($athletes); ?>
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