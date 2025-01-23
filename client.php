<?php
session_start();
// database connection
$connection = mysqli_connect('localhost', 'root', '', 'sportCompetitions');
// query to get all athletes
$result = mysqli_query($connection, "select * from athletes where IsActive = true") or exit("Failed");
$athletes = mysqli_fetch_all($result);
$userId = $_SESSION['athleteId'] ?? '';
// query to get athlete
$result = mysqli_query($connection, "select * from athletes where id = '$userId'") or exit("Failed");
$athlete = mysqli_fetch_assoc($result);
$athleteId = $athlete["Id"] ?? '';
// query to get results
$result2 = mysqli_query($connection, "select r.* from results r join competitions c on r.CompetitionId = c.Id where r.athleteId = '$athleteId' and r.IsActive order by c.Date;");
$results = mysqli_fetch_all($result2, MYSQLI_ASSOC);
// query to get competitionsId's
$result3 = mysqli_query($connection, "select CompetitionId from results where AthleteId = '$athleteId' and IsActive = true");
$competitionIds = mysqli_fetch_all($result3);
// query to get all competitions with 
$allCompetitions = [];
foreach ($competitionIds as $id) {
    $result4 = mysqli_query($connection, "select * from competitions where Id = '$id[0]';");
    while ($competition = mysqli_fetch_assoc($result4)) {
        $allCompetitions[] = $competition;
    }
}
$distance = $_SESSION['distance'] ?? '';
function Refresh()
{
    header("Refresh:0");
    exit();
}
// function to sort table
function SortBy($key, &$table)
{
    switch ($key) {
        case "Name": {
            $Names = array_column($table, 'Name');
            array_multisort($Names, SORT_ASC, $table);
        }
            break;
        case "Distance": {
            $Dis = array_column($table, 'Distance');
            array_multisort($Dis, SORT_ASC, $table);
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
function FillTable($competitions, $results)
{
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
}
if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: main.php');
    exit();
}
if (isset($_POST['refresh'])) {
    header("Refresh:0");
    exit();
}
if (isset($_POST['show'])) {
    $_SESSION['athleteId'] = $_POST['athlete'];
    $_SESSION['distance'] = '';
    Refresh();
}
if (isset($_POST['showtimes'])) {
    $_SESSION['distance'] = $_POST['distance'];
    Refresh();
}
// value of sortedBy
$currentSort = $_POST['sortby'] ?? 'JD';
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
        <h1 class="display-3 text-center fw-bold mb-4" style="text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);">Sports
            Competitions</h1>
        <form method="POST">
            <div class="btn-group mb-2" role="group" style="width: 200px;">
                <button name="refresh" class="btn btn-primary">Refresh</button>
                <button name="logout" class="btn btn-primary">Logout</button>
            </div>
            <select class="form-select mb-2" name="athlete">
                <option>Select Athlete</option>
                <?php
                foreach ($athletes as $a) {
                    $selected = $athleteId === $a[0] ? 'selected' : '';
                    $_SESSION['athleteId'] = $athleteId;
                    echo '<option value="' . $a[0] . '" ' . $selected . '>' . $a[1] . ' ' . $a[2] . '</option>';
                }
                ?>
            </select>
            <button type="submit" class="btn btn-outline-info mb-2" name="show" style="width: 200px;">Show info</button>
        </form>
        <div class="row w-100">
            <!-- Personal Details -->
            <div class="col mb-5 align-items-center">
                <h2 class="text-center mb-3" style="text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);">Personal Details</h2>
                <div class="container text-center">
                    <p class="fw-bold">Name: <?= $athlete['FirstName'] ?? '' ?></p>
                    <p class="fw-bold">Surname: <?= $athlete['LastName'] ?? '' ?></p>
                    <p class="fw-bold">Gender: <?= $athlete['Sex'] ?? '' ?></p>
                    <p class="fw-bold">Country: <?= $athlete['Country'] ?? '' ?></p>
                    <p class="fw-bold">City: <?= $athlete['City'] ?? '' ?></p>
                </div>
            </div>
            <!-- Progress -->
            <div class="col justify-content-center align-items-center">
                <h2 class="text-center mb-3" style="text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);">Progress</h2>
                <div class="row justify-content-center">
                    <div class="col-auto">
                        <form method="POST" class="d-flex flex-column align-items-center gap-2">
                            <select class="form-select" name="distance" style="width:200px;">
                                <?php
                                $distances = [];
                                foreach ($allCompetitions as $c) {
                                    if (!in_array($c['Distance'], $distances))
                                        array_push($distances, $c['Distance']);
                                }
                                sort($distances);
                                foreach ($distances as $d) {
                                    $selected = $distance === $d ? 'selected' : '';
                                    $_SESSION['distance'] = $distance;
                                    echo '<option value="' . $d . '" ' . $selected . '>' . $d . ' km' . '</option>';
                                }
                                ?>
                            </select>
                            <button type="submit" class="btn btn-outline-info" name="showtimes"
                                style="width: 200px;">ShowTimes</button>
                        </form>
                    </div>
                </div>
                <div class="row justify-content-center mt-3">
                    <?php
                    function IsTimeBetter($time1, $time2)
                    {
                        $time1 = explode(':', $time1);
                        $time2 = explode(':', $time2);
                        if ($time1[0] < $time2[0])
                            return false;
                        else if ($time1[0] > $time2[0])
                            return true;
                        else {
                            if ($time1[1] < $time2[1])
                                return false;
                            else if ($time1[1] > $time2[1])
                                return true;
                            else {
                                if ($time1[2] < $time2[2])
                                    return false;
                                else if ($time1[2] > $time2[2])
                                    return true;
                            }
                        }
                    }
                    function BestTime($results, $distances)
                    {
                        $bestTime = '';
                        $c = 0;
                        foreach ($results as $r) {
                            if (in_array($r['CompetitionId'], $distances)) {
                                $c++;
                                if ($c == 1) {
                                    $bestTime = $r['Time'];
                                } else {
                                    if (IsTimeBetter($bestTime, $r['Time'])) {
                                        $bestTime = $r['Time'];
                                    }
                                }
                            }
                        }
                        return $bestTime;
                    }
                    $givenDistancesCompetitionIds = [];
                    foreach ($allCompetitions as $c) {
                        if ($c['Distance'] == $_SESSION['distance']) {
                            if (!in_array($c['Id'], $givenDistancesCompetitionIds))
                                array_push($givenDistancesCompetitionIds, $c['Id']);
                        }
                    }
                    $counter = 0;
                    $bestTime = BestTime($results, $givenDistancesCompetitionIds);
                    foreach ($results as $r) {
                        if (in_array($r['CompetitionId'], $givenDistancesCompetitionIds)) {
                            $counter++;
                            if ($r['Time'] == $bestTime) {
                                $icon = 'crown';
                                $color = 'gold';
                                $prevTime = $r['Time'];
                            } else if ($counter == 1) {
                                $icon = 'monitoring';
                                $color = '';
                                $prevTime = $r['Time'];
                            } else {
                                if (IsTimeBetter($prevTime, $r['Time'])) {
                                    $icon = 'trending_up';
                                    $color = 'green';
                                    $prevTime = $r['Time'];
                                } else if ($r['Time'] == $prevTime) {
                                    $icon = 'trending_flat';
                                    $color = '#0096FF';
                                    $prevTime = $r['Time'];
                                } else {
                                    $icon = 'trending_down';
                                    $color = 'red';
                                    $prevTime = $r['Time'];
                                }
                            }
                            echo '<p class="fw-semibold text-center" style="font-size:20px; color:' . $color . ';">' . $counter . '. ' . $r['Time'] . '<span class="material-symbols-outlined" style="font-size:20px;">' . $icon . '</span>' . '</p>';
                        }
                    }
                    echo '<p class="fw-bold text-center">Best time (PR): ' . '<i style="color:gold;">' . $bestTime . '</i>' . '</p>';
                    ?>
                </div>
            </div>
        </div>
        <!-- Competitions -->
        <div class="w-100">
            <form method="POST">
                <h2 class="text-center mb-3" style="text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);">Competitions</h2>
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
                            <th scope="col">
                                <div class="input-group text-align-center">
                                    <select name="sortby" class="form-select form-select-sm" style="width:50px;">
                                        <?php
                                        $keys = array_keys($allCompetitions[0]);
                                        foreach ($keys as $key) {
                                            if ($key != 'IsActive') {
                                                $selected = $currentSort === $key ? 'selected' : '';
                                                echo '<option value="' . $key . '" ' . $selected . '>' . $key . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                    <button type="submit" name="sort" class="btn btn-primary btn-sm ">Sort</button>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        if (isset($_POST['sort'])) {
                            if (isset($_POST['sortby'])) {
                                $currentSort = $_POST['sortby'];
                                SortBy($currentSort, $allCompetitions);
                            }
                        }
                        FillTable($allCompetitions, $results);
                        ?>
                    </tbody>
                </table>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>

</html>