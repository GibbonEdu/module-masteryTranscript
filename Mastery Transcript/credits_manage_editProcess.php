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

use Gibbon\FileUploader;
use Gibbon\Services\Format;
use Gibbon\Module\MasteryTranscript\Domain\CreditGateway;
use Gibbon\Module\MasteryTranscript\Domain\CreditMentorGateway;

require_once '../../gibbon.php';

$masteryTranscriptCreditID = $_POST['masteryTranscriptCreditID'] ?? '';
$masteryTranscriptDomainID = $_GET['masteryTranscriptDomainID'] ?? '';
$search = $_GET['search'] ?? '';

$URL = $gibbon->session->get('absoluteURL')."/index.php?q=/modules/Mastery Transcript/credits_manage_edit.php&masteryTranscriptCreditID=$masteryTranscriptCreditID&masteryTranscriptDomainID=$masteryTranscriptDomainID&search=$search";

if (isActionAccessible($guid, $connection2, '/modules/Mastery Transcript/credits_manage_edit.php') == false) {
    $URL .= '&return=error0';
    header("Location: {$URL}");
    exit;
} else {

    // Proceed!
    $creditGateway = $container->get(CreditGateway::class);

    $data = [
        'masteryTranscriptDomainID' => $_POST['masteryTranscriptDomainID'] ?? '',
        'name'                      => $_POST['name'] ?? '',
        'description'               => $_POST['description'] ?? '',
        'active'                    => $_POST['active'] ?? '',
        'gibbonYearGroupIDList'     => (isset($_POST['gibbonYearGroupIDList']) && is_array($_POST['gibbonYearGroupIDList'])) ? implode(',', $_POST['gibbonYearGroupIDList']) : ''
    ];

    // Validate the required values are present
    if (empty($data['name']) || empty($data['active'])) {
        $URL .= '&return=error1';
        header("Location: {$URL}");
        exit;
    }

    // Validate that this record is unique
    if (!$creditGateway->unique($data, ['name'], $masteryTranscriptCreditID)) {
        $URL .= '&return=error7';
        header("Location: {$URL}");
        exit;
    }

    //Deal with file upload
    $data['logo'] = $_POST['logo'];
    if (!empty($_FILES['file']['tmp_name'])) {
        $fileUploader = new FileUploader($pdo, $gibbon->session);
        $logo = $fileUploader->uploadFromPost($_FILES['file'], 'masteryTranscript_domainLogo_'.$data['name']);

        if (empty($logo)) {
            $partialFail = true;
        }
        else {
            $data['logo'] = $logo;
        }
    }

    // Update the record
    $updated = $creditGateway->update($masteryTranscriptCreditID, $data);

    //Deal with mentors
    $creditMentorGateway = $container->get(CreditMentorGateway::class);
    if (!$creditMentorGateway->deleteMentorsByCredit($masteryTranscriptCreditID)) {
        $partialFail = true;
    }
    $gibbonPersonIDs = (isset($_POST['gibbonPersonID']) && is_array($_POST['gibbonPersonID'])) ? $_POST['gibbonPersonID'] : array();
    if (count($gibbonPersonIDs) > 0) {
        foreach ($gibbonPersonIDs as $gibbonPersonID) {
            $data = [
                'masteryTranscriptCreditID' => $masteryTranscriptCreditID,
                'gibbonPersonID'            => $gibbonPersonID
            ];
            if (!$creditMentorGateway->insert($data)) {
                $partialFail = true;
            }
        }
    }

    $URL .= !$updated
        ? "&return=error2"
        : "&return=success0";

    header("Location: {$URL}");
}
