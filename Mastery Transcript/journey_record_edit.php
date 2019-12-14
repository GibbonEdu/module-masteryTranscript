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
use Gibbon\Module\MasteryTranscript\Domain\OpportunityGateway;
use Gibbon\Module\MasteryTranscript\Domain\OpportunityMentorGateway;
use Gibbon\Module\MasteryTranscript\Domain\OpportunityCreditGateway;
use Gibbon\FileUploader;

if (isActionAccessible($guid, $connection2, '/modules/Mastery Transcript/journey_record_edit.php') == false) {
    // Access denied
    $page->addError(__('You do not have access to this action.'));
} else {
    // Proceed!
    $masteryTranscriptOpportunityID = $_GET['masteryTranscriptOpportunityID'] ?? '';
    $search = $_GET['search'] ?? '';

    $page->breadcrumbs
        ->add(__m('Manage Opportunities'), 'journey_record.php')
        ->add(__m('Edit Opportunity'));

    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], null, null);
    }

    if (empty($masteryTranscriptOpportunityID)) {
        $page->addError(__('You have not specified one or more required parameters.'));
        return;
    }

    $values = $container->get(OpportunityGateway::class)->getByID($masteryTranscriptOpportunityID);

    if (empty($values)) {
        $page->addError(__('The specified record cannot be found.'));
        return;
    }

    if ($search !='') {
        echo "<div class='linkTop'>";
        echo "<a href='".$_SESSION[$guid]['absoluteURL']."/index.php?q=/modules/Mastery Transcript/journey_record.php&search=$search'>".('Back to Search Results')."</a>";
        echo "</div>";
    }

    $form = Form::create('category', $gibbon->session->get('absoluteURL').'/modules/'.$gibbon->session->get('module')."/journey_record_editProcess.php?search=$search");
    $form->setFactory(DatabaseFormFactory::create($pdo));

    $form->addHiddenValue('address', $gibbon->session->get('address'));
    $form->addHiddenValue('masteryTranscriptOpportunityID', $masteryTranscriptOpportunityID);

    $row = $form->addRow();
        $row->addLabel('name', __('Name'))->description(__('Must be unique.'));
        $row->addTextField('name')->required()->maxLength(50);

    $row = $form->addRow();
        $row->addLabel('description', __('Description'));
        $row->addTextArea('description');

    $row = $form->addRow();
        $row->addLabel('active', __('Active'));
        $row->addYesNo('active')->required();

    $fileUploader = new FileUploader($pdo, $gibbon->session);
    $row = $form->addRow();
        $row->addLabel('file', __('Logo'));
        $row->addFileUpload('file')
            ->setAttachment('logo', $_SESSION[$guid]['absoluteURL'], $values['logo'])
            ->accepts($fileUploader->getFileExtensions('Graphics/Design'));

    $row = $form->addRow();
        $row->addLabel('creditLicensing', __m('Logo Credits & Licensing'));
        $row->addTextArea('creditLicensing');

    $row = $form->addRow();
        $row->addLabel('gibbonYearGroupIDList', __('Year Groups'))->description(__('Relevant student year groups'));
        $row->addCheckboxYearGroup('gibbonYearGroupIDList')->addCheckAllNone()->loadFromCSV($values);;

    $gibbonPersonIDList = array();
    $people = $container->get(OpportunityMentorGateway::class)->selectMentorsByOpportunity($masteryTranscriptOpportunityID);
    while ($person = $people->fetch()) {
        $gibbonPersonIDList[] = $person['gibbonPersonID'];
    }
    $row = $form->addRow();
        $row->addLabel('gibbonPersonID', __m('Mentor'))->description(__m('Which staff can be selected as a mentor for this opportunity?'));
        $row->addSelectStaff('gibbonPersonID')->selectMultiple()->selected($gibbonPersonIDList);

    $masteryTranscriptCreditIDList = array();
    $credits = $container->get(OpportunityCreditGateway::class)->selectCreditsByOpportunity($masteryTranscriptOpportunityID);
    while ($credit = $credits->fetch()) {
        $masteryTranscriptCreditIDList[] = $credit['masteryTranscriptCreditID'];
    }
    $sql = "SELECT masteryTranscriptCreditID AS value, masteryTranscriptCredit.name, masteryTranscriptDomain.name AS groupBy FROM masteryTranscriptCredit INNER JOIN masteryTranscriptDomain ON (masteryTranscriptCredit.masteryTranscriptDomainID=masteryTranscriptDomain.masteryTranscriptDomainID) WHERE masteryTranscriptCredit.active='Y' ORDER BY masteryTranscriptDomain.sequenceNumber, masteryTranscriptDomain.name, masteryTranscriptCredit.name";
    $row = $form->addRow();
        $row->addLabel('masteryTranscriptCreditID', __m('Available Credits'))->description(__m('Which credits might a student be eligible for?'));
        $row->addSelect('masteryTranscriptCreditID')->selectMultiple()->fromQuery($pdo, $sql, array(), 'groupBy')->selected($masteryTranscriptCreditIDList);

    $row = $form->addRow();
        $row->addFooter();
        $row->addSubmit();

    $form->loadAllValuesFrom($values);

    echo $form->getOutput();
}
