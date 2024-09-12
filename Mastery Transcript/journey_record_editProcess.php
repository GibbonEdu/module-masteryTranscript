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
use Gibbon\Domain\System\DiscussionGateway;
use Gibbon\Comms\NotificationSender;
use Gibbon\Domain\System\NotificationGateway;

require_once '../../gibbon.php';

$masteryTranscriptJourneyID = $_GET['masteryTranscriptJourneyID'] ?? '';
$search = $_GET['search'] ?? '';

$URL = $session->get('absoluteURL')."/index.php?q=/modules/Mastery Transcript/journey_record_edit.php&search=$search&masteryTranscriptJourneyID=$masteryTranscriptJourneyID";

if (isActionAccessible($guid, $connection2, '/modules/Mastery Transcript/journey_record_edit.php') == false) {
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
    $result = $container->get(JourneyGateway::class)->selectJourneyByID($masteryTranscriptJourneyID);

    if ($result->rowCount() != 1) {
        $URL .= '&return=error2';
        header("Location: {$URL}");
        exit;
    }

    $values = $result->fetch();

    if ($values['gibbonPersonIDStudent'] != $session->get('gibbonPersonID')) {
        $URL .= '&return=error0';
        header("Location: {$URL}");
        exit;
    }

    $discussionGateway = $container->get(DiscussionGateway::class);

    $data = [
        'foreignTable'         => 'masteryTranscriptJourney',
        'foreignTableID'       => $masteryTranscriptJourneyID,
        'gibbonModuleID'       => getModuleIDFromName($connection2, 'Mastery Transcript'),
        'gibbonPersonID'       => $session->get('gibbonPersonID'),
        'gibbonPersonIDTarget' => $session->get('gibbonPersonID'),
        'comment'              => $_POST['comment'] ?? '',
        'type'                 => $_POST['type'] ?? '',
        'comment'              => $_POST['comment'] ?? '',
        'attachmentType'       => $_POST['evidenceType'] ?? null,
        'attachmentLocation'   => $_POST['evidenceLink'] ?? null,
    ];

    //Deal with file upload
    if ($data['attachmentType'] == 'File' && !empty($_FILES['evidenceFile']['tmp_name'])) {
        $fileUploader = new FileUploader($pdo, $session);
        $logo = $fileUploader->uploadFromPost($_FILES['evidenceFile'], 'masteryTranscript_evidence_'.$session->get('gibbonPersonID'));

        if (!empty($logo)) {
            $data['attachmentLocation'] = $logo;
        }
    }

    // Validate the required values are present
    if (empty($data['type']) || empty($data['comment']) || (!is_null($data['attachmentType']) && empty($data['attachmentLocation']))) {
        $URL .= '&return=error1';
        header("Location: {$URL}");
        exit;
    }

    // Insert the record
    $inserted = $discussionGateway->insert($data);

    //Update the journey
    $data = [
        'status'                        => ($data['type'] == 'Comment') ? $values['status'] : 'Complete - Pending',
        'timestampCompletePending'      => ($data['type'] == 'Comment') ? null : date('Y-m-d H:i:s')
    ];
    $updated = $journeyGateway->update($masteryTranscriptJourneyID, $data);

    //Notify mentor
    $notificationGateway = new NotificationGateway($pdo);
    $notificationSender = new NotificationSender($notificationGateway, $session);
    if ($data['status'] == 'Complete - Pending') {
        $notificationString = __m('{student} has requested approval for the {type} {name}.', ['student' => Format::name('', $session->get('preferredName'), $session->get('surname'), 'Student', false, true), 'type' => strtolower($values['type']), 'name' => $values['name']]);
    }
    else {
        $notificationString = __m('{student} has added to the journey log for the {type} {name}.', ['student' => Format::name('', $session->get('preferredName'), $session->get('surname'), 'Student', false, true), 'type' => strtolower($values['type']), 'name' => $values['name']]);
    }
    $notificationSender->addNotification($values['gibbonPersonIDSchoolMentor'], $notificationString, "Mastery Transcript", "/index.php?q=/modules/Mastery Transcript/journey_manage_edit.php&masteryTranscriptJourneyID=$masteryTranscriptJourneyID");
    $notificationSender->sendNotifications();


    $URL .= !$inserted && !$updated
        ? "&return=error2"
        : "&return=success0";

    header("Location: {$URL}");
}
