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
use Gibbon\Module\MasteryTranscript\Domain\OpportunityGateway;
use Gibbon\Module\MasteryTranscript\Domain\OpportunityMentorGateway;
use Gibbon\Module\MasteryTranscript\Domain\OpportunityCreditGateway;

require_once '../../gibbon.php';

$masteryTranscriptOpportunityID = $_POST['masteryTranscriptOpportunityID'] ?? '';
$search = $_GET['search'] ?? '';

$URL = $gibbon->session->get('absoluteURL')."/index.php?q=/modules/Mastery Transcript/opportunities_manage_edit.php&masteryTranscriptOpportunityID=$masteryTranscriptOpportunityID&search=$search";

if (isActionAccessible($guid, $connection2, '/modules/Mastery Transcript/opportunities_manage_edit.php') == false) {
    $URL .= '&return=error0';
    header("Location: {$URL}");
    exit;
} else {

    // Proceed!
    $opportunityGateway = $container->get(OpportunityGateway::class);

    $data = [
        'name'                      => $_POST['name'] ?? '',
        'description'               => $_POST['description'] ?? '',
        'active'                    => $_POST['active'] ?? '',
        'gibbonYearGroupIDList'     => (isset($_POST['gibbonYearGroupIDList']) && is_array($_POST['gibbonYearGroupIDList'])) ? implode(',', $_POST['gibbonYearGroupIDList']) : '',
        'creditLicensing'           => $_POST['creditLicensing'] ?? '',
    ];

    // Validate the required values are present
    if (empty($data['name']) || empty($data['active'])) {
        $URL .= '&return=error1';
        header("Location: {$URL}");
        exit;
    }

    // Validate that this record is unique
    if (!$opportunityGateway->unique($data, ['name'], $masteryTranscriptOpportunityID)) {
        $URL .= '&return=error7';
        header("Location: {$URL}");
        exit;
    }

    //Deal with file upload
    $data['logo'] = $_POST['logo'];
    if (!empty($_FILES['file']['tmp_name'])) {
        $fileUploader = new FileUploader($pdo, $gibbon->session);
        $logo = $fileUploader->uploadFromPost($_FILES['file'], 'masteryTranscript_opportunityLogo_'.$data['name']);

        if (empty($logo)) {
            $partialFail = true;
        }
        else {
            $data['logo'] = $logo;
        }
    }

    // Update the record
    $updated = $opportunityGateway->update($masteryTranscriptOpportunityID, $data);

    //Deal with mentors
    $opportunityMentorGateway = $container->get(OpportunityMentorGateway::class);
    if (!$opportunityMentorGateway->deleteMentorsByOpportunity($masteryTranscriptOpportunityID)) {
        $partialFail = true;
    }
    $gibbonPersonIDs = (isset($_POST['gibbonPersonID']) && is_array($_POST['gibbonPersonID'])) ? $_POST['gibbonPersonID'] : array();
    if (count($gibbonPersonIDs) > 0) {
        foreach ($gibbonPersonIDs as $gibbonPersonID) {
            $data = [
                'masteryTranscriptOpportunityID' => $masteryTranscriptOpportunityID,
                'gibbonPersonID'            => $gibbonPersonID
            ];
            if (!$opportunityMentorGateway->insert($data)) {
                $partialFail = true;
            }
        }
    }

    //Deal with credits
    $opportunityCreditGateway = $container->get(OpportunityCreditGateway::class);
    if (!$opportunityCreditGateway->deleteCreditsByOpportunity($masteryTranscriptOpportunityID)) {
        $partialFail = true;
    }
    $masteryTranscriptCreditIDs = (isset($_POST['masteryTranscriptCreditID']) && is_array($_POST['masteryTranscriptCreditID'])) ? $_POST['masteryTranscriptCreditID'] : array();
    if (count($masteryTranscriptCreditIDs) > 0) {
        foreach ($masteryTranscriptCreditIDs as $masteryTranscriptCreditID) {
            $data = [
                'masteryTranscriptOpportunityID' => $masteryTranscriptOpportunityID,
                'masteryTranscriptCreditID'      => $masteryTranscriptCreditID
            ];
            if (!$opportunityCreditGateway->insert($data)) {
                $partialFail = true;
            }
        }
    }

    $URL .= !$updated
        ? "&return=error2"
        : "&return=success0";

    header("Location: {$URL}");
}
