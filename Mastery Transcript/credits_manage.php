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
use Gibbon\Module\MasteryTranscript\Domain\CreditGateway;
use Gibbon\Module\MasteryTranscript\Domain\DomainGateway;

if (isActionAccessible($guid, $connection2, '/modules/Mastery Transcript/credits_manage.php') == false) {
    // Access denied
    $page->addError(__('You do not have access to this action.'));
} else {
    // Proceed!
    $page->breadcrumbs
        ->add(__m('Manage Credits'));

    //Filter
    $masteryTranscriptDomainID = $_GET['masteryTranscriptDomainID'] ?? '';
    $search = $_GET['search'] ?? '';

    $form = Form::create('search', $session->get('absoluteURL').'/index.php', 'get');
    $form->setTitle(__('Filter'));
    $form->setClass('noIntBorder fullWidth');

    $form->addHiddenValue('q', '/modules/'.$session->get('module').'/credits_manage.php');

    $domainGateway = $container->get(DomainGateway::class);
    $domains = $domainGateway->selectActiveDomains()->fetchKeyPair();

    $row = $form->addRow();
        $row->addLabel('masteryTranscriptDomainID', __('Domain'));
        $row->addSelect('masteryTranscriptDomainID')->fromArray($domains)->placeholder()->selected($masteryTranscriptDomainID);

    $row = $form->addRow();
        $row->addLabel('search', __('Search'));
        $row->addTextField('search')->setValue($search);

    $row = $form->addRow();
        $row->addSearchSubmit($session, __('Clear Search'));

    echo $form->getOutput();

    // Query categories
    $creditGateway = $container->get(CreditGateway::class);

    $criteria = $creditGateway->newQueryCriteria()
        ->searchBy($creditGateway->getSearchableColumns(), $search)
        ->filterBy('masteryTranscriptDomainID', $masteryTranscriptDomainID)
        ->sortBy(['sequenceNumber','masteryTranscriptDomain.name'])
        ->fromPOST();

    $credits = $creditGateway->queryCredits($criteria);

    // Render table
    $table = DataTable::createPaginated('credits', $criteria);

    $table->addHeaderAction('add', __('Add'))
        ->addParam('masteryTranscriptDomainID', $masteryTranscriptDomainID)
        ->addParam('search', $search)
        ->setURL('/modules/Mastery Transcript/credits_manage_add.php')
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

    $table->addColumn('domain', __('Domain'));

    $table->addColumn('level', __('Level'));

    $table->addColumn('name', __('Name'));

    $table->addColumn('active', __m('Active'));

    // ACTIONS
    $table->addActionColumn()
        ->addParam('masteryTranscriptCreditID')
        ->addParam('masteryTranscriptDomainID', $masteryTranscriptDomainID)
        ->addParam('search', $search)
        ->format(function ($category, $actions) {
            $actions->addAction('edit', __('Edit'))
                    ->setURL('/modules/Mastery Transcript/credits_manage_edit.php');

            $actions->addAction('delete', __('Delete'))
                    ->setURL('/modules/Mastery Transcript/credits_manage_delete.php');
        });

    echo $table->render($credits);
}
