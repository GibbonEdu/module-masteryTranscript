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

require_once '../../gibbon.php';

$masteryTranscriptJourneyID = $_POST['masteryTranscriptJourneyID'] ?? '';
$search = $_GET['search'] ?? '';

$URL = $gibbon->session->get('absoluteURL')."/index.php?q=/modules/Mastery Transcript/journey_manage.php&search=$search";

$highestAction = getHighestGroupedAction($guid, '/modules/Mastery Transcript/journey_manage_delete.php', $connection2);

if (isActionAccessible($guid, $connection2, '/modules/Mastery Transcript/journey_manage_delete.php') == false || $highestAction == false) {
    $URL .= '&return=error0';
    header("Location: {$URL}");
    exit;
} elseif (empty($masteryTranscriptJourneyID)) {
    $URL .= '&return=error1';
    header("Location: {$URL}");
    exit;
} else {
    // Proceed!
    $journeyGateway = $container->get(JourneyGateway::class);
    $result = $journeyGateway->selectJourneyByID($masteryTranscriptJourneyID);

    if (empty($result)) {
        $URL .= '&return=error2';
        header("Location: {$URL}");
        exit;
    }

    $values = $result->fetch();

    if ($highestAction != 'Manage Journey_all' && $values['gibbonPersonIDSchoolMentor'] != $gibbon->session->get('gibbonPersonID')) {
        $URL .= '&return=error0';
        header("Location: {$URL}");
        exit();
    }

    $deleted = $journeyGateway->delete($masteryTranscriptJourneyID);

    $URL .= !$deleted
        ? '&return=error2'
        : '&return=success0';

    header("Location: {$URL}");
}
