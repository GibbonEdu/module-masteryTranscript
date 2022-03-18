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

use Gibbon\Http\Url;
use Gibbon\Forms\Form;
use Gibbon\FileUploader;
use Gibbon\Forms\DatabaseFormFactory;
use Gibbon\Module\MasteryTranscript\Domain\DomainGateway;
use Gibbon\Module\MasteryTranscript\Domain\CreditGateway;
use Gibbon\Module\MasteryTranscript\Domain\CreditMentorGateway;

if (isActionAccessible($guid, $connection2, '/modules/Mastery Transcript/credits_manage_edit.php') == false) {
    // Access denied
    $page->addError(__('You do not have access to this action.'));
} else {
    // Proceed!
    $masteryTranscriptCreditID = $_GET['masteryTranscriptCreditID'] ?? '';
    $masteryTranscriptDomainID = $_GET['masteryTranscriptDomainID'] ?? '';
    $search = $_GET['search'] ?? '';

    $page->breadcrumbs
        ->add(__m('Manage Credits'), 'credits_manage.php')
        ->add(__m('Edit Credit'));

    if (empty($masteryTranscriptCreditID)) {
        $page->addError(__('You have not specified one or more required parameters.'));
        return;
    }

    $values = $container->get(CreditGateway::class)->getByID($masteryTranscriptCreditID);

    if (empty($values)) {
        $page->addError(__('The specified record cannot be found.'));
        return;
    }

    if ($masteryTranscriptDomainID != '' || $search !='') {
        $params = [
            "search" => $search,
            "masteryTranscriptDomainID" => $masteryTranscriptDomainID
        ];
        $page->navigator->addSearchResultsAction(Url::fromModuleRoute('Mastery Transcript', 'credits_manage.php')->withQueryParams($params));
    }

    $form = Form::create('category', $session->get('absoluteURL').'/modules/'.$session->get('module')."/credits_manage_editProcess.php?masteryTranscriptDomainID=$masteryTranscriptDomainID&search=$search");
    $form->setFactory(DatabaseFormFactory::create($pdo));

    $form->addHiddenValue('address', $session->get('address'));
    $form->addHiddenValue('masteryTranscriptCreditID', $masteryTranscriptCreditID);

    $domainGateway = $container->get(DomainGateway::class);
    $domains = $domainGateway->selectActiveDomains()->fetchKeyPair();

    $row = $form->addRow()->addHeading(__('Basic Information'));

    $row = $form->addRow();
        $row->addLabel('masteryTranscriptDomainID', __('Domain'))->description(__('Must be unique.'));
        $row->addSelect('masteryTranscriptDomainID')->required()->fromArray($domains)->placeholder();

    $row = $form->addRow();
        $row->addLabel('name', __('Name'))->description(__('Must be unique.'));
        $row->addTextField('name')->required()->maxLength(50);

    $levels = array('Foundational' => __('Foundational'), 'Advanced' => __('Advanced'));
    $row = $form->addRow();
        $row->addLabel('level', __('Level'));
        $row->addSelect('level')->fromArray($levels)->placeholder()->required();

    $row = $form->addRow();
        $column = $row->addColumn();
        $column->addLabel('description', __('Description'));
        $column->addCommentEditor('description')
            ->maxLength(300);

    $row = $form->addRow();
        $row->addLabel('active', __('Active'));
        $row->addYesNo('active')->required();

    $row = $form->addRow()->addHeading(__m('Mentorship & Completion'));

    $gibbonPersonIDList = array();
    $people = $container->get(CreditMentorGateway::class)->selectMentorsByCredit($masteryTranscriptCreditID);
    while ($person = $people->fetch()) {
        $gibbonPersonIDList[] = $person['gibbonPersonID'];
    }
    $row = $form->addRow();
        $row->addLabel('gibbonPersonID', __('Mentor'))->description(__m('Which staff can be selected as a mentor for this credit?'));
        $row->addSelectStaff('gibbonPersonID')->selectMultiple()->selected($gibbonPersonIDList);

    $row = $form->addRow();
        $column = $row->addColumn();
        $column->addLabel('outcomes', __m('Indicative Outcomes & Criteria'))->description('How can students and mentor judge progress towards completion?');
        $column->addEditor('outcomes', $guid)->setRows(15)->showMedia();

    $row = $form->addRow()->addHeading(__('Logo'));

    $fileUploader = new FileUploader($pdo, $session);
    $row = $form->addRow();
        $row->addLabel('file', __('Logo'));
        $row->addFileUpload('file')
            ->setAttachment('logo', $session->get('absoluteURL'), $values['logo'])
            ->accepts($fileUploader->getFileExtensions('Graphics/Design'));

    $row = $form->addRow();
        $row->addLabel('creditLicensing', __m('Logo Credits & Licensing'));
        $row->addTextArea('creditLicensing');

    $row = $form->addRow();
        $row->addFooter();
        $row->addSubmit();

    $form->loadAllValuesFrom($values);

    echo $form->getOutput();
}
