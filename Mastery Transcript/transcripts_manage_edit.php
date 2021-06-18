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
use Gibbon\Forms\DatabaseFormFactory;
use Gibbon\Module\MasteryTranscript\Domain\TranscriptGateway;
use Gibbon\View\View;

if (isActionAccessible($guid, $connection2, '/modules/Mastery Transcript/transcripts_manage_edit.php') == false) {
    // Access denied
    $page->addError(__('You do not have access to this action.'));
} else {
    // Proceed!
    $masteryTranscriptTranscriptID = $_GET['masteryTranscriptTranscriptID'] ?? '';

    $page->breadcrumbs
        ->add(__m('Manage Transcripts'), 'transcripts_manage.php')
        ->add(__m('Edit'));

    if (isset($_GET['return'])) {
        returnProcess($guid, $_GET['return'], null, null);
    }

    if (empty($masteryTranscriptTranscriptID)) {
        $page->addError(__('You have not specified one or more required parameters.'));
        return;
    }

    $values = $container->get(TranscriptGateway::class)->getByID($masteryTranscriptTranscriptID);

    if (empty($values)) {
        $page->addError(__('The specified record cannot be found.'));
        return;
    }

    $form = Form::create('domain', $session->get('absoluteURL').'/modules/'.$session->get('module')."/transcripts_manage_editProcess.php");
    $form->setFactory(DatabaseFormFactory::create($pdo));

    $form->addHiddenValue('address', $session->get('address'));
    $form->addHiddenValue('masteryTranscriptTranscriptID', $masteryTranscriptTranscriptID);

    $row = $form->addRow();
        $row->addLabel('gibbonPersonIDStudent',__('Student'));
        $row->addSelectStudent('gibbonPersonIDStudent', $session->get('gibbonSchoolYearID'))->required()->readonly();;

    $row = $form->addRow();
        $row->addLabel('status', __('Status'));
        $row->addTextField('status')->required()->setValue('Complete')->maxLength(8)->readonly();

    $row = $form->addRow();
        $row->addLabel('code', __('Code'));
        $row->addTextField('code')->required()->maxLength(10);

    $row = $form->addRow();
        $row->addLabel('date', __('Date Complete'));
        $row->addDate('date')->required();

    $row = $form->addRow();
        $row->addFooter();
        $row->addSubmit();

    $form->loadAllValuesFrom($values);

    echo $form->getOutput();




}
