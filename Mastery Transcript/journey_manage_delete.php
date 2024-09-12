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

use Gibbon\Forms\Prefab\DeleteForm;
use Gibbon\Module\MasteryTranscript\Domain\JourneyGateway;

$highestAction = getHighestGroupedAction($guid, '/modules/Mastery Transcript/journey_manage_delete.php', $connection2);

if (isActionAccessible($guid, $connection2, '/modules/Mastery Transcript/journey_manage_delete.php') == false || $highestAction == false) {
    // Access denied
    $page->addError(__('You do not have access to this action.'));
} else {
    // Proceed!
    $masteryTranscriptJourneyID = $_GET['masteryTranscriptJourneyID'] ?? '';
    $search = $_GET['search'] ?? '';
    $status = $_GET['status'] ?? '';
    $gibbonPersonIDStudent = isset($_GET['gibbonPersonIDStudent'])? $_GET['gibbonPersonIDStudent'] : '';

    if (empty($masteryTranscriptJourneyID)) {
        $page->addError(__('You have not specified one or more required parameters.'));
        return;
    }

    $values = $container->get(JourneyGateway::class)->getByID($masteryTranscriptJourneyID);

    if (empty($values)) {
        $page->addError(__('The specified record cannot be found.'));
        return;
    }

    if ($highestAction != 'Manage Journey_all' && $values['gibbonPersonIDSchoolMentor'] != $session->get('gibbonPersonID')) {
        $page->addError(__('The specified record does not exist or you do not have access to it.'));
        return;
    }

    $form = DeleteForm::createForm($session->get('absoluteURL')."/modules/Mastery Transcript/journey_manage_deleteProcess.php?search=$search&status=$status&gibbonPersonIDStudent=$gibbonPersonIDStudent");
    echo $form->getOutput();
}
