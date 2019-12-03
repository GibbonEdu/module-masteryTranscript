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
use Gibbon\FileUploader;

if (isActionAccessible($guid, $connection2, '/modules/Mastery Transcript/opportunities_manage_add.php') == false) {
    // Access denied
    $page->addError(__('You do not have access to this action.'));
} else {
    // Proceed!
    $search = $_GET['search'] ?? '';

    $page->breadcrumbs
        ->add(__m('Manage Opportunities'), 'opportunities_manage.php')
        ->add(__m('Add Opportunity'));

    $editLink = '';
    if (isset($_GET['editID'])) {
        $editLink = $_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/Mastery Transcript/opportunities_manage_edit.php&masteryTranscriptOpportunityID='.$_GET['editID']."&search=$search";
    }
    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], $editLink, null);
    }

    if ($search !='') {
        echo "<div class='linkTop'>";
        echo "<a href='".$_SESSION[$guid]['absoluteURL']."/index.php?q=/modules/Mastery Transcript/opportunities_manage.php&search=$search'>".('Back to Search Results')."</a>";
        echo "</div>";
    }

    $form = Form::create('domain', $gibbon->session->get('absoluteURL').'/modules/'.$gibbon->session->get('module')."/opportunities_manage_addProcess.php?search=$search");
    $form->setFactory(DatabaseFormFactory::create($pdo));

    $form->addHiddenValue('address', $gibbon->session->get('address'));

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
        $row->addFileUpload('file')->accepts($fileUploader->getFileExtensions('Graphics/Design'));

    $row = $form->addRow();
        $row->addLabel('gibbonYearGroupIDList', __('Year Groups'))->description(__('Relevant student year groups'));
        $row->addCheckboxYearGroup('gibbonYearGroupIDList')->addCheckAllNone();

    $row = $form->addRow();
        $row->addLabel('gibbonPersonID', __m('Mentor'))->description(__m('Which staff can be selected as a mentor for this opportunity?'));
        $row->addSelectStaff('gibbonPersonID')->selectMultiple();

    $sql = "SELECT masteryTranscriptCreditID AS value, masteryTranscriptCredit.name, masteryTranscriptDomain.name AS groupBy FROM masteryTranscriptCredit INNER JOIN masteryTranscriptDomain ON (masteryTranscriptCredit.masteryTranscriptDomainID=masteryTranscriptDomain.masteryTranscriptDomainID) WHERE masteryTranscriptCredit.active='Y' ORDER BY masteryTranscriptDomain.name, masteryTranscriptCredit.name";
    $row = $form->addRow();
        $row->addLabel('masteryTranscriptCreditID', __m('Available Credits'))->description(__m('Which credits might a student be eligible for?'));
        $row->addSelect('masteryTranscriptCreditID')->selectMultiple()->fromQuery($pdo, $sql, array(), 'groupBy');

    $row = $form->addRow();
        $row->addFooter();
        $row->addSubmit();

    echo $form->getOutput();
}
