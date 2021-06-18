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
use Gibbon\Services\Format;

// Module includes
require_once __DIR__ . '/moduleFunctions.php';

$highestAction = getHighestGroupedAction($guid, '/modules/Mastery Transcript/journey_manage_commit.php', $connection2);

if (isActionAccessible($guid, $connection2, '/modules/Mastery Transcript/journey_manage_commit.php') == false || $highestAction == false) {
    // Access denied
    $page->addError(__('You do not have access to this action.'));
} else {
    // Proceed!
    $masteryTranscriptJourneyID = $_GET['masteryTranscriptJourneyID'] ?? '';
    $statusKey = $_GET['statusKey'] ?? '';
    $search = $_GET['search'] ?? '';
    $status = $_GET['status'] ?? '';
    $gibbonPersonIDStudent = isset($_GET['gibbonPersonIDStudent'])? $_GET['gibbonPersonIDStudent'] : '';

    if (empty($masteryTranscriptJourneyID)) {
        $page->addError(__('You have not specified one or more required parameters.'));
        return;
    }

    $journeyGateway = $container->get(JourneyGateway::class);
    $result = $journeyGateway->selectJourneyByID($masteryTranscriptJourneyID, $statusKey);

    if ($result->rowCount() != 1) {
        $page->addError(__('The specified record cannot be found.'));
        return;
    }

    $values = $result->fetch();

    if ($highestAction != 'Manage Journey_all' && $values['gibbonPersonIDSchoolMentor'] != $session->get('gibbonPersonID')) {
        $page->addError(__('The specified record does not exist or you do not have access to it.'));
        return;
    }

    $page->breadcrumbs->add(__m('Manage Journey'), 'journey_manage.php')
        ->add(__m('Commit'));

    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], null, array('success0' => __m('Your request was completed successfully. Thank you for your time. The requesting student has been notified, and will be in touch in due course: in the meanwhile, no further action is required on your part.'), 'success1' => __m('Your request was completed successfully. Thank you for your time. The requesting student has been notified. No further action is required on your part.')));
    }

    echo '<p style=\'margin-top: 20px\'>';
    echo __m('{student} has requested your help as mentor of the {type} {name}.', array('student' => Format::name('', $values['preferredName'], $values['surname'], 'Student', false, true), 'type' => strtolower($values['type']), 'name' => $values['name']))."<br/><br/>";
    echo __m('Please {link1} if you are able to get involved, or, {link2} if you not in a position to help.', array('link1' => "<a class='p-1 border border-solid border-green-500 text-green-500 bg-green-200' href='".$session->get('absoluteURL')."/modules/Mastery Transcript/journey_manage_commitProcess.php?response=Y&masteryTranscriptJourneyID=$masteryTranscriptJourneyID&statusKey=$statusKey&search=$search&status=$status&gibbonPersonIDStudent=$gibbonPersonIDStudent'>click here</a>", 'link2' => "<a class='p-1 border border-solid border-red-500 text-red-500 bg-red-200' href='".$session->get('absoluteURL')."/modules/Mastery Transcript/journey_manage_commitProcess.php?response=N&masteryTranscriptJourneyID=$masteryTranscriptJourneyID&statusKey=$statusKey&search=$search&status=$status&gibbonPersonIDStudent=$gibbonPersonIDStudent'>click here</a>"));
    echo '</p>';
}


?>
