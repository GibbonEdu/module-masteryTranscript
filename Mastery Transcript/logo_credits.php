<?php
/*
Gibbon: the flexible, open school platform
Founded by Ross Parker at ICHK Secondary. Built by Ross Parker, Sandra Kuipers and the Gibbon community (https://gibbonedu.org/about/)
Copyright © 2010, Gibbon Foundation
Gibbon™, Gibbon Education Ltd. (Hong Kong)

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

//Module includes
include './modules/'.$session->get('module').'/moduleFunctions.php';

if (isActionAccessible($guid, $connection2, '/modules/Mastery Transcript/logo_credits.php') == false) {
    //Acess denied
    echo "<div class='error'>";
    echo __('You do not have access to this action.');
    echo '</div>';
} else {
    //Proceed!
    $page->breadcrumbs->add(__('Credits & Licensing'));

    try {
        $data = array();
        $sql = "
        (SELECT name, creditLicensing FROM masteryTranscriptDomain WHERE NOT creditLicensing = '')
        UNION (SELECT name, creditLicensing FROM masteryTranscriptCredit WHERE NOT creditLicensing = '')
        UNION (SELECT name, creditLicensing FROM masteryTranscriptOpportunity WHERE NOT creditLicensing = '')
        ORDER BY name
        ";
        $result = $connection2->prepare($sql);
        $result->execute($data);
    } catch (PDOException $e) { echo "<div class='error'>".$e->getMessage().'</div>';
    }
    if ($result->rowCount() < 1) { echo "<div class='error'>";
        echo __('There are no records to display.');
        echo '</div>';
    } else {
        while ($row = $result->fetch()) {
            echo '<h4>'.$row['name'].'</h4>';
            echo $row['creditLicensing'].'<br/>';
        }
    }
}
