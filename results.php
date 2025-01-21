<?php
session_start();

// database connection
$connection = mysqli_connect('localhost', 'root', '', 'sportCompetitions');
$_SESSION['resultId'] = $resultId = $_POST['resultId'] ?? '';

function GetData()
{
    $res = [
        'CompetitionId' => $_POST['competitionId'] ?? '',
        'AthleteId' => $_POST['athleteId'] ?? '',
        'Time' => $_POST['time'] ?? '',
    ];
    return $res;
}
function IsFormValid($res)
{
    if (
        !empty($res['CompetitionId']) && is_numeric($res['CompetitionId']) &&
        !empty($res['AthleteId']) && is_numeric($res['AthleteId']) &&
        !empty($res['Time'])
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
        case "CompetitionId": {
            $CIds = array_column($table, 'CompetitionId');
            array_multisort($CIds, SORT_ASC, $table);
        }
            break;
        case "AthleteId": {
            $AIds = array_column($table, 'AthleteId');
            array_multisort($AIds, SORT_ASC, $table);
        }
            break;
        case "Time": {
            $Times = array_column($table, 'Time');
            array_multisort($Times, SORT_ASC, $table);
        }
            break;
        default: {
            $Ids = array_column($table, 'Id');
            array_multisort($Ids, SORT_ASC, $table);
        }
    }
}
function FillTable($results)
{
    foreach ($results as $res) {
        echo "<tr>";
        echo "<td>" . $res['Id'] . "</td>";
        echo "<td>" . $res['CompetitionId'] . "</td>";
        echo "<td>" . $res['AthleteId'] . "</td>";
        echo "<td>" . $res['Time'] . "</td>";
        echo '<td> <div class="btn-group"><button class="btn btn-outline-primary btn-sm" name="edit' . $res['Id']
            . '"><span class="material-symbols-outlined" style="font-size:20px;">edit</span></button>'
            . '<button class="btn btn-outline-danger btn-sm" name="delete' . $res['Id']
            . '"><span class="material-symbols-outlined" style="font-size:20px;">delete</span></button></div>' . '</td>'
        ;
        echo "</tr>";
    }
}
function Save($resultId, $connection)
{
    $res = GetData();
    $resultExists = mysqli_query($connection, "select * from results where Id = '" . $resultId
        . "' or (CompetitionId = '" . $res['CompetitionId']
        . "' and AthleteId = '" . $res['AthleteId'] . "')");
    if (IsFormValid($res)) {
        if (mysqli_num_rows($resultExists) == 0) {
            Insert($connection, $res);
        } else {
            Edit($resultId, $connection, $res);
        }
    } else
        echo "Invalid data cannot save to database";

}
function Insert($connection, $res)
{
    $query = "insert into results (CompetitionId, AthleteId, Time, IsActive) values (
        '" . $res['CompetitionId'] . "', 
        '" . $res['AthleteId'] . "', 
        '" . $res['Time'] . "', 
        '" . true . "'
        );";
    mysqli_query($connection, $query) or exit("Query $query failed");
}
function Edit($id, $connection, $res)
{
    $query = "update results set 
    CompetitionId = '" . $res['CompetitionId'] . "', 
    AthleteId = '" . $res['AthleteId'] . "', 
    Time = '" . $res['Time'] . "'
    where Id = '" . $id . "' or (AthleteId = '" . $res['AthleteId']
        . "' and CompetitionId = '" . $res['CompetitionId'] . "');";
    mysqli_query($connection, $query) or exit("Query $query failed");
}
function Remove($resultId, $connection)
{
    // instead of removing record from database im setting IsActive value = false
    $query = "update results set IsActive = false where Id = '$resultId'";
    mysqli_query($connection, $query) or exit("failed");
    Refresh();
}
// query to get results
$query = "select * from results where IsActive = true;";
$result = mysqli_query($connection, $query);
$results = mysqli_fetch_all($result, MYSQLI_ASSOC);
$res = GetData();
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
    Save($_POST['resultId'], $connection);
    Refresh();
}
if (isset($_POST['cancel'])) {
    Refresh();
}
$currentSort = $_POST['sortby'] ?? ''; // value of sortedBy
if (isset($_POST['sort'])) {
    if (isset($_POST['sortby'])) {
        $currentSort = $_POST['sortby'];
        SortBy($_POST['sortby'], $results);
    }
}
// EDIT buttons handle
foreach ($results as $item) {
    $i = $item['Id'];
    if (isset($_POST["edit$i"])) {
        $res['Id'] = $i;
        $resultId = $res['Id'];
        $res['CompetitionId'] = $item['CompetitionId'];
        $res['AthleteId'] = $item['AthleteId'];
        $res['Time'] = $item['Time'];
        break;
    }
}
// REMOVE buttons handle
foreach ($results as $r) {
    $i = $r['Id'];
    if (isset($_POST["delete$i"])) {
        $res['Id'] = $i;
        $resultId = $res['Id'];
        Remove($resultId, $connection);
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
                        <input type="hidden" name="resultId" value="<?= $resultId ?>">
                        <!-- hidden form to store Id -->
                        <label class="form-label">Competition:</label>
                        <input type="text" class="form-control" name="competitionId"
                            value="<?= $res['CompetitionId'] ?>">
                    </div>
                    <div class="col mb-3">
                        <label class="form-label">Athlete:</label>
                        <input type="text" class="form-control" name="athleteId" value="<?= $res['AthleteId'] ?>">
                    </div>
                    <div class="col mb-3">
                        <label class="form-label mb-3">Time:</label><br>
                        <input type="time" step="1" name="time" class="mx-2" value="<?= $res['Time'] ?>">
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
                            <th scope="col">Competition</th>
                            <th scope="col">Athlete</th>
                            <th scope="col">Time</th>
                            <th scope="col">
                                <div class="input-group text-align-center">
                                    <select name="sortby" class="form-select form-select-sm" style="width:10px;">
                                        <?php
                                        $keys = array_keys($results[0]);
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
                        <?php FillTable($results); ?>
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