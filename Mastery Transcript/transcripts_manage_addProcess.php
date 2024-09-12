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

use Gibbon\Services\Format;
use Gibbon\Module\MasteryTranscript\Domain\TranscriptGateway;

require_once '../../gibbon.php';

$URL = $session->get('absoluteURL')."/index.php?q=/modules/Mastery Transcript/transcripts_manage_add.php";

if (isActionAccessible($guid, $connection2, '/modules/Mastery Transcript/transcripts_manage_add.php') == false) {
    $URL .= '&return=error0';
    header("Location: {$URL}");
    exit;
} else {
    // Proceed!
    $transcriptGateway = $container->get(TranscriptGateway::class);

    $data = [
        'gibbonPersonIDStudent'             => $_POST['gibbonPersonIDStudent'] ?? '',
        'gibbonSchoolYearID'                => $session->get('gibbonSchoolYearID'),
        'status'                            => $_POST['status'] ?? '',
        'code'                              => $_POST['code'] ?? '',
        'date'                              => (!empty($_POST['date']))? Format::dateConvert($_POST['date']) : null
    ];

    // Validate the required values are present
    if (empty($data['gibbonPersonIDStudent']) || empty($data['status']) || empty($data['code']) || is_null($data['date'])) {
        $URL .= '&return=error1';
        header("Location: {$URL}");
        exit;
    }

    // Create the record
    $masteryTranscriptTranscriptID = $transcriptGateway->insert($data);

    if ($masteryTranscriptTranscriptID) {
        $URL .= "&return=success0&editID=$masteryTranscriptTranscriptID";
    }
    else {
        $URL .= "&return=error2";
    }

    header("Location: {$URL}");
}
