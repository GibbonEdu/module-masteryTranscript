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

use Gibbon\FileUploader;
use Gibbon\Services\Format;
use Gibbon\Module\MasteryTranscript\Domain\JourneyGateway;
use Gibbon\Module\MasteryTranscript\Domain\CreditGateway;
use Gibbon\Module\MasteryTranscript\Domain\OpportunityGateway;
use Gibbon\Comms\NotificationSender;
use Gibbon\Domain\System\NotificationGateway;

require_once '../../gibbon.php';

$search = $_GET['search'] ?? '';

$URL = $session->get('absoluteURL')."/index.php?q=/modules/Mastery Transcript/journey_record_add.php&search=$search";

if (isActionAccessible($guid, $connection2, '/modules/Mastery Transcript/journey_record_add.php') == false) {
    $URL .= '&return=error0';
    header("Location: {$URL}");
    exit;
} else {
    // Proceed!
    $journeyGateway = $container->get(JourneyGateway::class);

    $data = [
        'gibbonPersonIDStudent'             => $session->get('gibbonPersonID'),
        'gibbonSchoolYearID'                => $session->get('gibbonSchoolYearID'),
        'type'                              => $_POST['type'] ?? '',
        'masteryTranscriptOpportunityID'    => $_POST['masteryTranscriptOpportunityID'] ?? null,
        'masteryTranscriptCreditID'         => $_POST['masteryTranscriptCreditID'] ?? null,
        'gibbonPersonIDSchoolMentor'        => (!empty($_POST['gibbonPersonIDSchoolMentor'])) ? substr($_POST['gibbonPersonIDSchoolMentor'], (strpos($_POST['gibbonPersonIDSchoolMentor'], "-")+1)) : null,
        'status'                            => 'Current - Pending',
        'statusKey'                         => $confirmationKey = randomPassword(20)
    ];

    // Validate the required values are present
    if (empty($data['type']) || (empty($data['masteryTranscriptOpportunityID']) && empty($data['masteryTranscriptCreditID'])) || empty($data['gibbonPersonIDSchoolMentor'])) {
        $URL .= '&return=error1';
        header("Location: {$URL}");
        exit;
    }

    // Create the record
    $masteryTranscriptJourneyID = $journeyGateway->insert($data);

    //Get credit/oppotunity name
    $name = '';
    if ($data['type'] == 'Credit') {
        $creditGateway = $container->get(CreditGateway::class);
        $name = $creditGateway->getByID($data['masteryTranscriptCreditID'])['name'];
    }
    else if ($data['type'] == 'Opportunity') {
        $opportunityGateway = $container->get(OpportunityGateway::class);
        $name = $opportunityGateway->getByID($data['masteryTranscriptOpportunityID'])['name'];
    }

    //Notify the mentor
    $notificationGateway = new NotificationGateway($pdo);
    $notificationSender = new NotificationSender($notificationGateway, $session);
    $notificationString = __m('{student} has requested Mastery Trascript mentorship for the {type} {name}.', ['student' => Format::name('', $session->get('preferredName'), $session->get('surname'), 'Student', true, true), 'type' => strtolower($data['type']), 'name' => $name]);
    $notificationSender->addNotification($data['gibbonPersonIDSchoolMentor'], $notificationString, "Mastery Transcript", "/index.php?q=/modules/Mastery Transcript/journey_manage_commit.php&masteryTranscriptJourneyID=$masteryTranscriptJourneyID&statusKey=".$data['statusKey']);
    $notificationSender->sendNotifications();

    if ($masteryTranscriptJourneyID) {
        $URL .= "&return=success0&editID=$masteryTranscriptJourneyID";
    }
    else {
        $URL .= "&return=error2";
    }

    header("Location: {$URL}");
}
