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

use Gibbon\Module\MasteryTranscript\Domain\JourneyGateway;

$_POST['address'] = '/modules/Mastery Transcript/journey_manage_commitProcess.php';

require_once '../../gibbon.php';

$search = $_GET['search'] ?? '';

$URL = $gibbon->session->get('absoluteURL')."/index.php?q=/modules/Mastery Transcript/journey_manage.php&search=$search";

$highestAction = getHighestGroupedAction($guid, '/modules/Mastery Transcript/journey_manage_commit.php', $connection2);

if (isActionAccessible($guid, $connection2, '/modules/Mastery Transcript/journey_manage_commit.php') == false || $highestAction == false) {
    $URL .= '&return=error0';
    header("Location: {$URL}");
    exit();
} else {
    // Proceed!
    $masteryTranscriptJourneyID = $_GET['masteryTranscriptJourneyID'] ?? '';
    $response = $_GET['response'] ?? '';

    if (empty($masteryTranscriptJourneyID) || ($response != 'Y' && $response !='N')) {
        $URL .= '&return=error1';
        header("Location: {$URL}");
        exit;
    }

    $journeyGateway = $container->get(JourneyGateway::class);
    $result = $journeyGateway->selectJourneyByID($masteryTranscriptJourneyID);

    if (empty($result)) {
        $URL .= '&return=error0';
        header("Location: {$URL}");
        exit();
    }

    $values = $result->fetch();

    if ($highestAction != 'Manage Journey_all' && $values['gibbonPersonIDSchoolMentor'] != $gibbon->session->get('gibbonPersonID')) {
        $URL .= '&return=error0';
        header("Location: {$URL}");
        exit();
    }

    if ($response == 'Y') {
        //Update record
        $data = array(
            'status' => 'Current',
        );
        $updated = $journeyGateway->update($masteryTranscriptJourneyID, $data);

        //Notify student
        $notificationText = __m('Your mentorship request for the Mastery Transcript {type} {name} has been accepted.', array('type' => strtolower($values['type']), 'name' => $values['name']));
        setNotification($connection2, $guid, $values['gibbonPersonIDStudent'], $notificationText, 'Mastery Transcript', "/index.php?q=/modules/Mastery Transcript/journey_record_edit.php&masteryTranscriptJourneyID=$masteryTranscriptJourneyID");

        //Return to thanks page
        $URL .= "&return=success0&masteryTranscriptJourneyID=$masteryTranscriptJourneyID";
        header("Location: {$URL}");
    }
    else {
        //Delete record
        $deleted = $journeyGateway->delete($masteryTranscriptJourneyID);

        //Notify student
        $notificationText = __m('Your mentorship request for the Mastery Transcript {type} {name} has been declined. Your enrolment has been deleted.', array('type' => strtolower($values['type']), 'name' => $values['name']));
        setNotification($connection2, $guid, $values['gibbonPersonIDStudent'], $notificationText, 'Mastery Transcript', "/index.php?q=/modules/Mastery Transcript/journey_record.php");

        //Return to thanks page
        $URL .= "&return=success1&masteryTranscriptJourneyID=$masteryTranscriptJourneyID";
        header("Location: {$URL}");
    }
}
