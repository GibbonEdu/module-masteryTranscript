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
use Gibbon\Domain\Students\StudentGateway;

if (isActionAccessible($guid, $connection2, '/modules/Mastery Transcript/journey_record_add.php') == false) {
    // Access denied
    $page->addError(__('You do not have access to this action.'));
} else {
    // Proceed!
    $search = $_GET['search'] ?? '';

    $page->breadcrumbs
        ->add(__m('Record Journey'), 'journey_record.php')
        ->add(__m('Add'));

    $editLink = '';
    if (isset($_GET['editID'])) {
        $editLink = $_SESSION[$guid]['absoluteURL'].'/index.php?q=/modules/Mastery Transcript/journey_record_edit.php&masteryTranscriptJourneyID='.$_GET['editID']."&search=$search";
    }
    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], $editLink, null);
    }

    if ($search !='') {
        echo "<div class='linkTop'>";
        echo "<a href='".$_SESSION[$guid]['absoluteURL']."/index.php?q=/modules/Mastery Transcript/journey_record.php&search=$search'>".('Back to Search Results')."</a>";
        echo "</div>";
    }

    $form = Form::create('domain', $gibbon->session->get('absoluteURL').'/modules/'.$gibbon->session->get('module')."/journey_record_addProcess.php?search=$search");
    $form->setFactory(DatabaseFormFactory::create($pdo));

    $form->addHiddenValue('address', $gibbon->session->get('address'));

    $types = array(
        'Credit' => __m('Credit'),
        'Opportunity' => __m('Opportunity'),
    );
    $row = $form->addRow();
        $row->addLabel('type', __('Type'));
        $row->addSelect('type')->fromArray($types)->required()->placeholder();

    //Credit fields
    $form->toggleVisibilityByClass('credit')->onSelect('type')->when('Credit');

    $sql = "SELECT masteryTranscriptCreditID AS value, masteryTranscriptCredit.name, masteryTranscriptDomain.name AS groupBy FROM masteryTranscriptCredit INNER JOIN masteryTranscriptDomain ON (masteryTranscriptCredit.masteryTranscriptDomainID=masteryTranscriptDomain.masteryTranscriptDomainID) WHERE masteryTranscriptCredit.active='Y' ORDER BY masteryTranscriptDomain.sequenceNumber, masteryTranscriptDomain.name, masteryTranscriptCredit.name";
    $row = $form->addRow()->addClass('credit');
        $row->addLabel('masteryTranscriptCreditID', __m('Available Credits'))->description(__m('Which credit do you want to apply for?'));
        $row->addSelect('masteryTranscriptCreditID')->fromQuery($pdo, $sql, array(), 'groupBy')->required()->placeholder();

    $data = array();
    $sql = 'SELECT masteryTranscriptCredit.masteryTranscriptCreditID as chainedTo, CONCAT(masteryTranscriptCredit.masteryTranscriptCreditID, \'-\', gibbonPerson.gibbonPersonID) AS value, CONCAT(surname, \', \', preferredName) AS name FROM masteryTranscriptCredit JOIN masteryTranscriptCreditMentor ON (masteryTranscriptCreditMentor.masteryTranscriptCreditID=masteryTranscriptCredit.masteryTranscriptCreditID) JOIN gibbonPerson ON (masteryTranscriptCreditMentor.gibbonPersonID=gibbonPerson.gibbonPersonID) WHERE gibbonPerson.status=\'Full\' ORDER BY surname, preferredname';
    $row = $form->addRow()->addClass('credit');
        $row->addLabel('gibbonPersonIDSchoolMentor_credit', __('Mentor'));
        $row->addSelect('gibbonPersonIDSchoolMentor_credit')->setName('gibbonPersonIDSchoolMentor')->fromQueryChained($pdo, $sql, $data, 'masteryTranscriptCreditID')->required()->placeholder();

    //Opportunity fields
    $form->toggleVisibilityByClass('opportunity')->onSelect('type')->when('Opportunity');

    $studentGateway = $container->get(StudentGateway::class);
    $student = $studentGateway->selectActiveStudentByPerson($gibbon->session->get('gibbonSchoolYearID'), $gibbon->session->get('gibbonPersonID'));
    $data = array('gibbonYearGroupID' => '%'.$student->fetch()['gibbonYearGroupID'].'%');
    $sql = "SELECT masteryTranscriptOpportunityID AS value, masteryTranscriptOpportunity.name FROM masteryTranscriptOpportunity WHERE masteryTranscriptOpportunity.active='Y' AND gibbonYearGroupIDList LIKE :gibbonYearGroupID ORDER BY masteryTranscriptOpportunity.name";
    $row = $form->addRow()->addClass('opportunity');
        $row->addLabel('masteryTranscriptOpportunityID', __m('Available Opportunities'))->description(__m('Which opportunity do you want to apply for?'));
        $row->addSelect('masteryTranscriptOpportunityID')->fromQuery($pdo, $sql, $data)->required()->placeholder();

    $sql = 'SELECT masteryTranscriptOpportunity.masteryTranscriptOpportunityID as chainedTo, CONCAT(masteryTranscriptOpportunity.masteryTranscriptOpportunityID, \'-\', gibbonPerson.gibbonPersonID) AS value, CONCAT(surname, \', \', preferredName) AS name FROM masteryTranscriptOpportunity JOIN masteryTranscriptOpportunityMentor ON (masteryTranscriptOpportunityMentor.masteryTranscriptOpportunityID=masteryTranscriptOpportunity.masteryTranscriptOpportunityID) JOIN gibbonPerson ON (masteryTranscriptOpportunityMentor.gibbonPersonID=gibbonPerson.gibbonPersonID) WHERE gibbonPerson.status=\'Full\' ORDER BY surname, preferredname';
    $row = $form->addRow()->addClass('opportunity');
        $row->addLabel('gibbonPersonIDSchoolMentor_opportunity', __('Mentor'));
        $row->addSelect('gibbonPersonIDSchoolMentor_opportunity')->setName('gibbonPersonIDSchoolMentor')->fromQueryChained($pdo, $sql, array(), 'masteryTranscriptOpportunityID')->required()->placeholder();

    $row = $form->addRow();
        $row->addFooter();
        $row->addSubmit();

    echo $form->getOutput();
}
