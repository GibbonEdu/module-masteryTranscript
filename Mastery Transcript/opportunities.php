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
use Gibbon\Module\MasteryTranscript\Domain\OpportunityGateway;
use Gibbon\Module\MasteryTranscript\Domain\OpportunityCreditGateway;

if (isActionAccessible($guid, $connection2, '/modules/Mastery Transcript/opportunities.php') == false) {
    // Access denied
    $page->addError(__('You do not have access to this action.'));
} else {
    // Proceed!
    $page->breadcrumbs
        ->add(__m('Browse Opportunities'));

    //Filter
    $search = $_GET['search'] ?? '';

    $form = Form::create('search', $_SESSION[$guid]['absoluteURL'].'/index.php', 'get');
    $form->setTitle(__('Filter'));
    $form->setClass('noIntBorder fullWidth');

    $form->addHiddenValue('q', '/modules/'.$_SESSION[$guid]['module'].'/opportunities.php');

    $row = $form->addRow();
        $row->addLabel('search', __('Search'));
        $row->addTextField('search')->setValue($search);

    $row = $form->addRow();
        $row->addSearchSubmit($gibbon->session, __('Clear Search'));

    echo $form->getOutput();

    // Query categories
    $opportunityGateway = $container->get(OpportunityGateway::class);
    $opportunityCreditGateway = $container->get(opportunityCreditGateway::class);

    $criteria = $opportunityGateway->newQueryCriteria()
        ->searchBy($opportunityGateway->getSearchableColumns(), $search)
        ->sortBy(['name'])
        ->fromPOST();

    $opportunities = $opportunityGateway->queryOpportunities($criteria, false);

    // Render table
    $gridRenderer = new GridView($container->get('twig'));
    $table = $container->get(DataTable::class)->setRenderer($gridRenderer);

    $table->setTitle(__('Opportunities'));

    $table->addColumn('logo', __('Logo'))
    ->notSortable()
    ->format(function($values) use ($guid, $opportunityCreditGateway) {
        $return = null;
        $return .= "<div title='".str_replace("'", "&#39;", $values['description'])."' class='text-center pb-2'>";
            $return .= ($values['logo'] != '') ? "<img class='pt-10 pb-2 max-w-sm' style='max-width: 105px' src='".$_SESSION[$guid]['absoluteURL'].'/'.$values['logo']."'/><br/>":"<img class='pt-10 pb-2 max-w-sm' style='max-width: 105px' src='".$_SESSION[$guid]['absoluteURL'].'/themes/'.$_SESSION[$guid]['gibbonThemeName']."/img/anonymous_240_square.jpg'/><br/>";
            $return .= "<span class='font-bold'>".$values['name']."</span><br/>";


            $return .= "<div class='text-xs italic pt-2'>";
                $return .= "<div class='w-1/2 p-2 my-1 mx-auto'>";
                $return .= (!empty($values['gibbonYearGroupIDList'])) ? $values['yearGroups'] : __('N/A') ;
                $return .= "</div>";
                $credits = $opportunityCreditGateway->selectCreditsByOpportunity($values['masteryTranscriptOpportunityID']);
                while ($credit = $credits->fetch()) {
                    $background = ($credit['backgroundColour']) ? "; background-color: #".$credit['backgroundColour'] : '';
                    $border = ($credit['accentColour']) ? "; border: 1px solid #".$credit['accentColour'] : '';
                    $font = ($credit['accentColour']) ? "color: #".$credit['accentColour'] : '';
                    $return .= "<div class='w-1/2 p-2 my-1 mx-auto' style='".$font.$background.$border."'>".$credit['name']."</div>";
                }
            $return .= "</div>";
        $return .= "</div>";

        return $return;
    });

    echo $table->render($opportunities);
}
