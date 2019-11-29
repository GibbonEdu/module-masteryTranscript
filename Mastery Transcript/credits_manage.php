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

use Gibbon\Tables\DataTable;
use Gibbon\Services\Format;
use Gibbon\Module\MasteryTranscript\Domain\CreditGateway;

if (isActionAccessible($guid, $connection2, '/modules/Mastery Transcript/credits_manage.php') == false) {
    // Access denied
    $page->addError(__('You do not have access to this action.'));
} else {
    // Proceed!
    $page->breadcrumbs
        ->add(__m('Manage Credits'));

    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], null, null);
    }

    // Query categories
    $creditGateway = $container->get(CreditGateway::class);

    $criteria = $creditGateway->newQueryCriteria()
        ->fromPOST();

    $domains = $creditGateway->queryCredits($criteria);

    // Render table
    $table = DataTable::createPaginated('credits', $criteria);

    $table->addHeaderAction('add', __('Add'))
        ->setURL('/modules/Mastery Transcript/credits_manage_add.php')
        ->displayLabel();

    $table->modifyRows(function ($category, $row) {
        if ($category['active'] == 'N') $row->addClass('error');
        return $row;
    });

    $table->addExpandableColumn('description');

    $table->addColumn('logo', __('Logo'))
    ->notSortable()
    ->format(function($values) use ($guid) {
        $return = null;
        $return .= ($values['logo'] != '') ? "<img class='user' style='max-width: 75px' src='".$_SESSION[$guid]['absoluteURL'].'/'.$values['logo']."'/>":"<img class='user' style='max-width: 75px' src='".$_SESSION[$guid]['absoluteURL'].'/themes/'.$_SESSION[$guid]['gibbonThemeName']."/img/anonymous_240_square.jpg'/>";
        return $return;
    });

    $table->addColumn('domain', __('Domain'));

    $table->addColumn('name', __('Name'));

    $table->addColumn('active', __m('Active'));

    // ACTIONS
    $table->addActionColumn()
        ->addParam('masteryTranscriptCreditID')
        ->format(function ($category, $actions) {
            $actions->addAction('edit', __('Edit'))
                    ->setURL('/modules/Mastery Transcript/credits_manage_edit.php');

            $actions->addAction('delete', __('Delete'))
                    ->setURL('/modules/Mastery Transcript/credits_manage_delete.php');
        });

    echo $table->render($domains);
}
