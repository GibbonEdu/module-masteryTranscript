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
        echo "<div class='linkTop'>";
        echo "<a href='".$_SESSION[$guid]['absoluteURL']."/index.php?q=/modules/Mastery Transcript/opportunities.php&masteryTranscriptDomainID=".$masteryTranscriptDomainID."&search=".$search."'>".('Back to Search Results')."</a>";
        echo "</div>";
    }

    echo "<table class='smallIntBorder' cellspacing='0' style='width: 100%'>";
    echo '<tr>';
    echo "<td style='width: 75%; vertical-align: middle'>";
    echo "<span style='font-size: 150%; font-weight: bold'>".$values['name'].'</span><br/>';
    echo '</td>';
    echo "<td style='width: 135%!important; vertical-align: top; text-align: right' rowspan=4>";
    if ($values['logo'] == null) {
        echo "<img style='margin: 5px; height: 125px; width: 125px' class='user' src='".$_SESSION[$guid]['absoluteURL'].'/themes/'.$_SESSION[$guid]['gibbonThemeName']."/img/anonymous_125.jpg'/><br/>";
    } else {
        echo "<img style='margin: 5px; height: 125px; width: 125px' class='user' src='".$values['logo']."'/><br/>";
    }
    echo '</td>';
    echo '</tr>';
    echo '<tr>';
    echo "<td style='padding-top: 15px; vertical-align: top'>";
    echo "<span style='font-size: 115%; font-weight: bold'>".__m('Year Groups').'</span><br/>';
    echo '<i>';
    echo (!empty($values['gibbonYearGroupIDList'])) ? $values['yearGroups'] : __('N/A');
    echo '<i>';
    echo '</td>';
    echo '</tr>';
    echo '<tr>';
    echo "<td style='padding-top: 15px; vertical-align: top'>";
    echo "<span style='font-size: 115%; font-weight: bold'>".__m('Description').'</span><br/>';
    echo '<i>'.$values['description'].'<i>';
    echo '</td>';
    echo '</tr>';
    echo '<tr>';
    echo "<td style='padding-top: 15px; vertical-align: top'>";
    echo "<span style='font-size: 115%; font-weight: bold'>".__m('Outcomes').'</span><br/>';
    echo '<i>'.$values['outcomes'].'<i>';
    echo '</td>';
    echo '</tr>';
    echo '<tr>';
    echo "<td style='padding-top: 15px; vertical-align: top'>";
    echo "<span style='font-size: 115%; font-weight: bold'>".__m('Mentors').'</span><br/>';
    $mentors = $container->get(OpportunityMentorGateway::class)->selectMentorsByOpportunity($masteryTranscriptOpportunityID);
    if ($mentors->rowCount() < 1) {
        echo __('N/A');
    }
    else {
        echo "<ul>";
        while ($mentor = $mentors->fetch()) {
            echo "<li>".Format::name($mentor['title'], $mentor['preferredName'], $mentor['surname'], 'Staff', true, true)."</li>";
        }
        echo "</ul>";
    }
    echo '</td>';
    echo '</tr>';
    echo '</table>';
}
