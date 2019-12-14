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

if (isActionAccessible($guid, $connection2, '/modules/Mastery Transcript/journey_record.php') == false) {
    // Access denied
    $page->addError(__('You do not have access to this action.'));
} else {
    // Proceed!
    $page->breadcrumbs
        ->add(__m('Record Journey'));

    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], null, null);
    }

    //Filter
    $search = $_GET['search'] ?? '';

    $form = Form::create('search', $_SESSION[$guid]['absoluteURL'].'/index.php', 'get');
    $form->setTitle(__('Filter'));
    $form->setClass('noIntBorder fullWidth');

    $form->addHiddenValue('q', '/modules/'.$_SESSION[$guid]['module'].'/journey_record.php');

    $row = $form->addRow();
        $row->addLabel('search', __('Search'));
        $row->addTextField('search')->setValue($search);

    $row = $form->addRow();
        $row->addSearchSubmit($gibbon->session, __('Clear Search'));

    echo $form->getOutput();

    // Query categories
    $journeyGateway = $container->get(JourneyGateway::class);

    $criteria = $journeyGateway->newQueryCriteria()
        ->searchBy($journeyGateway->getSearchableColumns(), $search)
        ->sortBy('timestampJoined', 'DESC')
        ->fromPOST();

    $journey = $journeyGateway->selectJourneyByStudent($criteria, $gibbon->session->get('gibbonPersonID'));

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
                $journey['statusClass'] = 'warning';
                break;
            case 'Current - Pending':
                $journey['statusClass'] = 'warning';
                break;
            case 'Complete - Pending':
                $journey['statusClass'] = 'pending';
                break;
            case 'Evidence Not Yet Approved':
                $journey['statusClass'] = 'error';
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

    $table->addColumn('logo', __('Logo'))
    ->notSortable()
    ->format(function($values) use ($guid) {
        $return = null;
        $return .= ($values['logo'] != '') ? "<img class='user' style='max-width: 75px' src='".$_SESSION[$guid]['absoluteURL'].'/'.$values['logo']."'/>":"<img class='user' style='max-width: 75px' src='".$_SESSION[$guid]['absoluteURL'].'/themes/'.$_SESSION[$guid]['gibbonThemeName']."/img/anonymous_240_square.jpg'/>";
        return $return;
    });

    $table->addColumn('name', __('Name'));

    $table->addColumn('status', __m('Status'));

    // ACTIONS
    $table->addActionColumn()
        ->addParam('masteryTranscriptJourneyID')
        ->addParam('search', $search)
        ->format(function ($category, $actions) {
            $actions->addAction('edit', __('Edit'))
                    ->setURL('/modules/Mastery Transcript/journey_record_edit.php');

            $actions->addAction('delete', __('Delete'))
                    ->setURL('/modules/Mastery Transcript/journey_record_delete.php');
        });

    echo $table->render($journey);
}
