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

// Module includes
require_once __DIR__ . '/moduleFunctions.php';

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
$freeLearningUnitID = null;
if (isset($_GET['freeLearningUnitID'])) {
    $freeLearningUnitID = $_GET['freeLearningUnitID'];
}
$mode = 'external';
if (isset($_GET['mode']) && $_GET['mode'] == 'internal') {
    $mode = $_GET['mode'];
}
$confirmationKey = null;
if (isset($_GET['confirmationKey'])) {
    $confirmationKey = $_GET['confirmationKey'];
}

if ($freeLearningUnitID != '' && isset($_SESSION[$guid]['gibbonPersonID'])) {
    //Check unit
    try {
        $data = array('freeLearningUnitID' => $freeLearningUnitID) ;
        $sql = 'SELECT freeLearningUnit.* FROM freeLearningUnit WHERE freeLearningUnitID=:freeLearningUnitID';
        $result = $connection2->prepare($sql);
        $result->execute($data);
    } catch (PDOException $e) {
        echo "<div class='error'>".$e->getMessage().'</div>';
    }

    if ($result->rowCount() != 1) {
        echo "<div class='error'>";
        echo __($guid, 'There are no records to display.');
        echo '</div>';
    } else {
        $row = $result->fetch();

        $urlParams = compact('view', 'name', 'difficulty', 'gibbonDepartmentID', 'showInactive', 'freeLearningUnitID');

        $page->breadcrumbs
            ->add(__m('Browse Units'), 'units_browse.php', $urlParams);

        $urlParams["sidebar"] = "true";
        $page->breadcrumbs->add(__m('Unit Details'), 'units_browse_details.php', $urlParams)
            ->add(__m('Approval'));

        if (isset($_GET['return'])) {
            returnProcess($guid, $_GET['return'], null, array('success0' => __($guid, 'Your request was completed successfully. Thank you for your time.', 'Free Learning'), 'success1' => __($guid, 'Your request was completed successfully. Thank you for your time. The learners you are helping will be in touch in due course: in the meanwhile, no further action is required on your part.', 'Free Learning')));
        }

        //Show choice for school mentor
        if ($mode == "internal" && $confirmationKey != '') {
            echo '<p>';
            echo sprintf(__($guid, 'The following users at %1$s have requested your input into their %2$sFree Learning%3$s work, with the hope that you will be able to act as a "critical buddy" or mentor, offering feedback on their progress.', 'Free Learning'), $_SESSION[$guid]['systemName'], "<a target='_blank' href='http://rossparker.org'>", '</a>');
            echo '<br/>';
            echo '</p>';

            $freeLearningUnitStudentID = null;

            try {
                $dataConfCheck = array('confirmationKey' => $confirmationKey) ;
                $sqlConfCheck = 'SELECT freeLearningUnitStudentID, preferredName, surname
                    FROM freeLearningUnitStudent
                    JOIN gibbonPerson ON (freeLearningUnitStudent.gibbonPersonIDStudent=gibbonPerson.gibbonPersonID)
                    WHERE confirmationKey=:confirmationKey
                    ORDER BY freeLearningUnitStudentID';
                $resultConfCheck = $connection2->prepare($sqlConfCheck);
                $resultConfCheck->execute($dataConfCheck);
            } catch (PDOException $e) {
                echo "<div class='error'>".$e->getMessage().'</div>';
            }
            if ($resultConfCheck->rowCount() < 1) {
                echo "<div class='error'>";
                echo __($guid, 'An error occurred.');
                echo '</div>';
            }
            else {
                echo '<ul>';
                while ($rowConfCheck = $resultConfCheck->fetch()) {
                    $freeLearningUnitStudentID = (is_null($freeLearningUnitStudentID) ? $rowConfCheck['freeLearningUnitStudentID'] : $freeLearningUnitStudentID);
                    echo '<li>'.formatName('', $rowConfCheck['preferredName'], $rowConfCheck['surname'], 'Student', true).'</li>';
                }
                echo '</ul>';
                echo '<p style=\'margin-top: 20px\'>';
                echo sprintf(__($guid, 'The unit you are being asked to advise on is called %1$s and is described as follows:', 'Free Learning'), '<b>'.$row['name'].'</b>').$row['blurb']."<br/><br/>";
                echo sprintf(__($guid, 'Please %1$sclick here%2$s if you are able to get involved, or, %3$sclick here%4$s if you not in a position to help.', 'Free Learning'), "<a style='font-weight: bold; text-decoration: underline; color: #390' href='".$_SESSION[$guid]['absoluteURL']."/modules/Free Learning/units_mentorProcess.php?response=Y&freeLearningUnitStudentID=".$freeLearningUnitStudentID."&confirmationKey=$confirmationKey&freeLearningUnitID=$freeLearningUnitID'>", '</a>', "<a style='font-weight: bold; text-decoration: underline; color: #CC0000' href='".$_SESSION[$guid]['absoluteURL']."/modules/Free Learning/units_mentorProcess.php?response=N&freeLearningUnitStudentID=".$freeLearningUnitStudentID."&confirmationKey=$confirmationKey&freeLearningUnitID=$freeLearningUnitID'>", '</a>');
                echo '</p>';
            }
        }
    }
}


?>
