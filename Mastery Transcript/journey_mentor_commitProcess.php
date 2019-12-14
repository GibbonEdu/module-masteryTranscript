<?php
/*
Gibbon, Flexible & Open School System
Copyright (C) 2010, Ross Parker

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program. If not, see <http://www.gnu.org/licenses/>.
*/

require_once '../../gibbon.php';

require_once  './moduleFunctions.php';

$publicUnits = getSettingByScope($connection2, 'Free Learning', 'publicUnits');

$highestAction = false;
$canManage = false;
$gibbonPersonID ='';
if (isset($_SESSION[$guid]['gibbonPersonID'])) {
    $highestAction = getHighestGroupedAction($guid, '/modules/Free Learning/units_browse.php', $connection2);
    $gibbonPersonID = $_SESSION[$guid]['gibbonPersonID'];
    $canManage = false;
    if (isActionAccessible($guid, $connection2, '/modules/Free Learning/units_manage.php') and $highestAction == 'Browse Units_all') {
        $canManage = true;
    }
    if ($canManage) {
        if (isset($_GET['gibbonPersonID'])) {
            $gibbonPersonID = $_GET['gibbonPersonID'];
        }
    }
}

//Get params
$freeLearningUnitID = '';
if (isset($_GET['freeLearningUnitID'])) {
    $freeLearningUnitID = $_GET['freeLearningUnitID'];
}
$showInactive = 'N';
if ($canManage and isset($_GET['showInactive'])) {
    $showInactive = $_GET['showInactive'];
}
$gibbonDepartmentID = '';
if (isset($_GET['gibbonDepartmentID'])) {
    $gibbonDepartmentID = $_GET['gibbonDepartmentID'];
}
$difficulty = '';
if (isset($_GET['difficulty'])) {
    $difficulty = $_GET['difficulty'];
}
$name = '';
if (isset($_GET['name'])) {
    $name = $_GET['name'];
}
$view = '';
if (isset($_GET['view'])) {
    $view = $_GET['view'];
}
if ($view != 'grid' and $view != 'map') {
    $view = 'list';
}
$response = null;
if (isset($_GET['response'])) {
    $response = $_GET['response'];
}
$freeLearningUnitStudentID = null;
if (isset($_GET['freeLearningUnitStudentID'])) {
    $freeLearningUnitStudentID = $_GET['freeLearningUnitStudentID'];
}
$confirmationKey = null;
if (isset($_GET['confirmationKey'])) {
    $confirmationKey = $_GET['confirmationKey'];
}

//Check to see if system settings are set from databases
if (@$_SESSION[$guid]['systemSettingsSet'] == false) {
    getSystemSettings($guid, $connection2);
}

//Set return URL
$URL = $_SESSION[$guid]['absoluteURL']."/index.php?q=/modules/Free Learning/units_mentor.php&sidebar=true&gibbonDepartmentID=$gibbonDepartmentID&difficulty=$difficulty&name=$name&showInactive=$showInactive&gibbonPersonID=$gibbonPersonID&view=$view";

if ($response == '' or $freeLearningUnitStudentID == '' or $confirmationKey == '') {
    $URL .= '&return=error3';
    header("Location: {$URL}");
} else {
    //Check student & confirmation key
    try {
        $data = array('freeLearningUnitStudentID' => $freeLearningUnitStudentID, 'confirmationKey' => $confirmationKey) ;
        $sql = 'SELECT freeLearningUnitStudent.*, freeLearningUnit.name AS unit FROM freeLearningUnitStudent JOIN freeLearningUnit ON (freeLearningUnitStudent.freeLearningUnitID=freeLearningUnit.freeLearningUnitID) WHERE freeLearningUnitStudentID=:freeLearningUnitStudentID AND confirmationKey=:confirmationKey AND status=\'Current - Pending\'';
        $result = $connection2->prepare($sql);
        $result->execute($data);
    } catch (PDOException $e) {
        $URL .= '&return=error2';
        header("Location: {$URL}");
        exit();
    }

    if ($result->rowCount()!=1) {
        $URL .= '&return=error2';
        header("Location: {$URL}");
        exit();
    }
    else {
        $row = $result->fetch() ;
        $unit = $row['unit'];
        $freeLearningUnitID = $row['freeLearningUnitID'];

        if ($response == 'Y') { //If yes, updated student and collaborators based on confirmation key
            try {
                $data = array('confirmationKey' => $confirmationKey) ;
                $sql = 'UPDATE freeLearningUnitStudent SET status=\'Current\' WHERE confirmationKey=:confirmationKey';
                $result = $connection2->prepare($sql);
                $result->execute($data);
            } catch (PDOException $e) {
                $URL .= "&return=error2&freeLearningUnitID=$freeLearningUnitID";
                header("Location: {$URL}");
                exit();
            }

            //Notify student
            $notificationText = sprintf(__($guid, 'Your mentorship request for the Free Learning unit %1$s has been accepted.', 'Free Learning'), $unit);
            setNotification($connection2, $guid, $row['gibbonPersonIDStudent'], $notificationText, 'Free Learning', '/index.php?q=/modules/Free Learning/units_browse_details.php&freeLearningUnitID='.$freeLearningUnitID.'&freeLearningUnitStudentID='.$freeLearningUnitStudentID.'&gibbonDepartmentID=&difficulty=&name=&sidebar=true&tab=1');

            //Return to thanks page
            $URL .= "&return=success1&freeLearningUnitID=$freeLearningUnitID";
            header("Location: {$URL}");
        }
        else { //If no, delete the records
            try {
                $data = array('confirmationKey' => $confirmationKey) ;
                $sql = 'DELETE FROM freeLearningUnitStudent WHERE confirmationKey=:confirmationKey';
                $result = $connection2->prepare($sql);
                $result->execute($data);
            } catch (PDOException $e) {
                $URL .= "&return=error2&freeLearningUnitID=$freeLearningUnitID";
                header("Location: {$URL}");
                exit();
            }

            //Notify student
            $notificationText = sprintf(__($guid, 'Your mentorship request for the Free Learning unit %1$s has been declined. Your enrolment has been deleted.', 'Free Learning'), $unit);
            setNotification($connection2, $guid, $row['gibbonPersonIDStudent'], $notificationText, 'Free Learning', '/index.php?q=/modules/Free Learning/units_browse_details.php&freeLearningUnitID='.$freeLearningUnitID.'&freeLearningUnitStudentID='.$freeLearningUnitStudentID.'&gibbonDepartmentID=&difficulty=&name=&sidebar=true&tab=1');

            //Return to thanks page
            $URL .= "&return=success0&freeLearningUnitID=$freeLearningUnitID";
            header("Location: {$URL}");
        }
    }
}
