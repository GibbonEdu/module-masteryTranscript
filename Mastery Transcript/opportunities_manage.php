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

use Gibbon\Forms\Form;
use Gibbon\Tables\DataTable;
use Gibbon\Services\Format;
use Gibbon\Module\MasteryTranscript\Domain\OpportunityGateway;

if (isActionAccessible($guid, $connection2, '/modules/Mastery Transcript/opportunities_manage.php') == false) {
    // Access denied
    $page->addError(__('You do not have access to this action.'));
} else {
    // Proceed!
    $page->breadcrumbs
        ->add(__m('Manage Opportunities'));

    //Filter
    $search = $_GET['search'] ?? '';

    $form = Form::create('search', $session->get('absoluteURL').'/index.php', 'get');
    $form->setTitle(__('Filter'));
    $form->setClass('noIntBorder w-full');

    $form->addHiddenValue('q', '/modules/'.$session->get('module').'/opportunities_manage.php');

    $row = $form->addRow();
        $row->addLabel('search', __('Search'));
        $row->addTextField('search')->setValue($search);

    $row = $form->addRow();
        $row->addSearchSubmit($session, __('Clear Search'));

    echo $form->getOutput();

    // Query categories
    $opportunityGateway = $container->get(OpportunityGateway::class);

    $criteria = $opportunityGateway->newQueryCriteria()
        ->searchBy($opportunityGateway->getSearchableColumns(), $search)
        ->sortBy(['name'])
        ->fromPOST();

    $domains = $opportunityGateway->queryOpportunities($criteria);

    // Render table
    $table = DataTable::createPaginated('opportunities', $criteria);

    $table->addHeaderAction('add', __('Add'))
        ->addParam('search', $search)
        ->setURL('/modules/Mastery Transcript/opportunities_manage_add.php')
        ->displayLabel();

    $table->modifyRows(function ($category, $row) {
        if ($category['active'] == 'N') $row->addClass('error');
        return $row;
    });

    $table->addExpandableColumn('description');

    $table->addColumn('logo', __('Logo'))
    ->notSortable()
    ->format(function($values) use ($session) {
        $return = null;
        $return .= ($values['logo'] != '') ? "<img class='user' style='max-width: 75px' src='".$session->get('absoluteURL').'/'.$values['logo']."'/>":"<img class='user' style='max-width: 75px' src='".$session->get('absoluteURL').'/themes/'.$session->get('gibbonThemeName')."/img/anonymous_240_square.jpg'/>";
        return $return;
    });

    $table->addColumn('name', __('Name'));

    $table->addColumn('active', __m('Active'));

    // ACTIONS
    $table->addActionColumn()
        ->addParam('masteryTranscriptOpportunityID')
        ->addParam('search', $search)
        ->format(function ($category, $actions) {
            $actions->addAction('edit', __('Edit'))
                    ->setURL('/modules/Mastery Transcript/opportunities_manage_edit.php');

            $actions->addAction('delete', __('Delete'))
                    ->setURL('/modules/Mastery Transcript/opportunities_manage_delete.php');
        });

    echo $table->render($domains);
}
