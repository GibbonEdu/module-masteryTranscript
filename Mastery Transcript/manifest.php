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
$version = '0.1.00';
$author = 'Sanda Kuipers & Ross Parker';
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

//Settings - none
//$gibbonSetting[] = "";

//Action rows
$actionRows[] = [
    'name'                      => 'Manage Domains',
    'precedence'                => '0',
    'category'                  => 'Define',
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
