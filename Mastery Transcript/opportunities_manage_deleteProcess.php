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

use Gibbon\Module\MasteryTranscript\Domain\OpportunityGateway;
use Gibbon\Module\MasteryTranscript\Domain\OpportunityMentorGateway;
use Gibbon\Module\MasteryTranscript\Domain\OpportunityCreditGateway;

require_once '../../gibbon.php';

$masteryTranscriptOpportunityID = $_POST['masteryTranscriptOpportunityID'] ?? '';
$search = $_GET['search'] ?? '';

$URL = $session->get('absoluteURL')."/index.php?q=/modules/Mastery Transcript/opportunities_manage.php&search=$search";

if (isActionAccessible($guid, $connection2, '/modules/Mastery Transcript/opportunities_manage_delete.php') == false) {
    $URL .= '&return=error0';
    header("Location: {$URL}");
    exit;
} elseif (empty($masteryTranscriptOpportunityID)) {
    $URL .= '&return=error1';
    header("Location: {$URL}");
    exit;
} else {
    // Proceed!
    $opportunityGateway = $container->get(OpportunityGateway::class);
    $values = $opportunityGateway->getByID($masteryTranscriptOpportunityID);

    if (empty($values)) {
        $URL .= '&return=error2';
        header("Location: {$URL}");
        exit;
    }

    $deleted = $opportunityGateway->delete($masteryTranscriptOpportunityID);

    $opportunityMentorGateway = $container->get(OpportunityMentorGateway::class);
    $opportunityMentorGateway->deleteMentorsByOpportunity($masteryTranscriptOpportunityID);

    $opportunityCreditGateway = $container->get(OpportunityCreditGateway::class);
    $opportunityCreditGateway->deleteCreditsByOpportunity($masteryTranscriptOpportunityID);

    $URL .= !$deleted
        ? '&return=error2'
        : '&return=success0';

    header("Location: {$URL}");
}
