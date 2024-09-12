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
use Gibbon\Forms\Form;
use Gibbon\Services\Format;
use Gibbon\Tables\DataTable;
use Gibbon\Tables\View\GridView;
use Gibbon\Module\MasteryTranscript\Domain\OpportunityGateway;
use Gibbon\Module\MasteryTranscript\Domain\OpportunityMentorGateway;

if (isActionAccessible($guid, $connection2, '/modules/Mastery Transcript/opportunities_detail.php') == false) {
    // Access denied
    $page->addError(__('You do not have access to this action.'));
} else {
    // Proceed!
    $masteryTranscriptOpportunityID = $_GET['masteryTranscriptOpportunityID'] ?? '';
    $search = $_GET['search'] ?? '';

    $page->breadcrumbs
        ->add(__m('Browse Opportunities'), 'opportunities.php')
        ->add(__m('Opportunity Details'));

    if (empty($masteryTranscriptOpportunityID)) {
        $page->addError(__('You have not specified one or more required parameters.'));
        return;
    }

    $result = $container->get(OpportunityGateway::class)->selectOpportunityByID($masteryTranscriptOpportunityID);

    if ($result->rowCount() != 1) {
        $page->addError(__('The specified record cannot be found.'));
        return;
    }

    $values = $result->fetch();

    if ($search !='') {
        $params = [
            "search" => $search
        ];
        $page->navigator->addSearchResultsAction(Url::fromModuleRoute('Mastery Transcript', 'opportunities.php')->withQueryParams($params));
    }

    // CREDIT DETAILS TABLE
    $table = DataTable::createDetails('unitDetails');

    $table->addColumn('name', '')->addClass('col-span-2 text-lg font-bold');

    $table->addColumn('logo', '')
        ->addClass('row-span-5 text-right')
        ->format(function ($values) use ($session) {
            if ($values['logo'] == null) {
                return "<img style='margin: 5px; height: 125px; width: 125px' class='user' src='".$session->get('absoluteURL').'/themes/'.$session->get('gibbonThemeName')."/img/anonymous_125.jpg'/><br/>";
            } else {
                return "<img style='margin: 5px; height: 125px; width: 125px' class='user' src='".$values['logo']."'/><br/>";
            }
        });

    $table->addColumn('yearGroups', __('Year Groups'))
        ->addClass('col-span-2')
        ->format(function ($values) {
            return (!empty($values['gibbonYearGroupIDList'])) ? $values['yearGroups'] : __('N/A');
        });

    $table->addColumn('description', __m('Description'))->addClass('col-span-2');
    $table->addColumn('outcomes', __m('Indicative Outcomes & Criteria'))->addClass('col-span-2');

    $table->addColumn('mentors', __m('Mentors'))
        ->addClass('col-span-2')
        ->format(function ($values) use ($container, $masteryTranscriptOpportunityID) {
            $return = '';

            $mentors = $container->get(OpportunityMentorGateway::class)->selectMentorsByOpportunity($masteryTranscriptOpportunityID);
            if ($mentors->rowCount() < 1) {
                $return .= __('N/A');
            }
            else {
                $return .= "<ul>";
                while ($mentor = $mentors->fetch()) {
                    $return .= "<li>".Format::name($mentor['title'], $mentor['preferredName'], $mentor['surname'], 'Staff', true, true)."</li>";
                }
                $return .= "</ul>";
            }

            return $return;
        });

    echo $table->render([$values]);
}
