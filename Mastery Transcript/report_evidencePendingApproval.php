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
use Gibbon\Tables\DataTable;
use Gibbon\Services\Format;
use Gibbon\Module\MasteryTranscript\Domain\JourneyGateway;

//Module includes
include "./modules/" . $_SESSION[$guid]["module"] . "/moduleFunctions.php" ;

$highestAction = getHighestGroupedAction($guid, '/modules/Mastery Transcript/report_evidencePendingApproval.php', $connection2);

if (isActionAccessible($guid, $connection2, "/modules/Mastery Transcript/report_evidencePendingApproval.php")==FALSE) {
    //Acess denied
    print "<div class='error'>" ;
        print __( "You do not have access to this action.") ;
    print "</div>" ;
}
else {
    //Proceed!
    $page->breadcrumbs
         ->add(__m('Evidence Pending Approval'));

    print "<p>" ;
        print __m('This report shows all evidence that is complete, but pending approval, in all of your classes.') ;
    print "<p>" ;

    //Filter
    $allMentors = (isset($_GET['allMentors']) && $highestAction == 'Evidence Pending Approval_all') ? $_GET['allMentors'] : '';
    $search = $_GET['search'] ?? '';

    if ($highestAction == 'Evidence Pending Approval_all') {
        $form = Form::create('search', $_SESSION[$guid]['absoluteURL'].'/index.php', 'get');
        $form->setTitle(__('Filter'));
        $form->setClass('noIntBorder fullWidth');

        $form->addHiddenValue('q', '/modules/'.$_SESSION[$guid]['module'].'/report_evidencePendingApproval.php');

        $row = $form->addRow();
            $row->addLabel('allMentors', __('All Mentors'))->description(__('Include evidence pending for all mentors.'));
            $row->addCheckbox('allMentors')->setValue('on')->checked($allMentors);

        $row = $form->addRow();
            $row->addSearchSubmit($gibbon->session, __('Clear Search'));

        echo $form->getOutput();
    }

    //Table
    $journeyGateway = $container->get(JourneyGateway::class);

    $criteria = $journeyGateway->newQueryCriteria()
        ->searchBy($journeyGateway->getSearchableColumns(), $search)
        ->sortBy('timestampJoined', 'DESC')
        ->fromPOST();

    if (!empty($allMentors)) {
        $journey = $journeyGateway->selectEvidencePending($criteria);
    }
    else {
        $journey = $journeyGateway->selectEvidencePending($criteria, $gibbon->session->get('gibbonPersonID'));
    }

    // Render table
    $table = DataTable::createPaginated('opportunities', $criteria);

    $table->setTitle(__('Data'));

    $table->modifyRows(function ($journey, $row) {
        $row->addClass('pending');
        return $row;
    });

    $table->addColumn('type', __('Type'));

    $table->addColumn('logo', __('Logo'))
    ->notSortable()
    ->format(function($values) use ($guid) {
        $return = null;
        $return .= ($values['logo'] != '') ? "<img class='user' style='max-width: 75px' src='".$_SESSION[$guid]['absoluteURL'].'/'.$values['logo']."'/>":"<img class='user' style='max-width: 75px' src='".$_SESSION[$guid]['absoluteURL'].'/themes/'.$_SESSION[$guid]['gibbonThemeName']."/img/anonymous_240_square.jpg'/>";
        return $return;
    });

    $table->addColumn('name', __('Name'));

    $table->addColumn('student', __('Student'))
        ->notSortable()
        ->format(function($values) use ($guid) {
            return Format::name('', $values['studentpreferredName'], $values['studentsurname'], 'Student', false, true);
        });

    if (!empty($allMentors)) {
        $table->addColumn('mentor', __('Mentor'))
            ->notSortable()
            ->format(function($values) use ($guid) {
                return Format::name($values['mentortitle'], $values['mentorpreferredName'], $values['mentorsurname'], 'Staff', false, true);
            });
    }

    // ACTIONS
    $table->addActionColumn()
        ->addParam('masteryTranscriptJourneyID')
        ->addParam('search', $search)
        ->format(function ($category, $actions) {
            $actions->addAction('edit', __('Edit'))
                    ->setURL('/modules/Mastery Transcript/journey_manage_edit.php');
        });

    echo $table->render($journey);
}
?>
