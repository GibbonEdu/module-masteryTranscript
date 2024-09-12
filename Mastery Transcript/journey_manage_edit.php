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

use Gibbon\Http\Url;
use Gibbon\View\View;
use Gibbon\Forms\Form;
use Gibbon\FileUploader;
use Gibbon\Forms\DatabaseFormFactory;
use Gibbon\Domain\System\DiscussionGateway;
use Gibbon\Module\MasteryTranscript\Domain\JourneyGateway;

$highestAction = getHighestGroupedAction($guid, '/modules/Mastery Transcript/journey_manage_edit.php', $connection2);

if (isActionAccessible($guid, $connection2, '/modules/Mastery Transcript/journey_manage_edit.php') == false || $highestAction == false) {
    // Access denied
    $page->addError(__('You do not have access to this action.'));
} else {
    // Proceed!
    $masteryTranscriptJourneyID = $_GET['masteryTranscriptJourneyID'] ?? '';
    $search = $_GET['search'] ?? '';
    $status = $_GET['status'] ?? '';
    $gibbonPersonIDStudent = isset($_GET['gibbonPersonIDStudent'])? $_GET['gibbonPersonIDStudent'] : '';

    $page->breadcrumbs
        ->add(__m('Manage Journey'), 'journey_manage.php');

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

    if ($search !='' || $status !='' || $gibbonPersonIDStudent !='') {
        $params = [
            "search" => $search,
            "status" => $status,
            "gibbonPersonIDStudent" => $gibbonPersonIDStudent
        ];
        $page->navigator->addSearchResultsAction(Url::fromModuleRoute('Mastery Transcript', 'journey_manage.php')->withQueryParams($params));
    }

    if ($highestAction != 'Manage Journey_all' && $values['gibbonPersonIDSchoolMentor'] != $session->get('gibbonPersonID')) {
        $page->addError(__('The selected record does not exist, or you do not have access to it.'));
        return;
    }

    if ($values['status'] == 'Current - Pending') {
        $page->addWarning(__m('This journey is pending mentor agreement, and so cannot be edited at this time.'));
        return;
    }

    if ($values['type'] == 'Opportunity') {
        $page->navigator->addHeaderAction('view', __m('View Opportunity Details'))
            ->setURL('/modules/Mastery Transcript/opportunities_detail.php')
            ->addParams(["masteryTranscriptOpportunityID" => $values['masteryTranscriptOpportunityID']])
            ->displayLabel();
    } else if ($values['type'] == 'Credit') {
        $page->navigator->addHeaderAction('view', __m('View Credit Details'))
            ->setURL('/modules/Mastery Transcript/credits_detail.php')
            ->addParams(["masteryTranscriptCreditID" => $values['masteryTranscriptCreditID']])
            ->displayLabel();
    }

    //Render log
    $discussionGateway = $container->get(DiscussionGateway::class);
    $logs = $discussionGateway->selectDiscussionByContext('masteryTranscriptJourney', $masteryTranscriptJourneyID);
    if ($logs->rowCount() < 1) {
        $page->addMessage(__m('The conversation has not yet begun.'), 'warning');
    }
    else {
        echo "<h2>".__m('Conversation Log')."</h2>";

        //Legend
        $templateView = new View($container->get('twig'));
        echo $templateView->fetchFromTemplate('legend.twig.html');

        while ($log = $logs->fetch()) {
            echo $page->fetchFromTemplate('logEntry.twig.html', [
                'log' => $log
            ]);
        }
    }

    //New log form
    if ($values['status'] != 'Current - Pending') {
        echo "<h2>".__m('New Entry')."</h2>";
        $form = Form::create('log', $session->get('absoluteURL').'/modules/'.$session->get('module')."/journey_manage_editProcess.php?masteryTranscriptJourneyID=$masteryTranscriptJourneyID&search=$search&status=$status&gibbonPersonIDStudent=$gibbonPersonIDStudent");
        $form->setFactory(DatabaseFormFactory::create($pdo));

        $form->addHiddenValue('address', $session->get('address'));

        $row = $form->addRow();
            $column = $row->addColumn();
            $column->addLabel('comment', __m('Comment'));
            $column->addEditor('comment', $guid)->setRows(15)->showMedia()->required();

        if ($values['status'] == 'Complete - Pending') {
            $statuses = array(
                'Complete - Approved' => __m('Complete - Approved'),
                'Evidence Not Yet Approved' => __m('Evidence Not Yet Approved'),
            );
            $row = $form->addRow();
                $row->addLabel('status', __('Approval'));
                $row->addSelect('status')->fromArray($statuses)->placeholder()->required();
        }

        $row = $form->addRow();
            $row->addFooter();
            $row->addSubmit();

        echo $form->getOutput();
    }
}
