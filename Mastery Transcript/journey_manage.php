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
use Gibbon\Tables\DataTable;
use Gibbon\Services\Format;
use Gibbon\Module\MasteryTranscript\Domain\JourneyGateway;
use Gibbon\View\View;

$highestAction = getHighestGroupedAction($guid, '/modules/Mastery Transcript/journey_manage_commit.php', $connection2);

if (isActionAccessible($guid, $connection2, '/modules/Mastery Transcript/journey_manage.php') == false || $highestAction == false) {
    // Access denied
    $page->addError(__('You do not have access to this action.'));
} else {
    // Proceed!
    $page->breadcrumbs
        ->add(__m('Manage Journey'));

    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], null, null);
    }

    //Filter
    $search = $_GET['search'] ?? '';
    $status = $_GET['status'] ?? '';
    $gibbonPersonIDStudent = isset($_GET['gibbonPersonIDStudent'])? $_GET['gibbonPersonIDStudent'] : '';

    $form = Form::create('search', $_SESSION[$guid]['absoluteURL'].'/index.php', 'get');
    $form->setTitle(__('Filter'));
    $form->setClass('noIntBorder fullWidth');
    $form->setFactory(DatabaseFormFactory::create($pdo));

    $form->addHiddenValue('q', '/modules/'.$_SESSION[$guid]['module'].'/journey_manage.php');

    $row = $form->addRow();
        $row->addLabel('search', __('Search'));
        $row->addTextField('search')->setValue($search);

    $statuses = array(
        'Current - Pending' => __m('Current - Pending'),
        'Current' => __m('Current'),
        'Complete - Pending' => __m('Complete - Pending'),
        'Evidence Not Yet Approved' => __m('Evidence Not Yet Approved'),
        'Complete - Approved' => __m('Complete - Approved'),
    );
    $row = $form->addRow();
        $row->addLabel('status', __('Status'));
        $row->addSelect('status')->fromArray($statuses)->selected($status)->placeholder();

    $row = $form->addRow();
        $row->addLabel('gibbonPersonIDStudent',__('Student'));
        $row->addSelectStudent('gibbonPersonIDStudent', $_SESSION[$guid]['gibbonSchoolYearID'])->selected($gibbonPersonIDStudent)->placeholder();

    $row = $form->addRow();
        $row->addSearchSubmit($gibbon->session, __('Clear Search'));

    echo $form->getOutput();

    //Legend
    $templateView = new View($container->get('twig'));
    echo $templateView->fetchFromTemplate('legend.twig.html', [
        'view' => 'table'
    ]);

    //Counters
    $foundational = 0;
    $advanced = 0;
    $foundationalComplete = 0;
    $advancedComplete = 0;

    // Query categories
    $journeyGateway = $container->get(JourneyGateway::class);

    $criteria = $journeyGateway->newQueryCriteria()
        ->searchBy($journeyGateway->getSearchableColumns(), $search)
        ->filterBy('student', $gibbonPersonIDStudent)
        ->filterBy('status', $status)
        ->sortBy('timestampJoined', 'DESC')
        ->fromPOST();

    $journey = $journeyGateway->selectJourneyByStaff($criteria, $gibbon->session->get('gibbonPersonID'), $highestAction);

    // Render table
    $table = DataTable::createPaginated('opportunities', $criteria);

    //Apply colours from Free Learning
    $journey->transform(function (&$journey) {
        switch ($journey['status']) {
            case 'Complete - Approved':
            case 'Exempt':
                $journey['statusClass'] = 'success';
                break;
            case 'Current':
                $journey['statusClass'] = 'currentUnit';
                break;
            case 'Current - Pending':
                $journey['statusClass'] = 'currentPending';
                break;
            case 'Complete - Pending':
                $journey['statusClass'] = 'pending';
                break;
            case 'Evidence Not Yet Approved':
                $journey['statusClass'] = 'warning';
                break;
            default:
                $journey['statusClass']  = '';
        }
    });

    $table->modifyRows(function ($journey, $row) {
        if (!empty($journey['statusClass'])) $row->addClass($journey['statusClass']);
        return $row;
    });

    $table->addColumn('type', __('Type'));

    $table->addColumn('logo', __('Name'))
        ->notSortable()
        ->format(function($values) use ($guid, &$foundational, &$advanced, &$foundationalComplete, &$advancedComplete) {
            $return = null;
            $return .= "<div class='text-center'>";
            $return .= ($values['logo'] != '') ? "<img class='user' style='max-width: 75px' src='".$_SESSION[$guid]['absoluteURL'].'/'.$values['logo']."'/><br/>":"<img class='user' style='max-width: 75px' src='".$_SESSION[$guid]['absoluteURL'].'/themes/'.$_SESSION[$guid]['gibbonThemeName']."/img/anonymous_240_square.jpg'/><br/>";
            $return .= "<div class='mt-1 font-bold'>".$values['name']."</div>";
            if ($values['type'] == 'Credit') {
                $return .= Format::small($values['level']);

                //Update counters
                if ($values['level'] == 'Foundational') {
                    $foundational++;
                    if ($values['status'] == 'Complete - Approved') {
                        $foundationalComplete++;
                    }
                }
                else if ($values['level'] == 'Advanced') {
                    $advanced++;
                    if ($values['status'] == 'Complete - Approved') {
                        $advancedComplete++;
                    }
                }
            }
            $return .= "</div>";
            return $return;
        });

    $table->addColumn('student', __('Student'))
        ->notSortable()
        ->format(function($values) use ($guid) {
            return Format::name('', $values['preferredName'], $values['surname'], 'Student', false, true);
        });

    if ($highestAction == 'Manage Journey_all') {
        $table->addColumn('mentor', __('Mentor'))
            ->notSortable()
            ->format(function($values) use ($guid) {
                return Format::name('', $values['mentorpreferredName'], $values['mentorsurname'], 'Student', false, true);
            });
    }

    $table->addColumn('status', __m('Status'));

    // ACTIONS
    $table->addActionColumn()
        ->addParam('masteryTranscriptJourneyID')
        ->addParam('statusKey')
        ->addParam('search', $search)
        ->addParam('status', $status)
        ->addParam('gibbonPersonIDStudent', $gibbonPersonIDStudent)
        ->format(function ($category, $actions) {
            if ($category['status'] != 'Current - Pending') {
                $actions->addAction('edit', __('Edit'))
                    ->setURL('/modules/Mastery Transcript/journey_manage_edit.php');
            }
            else {
                $actions->addAction('accept', __('Accept'))
                    ->setURL('/modules/Mastery Transcript/journey_manage_commit.php');
            }
            if ($category['status'] != 'Complete - Approved') {
                $actions->addAction('delete', __('Delete'))
                    ->setURL('/modules/Mastery Transcript/journey_manage_delete.php');
            }
        });

    echo $table->render($journey);

    echo "<div class='message text-right'>";
        echo "<b>".__m('Foundational')."</b>: ".$foundationalComplete." of ".$foundational." complete<br/>";
        echo "<b>".__m('Advanced')."</b>: ".$advancedComplete." of ".$advanced." complete<br/>";
    echo "</div>";

}
