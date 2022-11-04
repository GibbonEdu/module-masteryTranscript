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
use Gibbon\View\View;

if (isActionAccessible($guid, $connection2, '/modules/Mastery Transcript/journey_record.php') == false) {
    // Access denied
    $page->addError(__('You do not have access to this action.'));
} else {
    // Proceed!
    $page->breadcrumbs
        ->add(__m('Record Journey'));

    //Filter
    $search = $_GET['search'] ?? '';

    $form = Form::create('search', $session->get('absoluteURL').'/index.php', 'get');
    $form->setTitle(__('Filter'));
    $form->setClass('noIntBorder fullWidth');

    $form->addHiddenValue('q', '/modules/'.$session->get('module').'/journey_record.php');

    $row = $form->addRow();
        $row->addLabel('search', __('Search'));
        $row->addTextField('search')->setValue($search);

    $row = $form->addRow();
        $row->addSearchSubmit($session, __('Clear Search'));

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
        ->sortBy('timestampJoined', 'DESC')
        ->fromPOST();

    $journey = $journeyGateway->selectJourneyByStudent($criteria, $session->get('gibbonPersonID'));

    // Render table
    $table = DataTable::createPaginated('opportunities', $criteria);

    $table->addHeaderAction('add', __('Add'))
        ->addParam('search', $search)
        ->setURL('/modules/Mastery Transcript/journey_record_add.php')
        ->displayLabel();

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

    $table->addColumn('logo', __('Name'))
        ->notSortable()
        ->format(function($values) use ($session, &$foundational, &$advanced, &$foundationalComplete, &$advancedComplete) {
            $return = null;
            $return .= "<div class='text-center'>";
            $return .= ($values['logo'] != '') ? "<img class='user' style='max-width: 75px' src='".$session->get('absoluteURL').'/'.$values['logo']."'/><br/>":"<img class='user' style='max-width: 75px' src='".$session->get('absoluteURL').'/themes/'.$session->get('gibbonThemeName')."/img/anonymous_240_square.jpg'/><br/>";
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

    $table->addColumn('type', __('Type'));

    $table->addColumn('status', __m('Status'));

    $canViewStaff = isActionAccessible($guid, $connection2, '/modules/Staff/staff_view_details.php');
    $table->addColumn('mentor', __m('Mentor'))
        ->format(function ($values) use ($canViewStaff) {
            return $canViewStaff
                ? Format::nameLinked($values['gibbonPersonID'], $values['title'], $values['preferredName'], $values['surname'], 'Staff')
                : Format::name($values['title'], $values['preferredName'], $values['surname'], 'Staff');
        });

    // ACTIONS
    $table->addActionColumn()
        ->addParam('masteryTranscriptJourneyID')
        ->addParam('search', $search)
        ->format(function ($category, $actions) {
            if ($category['status'] != 'Current - Pending') {
                $actions->addAction('edit', __('Edit'))
                    ->setURL('/modules/Mastery Transcript/journey_record_edit.php');
            }
            if ($category['status'] != 'Complete - Approved') {
                $actions->addAction('delete', __('Delete'))
                    ->setURL('/modules/Mastery Transcript/journey_record_delete.php');
            }
        });

    echo $table->render($journey);

    echo "<div class='message text-right'>";
        echo "<b>".__m('Foundational')."</b>: ".$foundationalComplete." of ".$foundational." complete<br/>";
        echo "<b>".__m('Advanced')."</b>: ".$advancedComplete." of ".$advanced." complete<br/>";
    echo "</div>";
}
