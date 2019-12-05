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
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

//This file describes the module, including database tables

//Basic variables
$name = 'Mastery Transcript';
$description = 'This module implements the Mastery Transcript (https://mastery.org), allowing schools to create and issue credits. Students undertake learning opportunities to earn credits, producing an evidenced porfolio, accessible via an online trascript.';
$entryURL = 'index.php';
$type = 'Additional';
$category = 'Assess';
$version = '0.2.00';
$author = 'Ross Parker';
$url = 'https://gibbonedu.org';

//Module tables
$moduleTables[] = "CREATE TABLE `masteryTranscriptDomain` (
  `masteryTranscriptDomainID` int(3) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `active` enum('Y','N') NOT NULL DEFAULT 'Y',
  `sequenceNumber` int(3) NOT NULL,
  `backgroundColour` varchar(6) NOT NULL DEFAULT '',
  `accentColour` varchar(6) NOT NULL DEFAULT '',
  `logo` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`masteryTranscriptDomainID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$moduleTables[] = "CREATE TABLE `masteryTranscriptCredit` (
  `masteryTranscriptCreditID` int(4) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `masteryTranscriptDomainID` int(3) unsigned zerofill NOT NULL,
  `name` varchar(50) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `active` enum('Y','N') NOT NULL DEFAULT 'Y',
  `logo` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`masteryTranscriptCreditID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$moduleTables[] = "CREATE TABLE `masteryTranscriptCreditMentor` (
  `masteryTranscriptCreditMentorID` int(6) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `masteryTranscriptCreditID` int(4) unsigned zerofill NOT NULL,
  `gibbonPersonID` int(10) unsigned zerofill NOT NULL,
  PRIMARY KEY (`masteryTranscriptCreditMentorID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$moduleTables[] = "CREATE TABLE `masteryTranscriptOpportunity` (
  `masteryTranscriptOpportunityID` int(4) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `active` enum('Y','N') NOT NULL DEFAULT 'Y',
  `logo` varchar(255) NOT NULL DEFAULT '',
  `gibbonYearGroupIDList` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`masteryTranscriptOpportunityID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$moduleTables[] = "CREATE TABLE `masteryTranscriptOpportunityMentor` (
  `masteryTranscriptOpportunityMentorID` int(6) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `masteryTranscriptOpportunityID` int(4) unsigned zerofill NOT NULL,
  `gibbonPersonID` int(10) unsigned zerofill NOT NULL,
  PRIMARY KEY (`masteryTranscriptOpportunityMentorID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

$moduleTables[] = "CREATE TABLE `masteryTranscriptOpportunityCredit` (
  `masteryTranscriptOpportunityCreditID` int(6) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `masteryTranscriptOpportunityID` int(4) unsigned zerofill NOT NULL,
  `masteryTranscriptCreditID` int(4) unsigned zerofill NOT NULL,
  PRIMARY KEY (`masteryTranscriptOpportunityCreditID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

//Settings - none
$gibbonSetting[] = "INSERT INTO `gibbonSetting` (`gibbonSettingID` ,`scope` ,`name` ,`nameDisplay` ,`description` ,`value`) VALUES (NULL , 'Mastery Transcript', 'indexText', 'Index Text', 'Welcome text for users arriving in the module.', '\"The Mastery Transcript Consortium is made up of a growing network of public and private high schools who are codesigning the Mastery Transcript, a high school transcript that supports mastery learning and reflects the unique skills, strengths, and interests of each learner. In the coming years, the MTC hopes to change the way students prepare for college, career, and life.\"<br/><br/><a href=\'https://mastery.org\' target=\'_blank\'>Mastery Transcript Consortium</a>')";

//Action rows
$actionRows[] = [
    'name'                      => 'Manage Domains',
    'precedence'                => '0',
    'category'                  => 'Manage',
    'description'               => 'Manage the domains in which credits can be situated.',
    'URLList'                   => 'domains_manage.php,domains_manage_add.php,domains_manage_edit.php,domains_manage_delete.php',
    'entryURL'                  => 'domains_manage.php',
    'entrySidebar'              => 'Y',
    'menuShow'                  => 'Y',
    'defaultPermissionAdmin'    => 'Y',
    'defaultPermissionTeacher'  => 'N',
    'defaultPermissionStudent'  => 'N',
    'defaultPermissionParent'   => 'N',
    'defaultPermissionSupport'  => 'N',
    'categoryPermissionStaff'   => 'Y',
    'categoryPermissionStudent' => 'N',
    'categoryPermissionParent'  => 'N',
    'categoryPermissionOther'   => 'N',
];

$actionRows[] = [
    'name'                      => 'Manage Credits',
    'precedence'                => '0',
    'category'                  => 'Manage',
    'description'               => 'Manage the credits towards which students come work.',
    'URLList'                   => 'credits_manage.php,credits_manage_add.php,credits_manage_edit.php,credits_manage_delete.php',
    'entryURL'                  => 'credits_manage.php',
    'entrySidebar'              => 'Y',
    'menuShow'                  => 'Y',
    'defaultPermissionAdmin'    => 'Y',
    'defaultPermissionTeacher'  => 'N',
    'defaultPermissionStudent'  => 'N',
    'defaultPermissionParent'   => 'N',
    'defaultPermissionSupport'  => 'N',
    'categoryPermissionStaff'   => 'Y',
    'categoryPermissionStudent' => 'N',
    'categoryPermissionParent'  => 'N',
    'categoryPermissionOther'   => 'N',
];

$actionRows[] = [
    'name'                      => 'Manage Opportunities',
    'precedence'                => '0',
    'category'                  => 'Manage',
    'description'               => 'Manage the learing opportunities that students can undertake.',
    'URLList'                   => 'opportunities_manage.php,opportunities_manage_add.php,opportunities_manage_edit.php,opportunities_manage_delete.php',
    'entryURL'                  => 'opportunities_manage.php',
    'entrySidebar'              => 'Y',
    'menuShow'                  => 'Y',
    'defaultPermissionAdmin'    => 'Y',
    'defaultPermissionTeacher'  => 'N',
    'defaultPermissionStudent'  => 'N',
    'defaultPermissionParent'   => 'N',
    'defaultPermissionSupport'  => 'N',
    'categoryPermissionStaff'   => 'Y',
    'categoryPermissionStudent' => 'N',
    'categoryPermissionParent'  => 'N',
    'categoryPermissionOther'   => 'N',
];

$actionRows[] = [
    'name'                      => 'Settings',
    'precedence'                => '0',
    'category'                  => 'Manage',
    'description'               => 'Control settings that adjust the way the module works.',
    'URLList'                   => 'settings.php',
    'entryURL'                  => 'settings.php',
    'entrySidebar'              => 'Y',
    'menuShow'                  => 'Y',
    'defaultPermissionAdmin'    => 'Y',
    'defaultPermissionTeacher'  => 'N',
    'defaultPermissionStudent'  => 'N',
    'defaultPermissionParent'   => 'N',
    'defaultPermissionSupport'  => 'N',
    'categoryPermissionStaff'   => 'Y',
    'categoryPermissionStudent' => 'N',
    'categoryPermissionParent'  => 'N',
    'categoryPermissionOther'   => 'N',
];

$actionRows[] = [
    'name'                      => 'Browse Credits',
    'precedence'                => '0',
    'category'                  => 'Journey',
    'description'               => 'Allows users to view a grid of available mastery credits.',
    'URLList'                   => 'credits.php',
    'entryURL'                  => 'credits.php',
    'entrySidebar'              => 'Y',
    'menuShow'                  => 'Y',
    'defaultPermissionAdmin'    => 'Y',
    'defaultPermissionTeacher'  => 'Y',
    'defaultPermissionStudent'  => 'Y',
    'defaultPermissionParent'   => 'Y',
    'defaultPermissionSupport'  => 'Y',
    'categoryPermissionStaff'   => 'Y',
    'categoryPermissionStudent' => 'Y',
    'categoryPermissionParent'  => 'Y',
    'categoryPermissionOther'   => 'Y',
];

$actionRows[] = [
    'name'                      => 'Browse Opportunities',
    'precedence'                => '0',
    'category'                  => 'Journey',
    'description'               => 'Allows users to view a grid of available learning opportunties.',
    'URLList'                   => 'opportunities.php',
    'entryURL'                  => 'opportunities.php',
    'entrySidebar'              => 'Y',
    'menuShow'                  => 'Y',
    'defaultPermissionAdmin'    => 'Y',
    'defaultPermissionTeacher'  => 'Y',
    'defaultPermissionStudent'  => 'Y',
    'defaultPermissionParent'   => 'Y',
    'defaultPermissionSupport'  => 'Y',
    'categoryPermissionStaff'   => 'Y',
    'categoryPermissionStudent' => 'Y',
    'categoryPermissionParent'  => 'Y',
    'categoryPermissionOther'   => 'Y',
];
