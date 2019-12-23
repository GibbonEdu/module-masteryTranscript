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

use Gibbon\Forms\Form;
use Gibbon\Forms\DatabaseFormFactory;
use Gibbon\Module\MasteryTranscript\Domain\JourneyGateway;
use Gibbon\Module\MasteryTranscript\Domain\JourneyLogGateway;
use Gibbon\FileUploader;

if (isActionAccessible($guid, $connection2, '/modules/Mastery Transcript/journey_record_edit.php') == false) {
    // Access denied
    $page->addError(__('You do not have access to this action.'));
} else {
    // Proceed!
    $masteryTranscriptJourneyID = $_GET['masteryTranscriptJourneyID'] ?? '';
    $search = $_GET['search'] ?? '';

    $page->breadcrumbs
        ->add(__m('Record Journey'), 'journey_record.php');

    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], null, null);
    }

    $search = $_GET['search'] ?? '';

    if (empty($masteryTranscriptJourneyID)) {
        $page->addError(__('You have not specified one or more required parameters.'));
        return;
    }

    $result = $container->get(JourneyGateway::class)->selectJourneyByID($masteryTranscriptJourneyID);

    if ($result->rowCount() != 1) {
        $page->addError(__('The specified record cannot be found.'));
        return;
    }

    $values = $result->fetch();

    $page->breadcrumbs
        ->add($values['name']." (".$values['status'].")");

    if ($search !='') {
        echo "<div class='linkTop'>";
        echo "<a href='".$_SESSION[$guid]['absoluteURL']."/index.php?q=/modules/Mastery Transcript/journey_record.php&search=$search'>".('Back to Search Results')."</a>";
        echo "</div>";
    }

    //Render log
    $journeyLogGateway = $container->get(JourneyLogGateway::class);
    $logs = $journeyLogGateway->selectJourneyLogByJourney($masteryTranscriptJourneyID);
    if ($logs->rowCount() < 1) {
        $page->addMessage(__m('The conversation has not yet begun.'), 'warning');
    }
    else {
        echo "<h2>".__m('Conversation Log')."</h2>";
        while ($log = $logs->fetch()) {
            echo $page->fetchFromTemplate('logEntry.twig.html', [
                'log' => $log
            ]);
        }
    }

    //New log form
    if ($values['status'] != 'Current - Pending') {
        echo "<h2>".__m('New Entry')."</h2>";
        $form = Form::create('log', $gibbon->session->get('absoluteURL').'/modules/'.$gibbon->session->get('module')."/journey_record_editProcess.php?masteryTranscriptJourneyID=$masteryTranscriptJourneyID&search=$search");
        $form->setFactory(DatabaseFormFactory::create($pdo));

        $form->addHiddenValue('address', $gibbon->session->get('address'));

        $types = array(
            'Comment' => __m('Comment'),
        );
        if ($values['status'] != 'Complete - Approved') {
            $types['Evidence'] = __m('Evidence');
        }
        $row = $form->addRow();
            $row->addLabel('type', __('Type'));
            $row->addSelect('type')->required()->fromArray($types)->placeholder()->required();

        $row = $form->addRow();
            $column = $row->addColumn();
            $column->addLabel('comment', __m('Comment'));
            $column->addEditor('comment', $guid)->setRows(15)->showMedia()->required();

        $form->toggleVisibilityByClass('evidence')->onSelect('type')->when('Evidence');
        $form->toggleVisibilityByClass('evidenceLink')->onSelect('evidenceType')->when('Link');
        $form->toggleVisibilityByClass('evidenceFile')->onSelect('evidenceType')->when('File');

        $evidenceTypes = array(
            'Link' => __m('Link'),
            'File' => __m('File'),
        );
        $row = $form->addRow()->addClass('evidence');
            $row->addLabel('evidenceType', __('Evidence Type'));
            $row->addSelect('evidenceType')->fromArray($evidenceTypes)->placeholder()->required();

        $row = $form->addRow()->addClass('evidenceLink');
            $row->addLabel('evidenceLink', __('Link'));
            $row->addURL('evidenceLink')->required()->required();

        $fileUploader = new FileUploader($pdo, $gibbon->session);
        $row = $form->addRow()->addClass('evidenceFile');
            $row->addLabel('evidenceFile', __('File'));
            $row->addFileUpload('evidenceFile')->required();

        $row = $form->addRow();
            $row->addFooter();
            $row->addSubmit();

        echo $form->getOutput();
    }

}
