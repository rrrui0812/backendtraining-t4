<?php
require_once __DIR__.'/vendor/autoload.php';

use App\Controller\DatabaseController;
use App\Core\Database\DB;
use Dotenv\Dotenv;

//預設使用 dotenv
$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$pdo = DB::init()->pdo();

$databaseController = new DatabaseController($pdo);

do {
    $command = strtolower(readline('Import data again?[y/n/quit]: '));
    switch ($command) {
        case 'y':
        case 'yes':
            $askAgain = false;
            try {
                $databaseController->importData();
            } catch (Exception $e) {
                echo $e->getMessage();
                die();
            }
            break;
        case 'n':
        case 'no':
            $askAgain = false;
            break;
        case 'q':
        case 'quit':
            die();
        default:
            $askAgain = true;
            break;
    }
} while ($askAgain);


statistics:
echo '=================================='.PHP_EOL;
do {
    try {
        $districts = $databaseController->showDistricts();
    } catch (Exception $e) {
        die("DB error!!");
    }
    $command = strtolower(readline('What kind of data to display?'.PHP_EOL.'1:District sort 2:Rainfall statistics [1/2/quit]: '));
    switch ($command) {
        case '1':
            $askAgain = true;
            var_export($districts);
            echo PHP_EOL.'=================================='.PHP_EOL;
            break;
        case '2':
            goto totalRainfall;
            break;
        case 'q':
        case 'quit':
            die();
        default:
            $askAgain = true;
            break;
    }
} while ($askAgain);


totalRainfall:
echo '=================================='.PHP_EOL;
do {
    $command = strtolower(readline('What kind of data to display?'.PHP_EOL.'1:Total annual rainfall 2:Total monthly rainfall [1/2/back/quit]: '));
    switch ($command) {
        case '1':
            $sumBy = 'year';
            goto selectArea;
            break;
        case '2':
            $sumBy = 'month';
            goto selectArea;
            break;
        case 'b':
        case 'back':
            goto statistics;
            break;
        case 'q':
        case 'quit':
            die();
        default:
            $askAgain = true;
            break;
    }
} while ($askAgain);

selectArea:
echo '=================================='.PHP_EOL;
do {
    $command = strtolower(readline('Select districts?'.PHP_EOL.'1:All districts 2:Designated district [1/2/back/quit]: '));
    switch ($command) {
        case '1':
            if ($sumBy === 'year') var_export($databaseController->sumByYear());
            if ($sumBy === 'month') var_export($databaseController->sumByMonth());
            goto totalRainfall;
            break;
        case '2':
            goto selectDistricts;
            break;
        case 'b':
        case 'back':
            goto statistics;
            break;
        case 'q':
        case 'quit':
            die();
        default:
            $askAgain = true;
            break;
    }
} while ($askAgain);

selectDistricts:
echo '=================================='.PHP_EOL;
var_export($districts);
echo PHP_EOL;
do {
    $command = strtolower(readline('Select districts? [0~'.(count($districts) - 1).'/back/quit]: '));
    switch ($command) {
        case 'b':
        case 'back':
            goto selectArea;
            break;
        case 'q':
        case 'quit':
            die();
        default:
            if (!in_array($command, array_keys($districts))) {
                $askAgain = true;
                break;
            }
            if ($sumBy === 'year') var_export($databaseController->sumByYear($districts[$command]));
            if ($sumBy === 'month') var_export($databaseController->sumByMonth($districts[$command]));
            goto totalRainfall;
            break;
    }
} while ($askAgain);
