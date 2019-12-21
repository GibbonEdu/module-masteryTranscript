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
use Gibbon\Tables\View\GridView;
use Gibbon\Services\Format;
use Gibbon\Module\MasteryTranscript\Domain\CreditGateway;
use Gibbon\Module\MasteryTranscript\Domain\DomainGateway;

if (isActionAccessible($guid, $connection2, '/modules/Mastery Transcript/credits.php') == false) {
    // Access denied
    $page->addError(__('You do not have access to this action.'));
} else {
    // Proceed!
    $page->breadcrumbs
        ->add(__m('Browse Credits'));

    //Filter
    $masteryTranscriptDomainID = $_GET['masteryTranscriptDomainID'] ?? '';
    $search = $_GET['search'] ?? '';

    $form = Form::create('search', $_SESSION[$guid]['absoluteURL'].'/index.php', 'get');
    $form->setTitle(__('Filter'));
    $form->setClass('noIntBorder fullWidth');

    $form->addHiddenValue('q', '/modules/'.$_SESSION[$guid]['module'].'/credits.php');

    $domainGateway = $container->get(DomainGateway::class);
    $domains = $domainGateway->selectActiveDomains()->fetchKeyPair();

    $row = $form->addRow();
        $row->addLabel('masteryTranscriptDomainID', __('Domain'));
        $row->addSelect('masteryTranscriptDomainID')->fromArray($domains)->placeholder()->selected($masteryTranscriptDomainID);

    $row = $form->addRow();
        $row->addLabel('search', __('Search'));
        $row->addTextField('search')->setValue($search);

    $row = $form->addRow();
        $row->addSearchSubmit($gibbon->session, __('Clear Search'));

    echo $form->getOutput();

    // Query categories
    $creditGateway = $container->get(CreditGateway::class);

    $criteria = $creditGateway->newQueryCriteria()
        ->searchBy($creditGateway->getSearchableColumns(), $search)
        ->filterBy('masteryTranscriptDomainID', $masteryTranscriptDomainID)
        ->sortBy(['sequenceNumber','masteryTranscriptDomain.name'])
        ->fromPOST();

    $credits = $creditGateway->queryCredits($criteria, false);

    // Render table
    $gridRenderer = new GridView($container->get('twig'));
    $table = $container->get(DataTable::class)->setRenderer($gridRenderer);

    $table->setTitle(__('Credits'));

    $table->addColumn('logo', __('Logo'))
    ->notSortable()
    ->format(function($values) use ($guid, $gibbon, $search, $masteryTranscriptDomainID) {
        $return = null;
        $background = ($values['backgroundColour']) ? "; background-color: #".$values['backgroundColour'] : '';
        $font = ($values['accentColour']) ? "color: #".$values['accentColour'] : '';
        $return .= "<a class='text-black no-underline' href='".$gibbon->session->get('absoluteURL')."/index.php?q=/modules/Mastery Transcript/credits_detail.php&masteryTranscriptCreditID=".$values['masteryTranscriptCreditID']."&search=$search&$masteryTranscriptDomainID=$masteryTranscriptDomainID'><div title='".str_replace("'", "&#39;", $values['description'])."' class='text-center pb-8' style='".$background."'>";
        $return .= ($values['logo'] != '') ? "<img class='pt-10 pb-2' style='max-width: 65px' src='".$_SESSION[$guid]['absoluteURL'].'/'.$values['logo']."'/><br/>":"<img class='pt-10 pb-2' style='max-width: 65px' src='".$_SESSION[$guid]['absoluteURL'].'/themes/'.$_SESSION[$guid]['gibbonThemeName']."/img/anonymous_240_square.jpg'/><br/>";
        $return .= "<span class='font-bold underline'>".$values['name']."</span><br/>";
        $return .= "<span class='text-sm italic' style='$font'>".$values['domain']."</span><br/>";
        $return .= "</div></a>";

        return $return;
    });

    echo $table->render($credits);
}
