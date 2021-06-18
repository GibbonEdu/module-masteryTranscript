<?php
//USE ;end TO SEPERATE SQL STATEMENTS. DON'T USE ;end IN ANY OTHER PLACES!

$sql = array();
$count = 0;

//v0.1.00
$sql[$count][0] = '0.1.00';
$sql[$count][1] = '-- First version, nothing to update';


//v0.2.00
$sql[$count][0] = '0.2.00';
$sql[$count][1] = "
INSERT INTO `gibbonAction` (`gibbonModuleID`, `name`, `precedence`, `category`, `description`, `URLList`, `entryURL`, `defaultPermissionAdmin`, `defaultPermissionTeacher`, `defaultPermissionStudent`, `defaultPermissionParent`, `defaultPermissionSupport`, `categoryPermissionStaff`, `categoryPermissionStudent`, `categoryPermissionParent`, `categoryPermissionOther`) VALUES ((SELECT gibbonModuleID FROM gibbonModule WHERE name='Mastery Transcript'), 'Browse Credits', 0, 'Journey', 'Allows users to view a grid of available mastery credits.', 'credits.php', 'credits.php', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y');end
INSERT INTO `gibbonPermission` (`gibbonRoleID` ,`gibbonActionID`) VALUES ('001', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Mastery Transcript' AND gibbonAction.name='Browse Credits'));end
INSERT INTO `gibbonPermission` (`gibbonRoleID` ,`gibbonActionID`) VALUES ('002', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Mastery Transcript' AND gibbonAction.name='Browse Credits'));end
INSERT INTO `gibbonPermission` (`gibbonRoleID` ,`gibbonActionID`) VALUES ('003', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Mastery Transcript' AND gibbonAction.name='Browse Credits'));end
INSERT INTO `gibbonPermission` (`gibbonRoleID` ,`gibbonActionID`) VALUES ('004', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Mastery Transcript' AND gibbonAction.name='Browse Credits'));end
INSERT INTO `gibbonPermission` (`gibbonRoleID` ,`gibbonActionID`) VALUES ('006', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Mastery Transcript' AND gibbonAction.name='Browse Credits'));end
INSERT INTO `gibbonAction` (`gibbonModuleID`, `name`, `precedence`, `category`, `description`, `URLList`, `entryURL`, `defaultPermissionAdmin`, `defaultPermissionTeacher`, `defaultPermissionStudent`, `defaultPermissionParent`, `defaultPermissionSupport`, `categoryPermissionStaff`, `categoryPermissionStudent`, `categoryPermissionParent`, `categoryPermissionOther`) VALUES ((SELECT gibbonModuleID FROM gibbonModule WHERE name='Mastery Transcript'), 'Browse Opportunities', 0, 'Journey', 'Allows users to view a grid of available learning opportunties.', 'opportunities.php', 'opportunities.php', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y');end
INSERT INTO `gibbonPermission` (`gibbonRoleID` ,`gibbonActionID`) VALUES ('001', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Mastery Transcript' AND gibbonAction.name='Browse Opportunities'));end
INSERT INTO `gibbonPermission` (`gibbonRoleID` ,`gibbonActionID`) VALUES ('002', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Mastery Transcript' AND gibbonAction.name='Browse Opportunities'));end
INSERT INTO `gibbonPermission` (`gibbonRoleID` ,`gibbonActionID`) VALUES ('003', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Mastery Transcript' AND gibbonAction.name='Browse Opportunities'));end
INSERT INTO `gibbonPermission` (`gibbonRoleID` ,`gibbonActionID`) VALUES ('004', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Mastery Transcript' AND gibbonAction.name='Browse Opportunities'));end
INSERT INTO `gibbonPermission` (`gibbonRoleID` ,`gibbonActionID`) VALUES ('006', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Mastery Transcript' AND gibbonAction.name='Browse Opportunities'));end
";

//v0.2.01
$sql[$count][0] = '0.2.01';
$sql[$count][1] = "
INSERT INTO `gibbonAction` (`gibbonModuleID`, `name`, `precedence`, `category`, `description`, `URLList`, `entryURL`, `defaultPermissionAdmin`, `defaultPermissionTeacher`, `defaultPermissionStudent`, `defaultPermissionParent`, `defaultPermissionSupport`, `categoryPermissionStaff`, `categoryPermissionStudent`, `categoryPermissionParent`, `categoryPermissionOther`) VALUES ((SELECT gibbonModuleID FROM gibbonModule WHERE name='Mastery Transcript'), 'Credits & Licensing', 0, 'Other', 'Allows a user to view image credits for licensed images.', 'logo_credits.php', 'logo_credits.php', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y', 'Y') ;end
INSERT INTO `gibbonPermission` (`permissionID` ,`gibbonRoleID` ,`gibbonActionID`) VALUES (NULL , '1', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Mastery Transcript' AND gibbonAction.name='Credits & Licensing'));end
INSERT INTO `gibbonPermission` (`permissionID` ,`gibbonRoleID` ,`gibbonActionID`) VALUES (NULL , '2', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Mastery Transcript' AND gibbonAction.name='Credits & Licensing'));end
INSERT INTO `gibbonPermission` (`permissionID` ,`gibbonRoleID` ,`gibbonActionID`) VALUES (NULL , '3', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Mastery Transcript' AND gibbonAction.name='Credits & Licensing'));end
INSERT INTO `gibbonPermission` (`permissionID` ,`gibbonRoleID` ,`gibbonActionID`) VALUES (NULL , '4', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Mastery Transcript' AND gibbonAction.name='Credits & Licensing'));end
INSERT INTO `gibbonPermission` (`permissionID` ,`gibbonRoleID` ,`gibbonActionID`) VALUES (NULL , '6', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Mastery Transcript' AND gibbonAction.name='Credits & Licensing'));end
ALTER TABLE `masteryTranscriptDomain` ADD `creditLicensing` text NOT NULL;end
ALTER TABLE `masteryTranscriptCredit` ADD `creditLicensing` text NOT NULL;end
ALTER TABLE `masteryTranscriptOpportunity` ADD `creditLicensing` text NOT NULL;end
";

//v0.2.02
$sql[$count][0] = '0.2.02';
$sql[$count][1] = "";

//v0.5.00
$sql[$count][0] = '0.5.00';
$sql[$count][1] = "
INSERT INTO `gibbonAction` (`gibbonModuleID`, `name`, `precedence`, `category`, `description`, `URLList`, `entryURL`, `defaultPermissionAdmin`, `defaultPermissionTeacher`, `defaultPermissionStudent`, `defaultPermissionParent`, `defaultPermissionSupport`, `categoryPermissionStaff`, `categoryPermissionStudent`, `categoryPermissionParent`, `categoryPermissionOther`) VALUES ((SELECT gibbonModuleID FROM gibbonModule WHERE name='Mastery Transcript'), 'Record Journey', 0, 'Journey', 'Allows a student to record steps in their journey to mastery.', 'journey_record.php,journey_record_add.php,journey_record_edit.php,journey_record_delete.php', 'journey_record.php', 'N', 'N', 'Y', 'N', 'N', 'N', 'Y', 'N', 'N') ;end
INSERT INTO `gibbonPermission` (`permissionID` ,`gibbonRoleID` ,`gibbonActionID`) VALUES (NULL , '3', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Mastery Transcript' AND gibbonAction.name='Record Journey'));end
CREATE TABLE `masteryTranscriptJourney` (`masteryTranscriptJourneyID` int(12) unsigned zerofill NOT NULL AUTO_INCREMENT, `gibbonPersonIDStudent` int(10) unsigned zerofill NULL DEFAULT NULL, `gibbonSchoolYearID` INT(3) UNSIGNED ZEROFILL NULL DEFAULT NULL, `type` enum('Credit','Opportunity') NOT NULL DEFAULT 'Credit', `masteryTranscriptOpportunityID` int(4) unsigned zerofill NULL DEFAULT NULL, `masteryTranscriptCreditID` int(4) unsigned zerofill NULL DEFAULT NULL, `gibbonPersonIDSchoolMentor` int(10) unsigned zerofill NULL DEFAULT NULL, `status` enum('Current','Current - Pending','Complete - Pending','Complete - Approved','Exempt','Evidence Not Yet Approved') NOT NULL DEFAULT 'Current', `timestampJoined` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, `timestampCompletePending` timestamp NULL DEFAULT NULL, `timestampCompleteApproved` timestamp NULL DEFAULT NULL, `gibbonPersonIDApproval` int(10) unsigned zerofill NULL DEFAULT NULL, `evidenceType` enum('File','Link') NULL DEFAULT NULL, `evidenceLocation` text NULL DEFAULT NULL, PRIMARY KEY (`masteryTranscriptJourneyID`), INDEX(`gibbonPersonIDStudent`), INDEX(`status`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;
INSERT INTO `gibbonAction` (`gibbonModuleID`, `name`, `precedence`, `category`, `description`, `URLList`, `entryURL`, `defaultPermissionAdmin`, `defaultPermissionTeacher`, `defaultPermissionStudent`, `defaultPermissionParent`, `defaultPermissionSupport`, `categoryPermissionStaff`, `categoryPermissionStudent`, `categoryPermissionParent`, `categoryPermissionOther`) VALUES ((SELECT gibbonModuleID FROM gibbonModule WHERE name='Mastery Transcript'), 'Manage Journey_all', 1, 'Journey', 'Allows a member of staff to interact with all student journey records.', 'journey_manage.php,journey_manage_edit.php,journey_manage_delete.php,journey_manage_commit.php', 'journey_manage.php', 'Y', 'N', 'N', 'N', 'N', 'Y', 'N', 'N', 'N') ;end
INSERT INTO `gibbonPermission` (`permissionID` ,`gibbonRoleID` ,`gibbonActionID`) VALUES (NULL , '1', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Mastery Transcript' AND gibbonAction.name='Manage Journey_all'));end
INSERT INTO `gibbonAction` (`gibbonModuleID`, `name`, `precedence`, `category`, `description`, `URLList`, `entryURL`, `defaultPermissionAdmin`, `defaultPermissionTeacher`, `defaultPermissionStudent`, `defaultPermissionParent`, `defaultPermissionSupport`, `categoryPermissionStaff`, `categoryPermissionStudent`, `categoryPermissionParent`, `categoryPermissionOther`) VALUES ((SELECT gibbonModuleID FROM gibbonModule WHERE name='Mastery Transcript'), 'Manage Journey_my', 0, 'Journey', 'Allows a member of staff to interact with journey records of students they mentor.', 'journey_manage.php,journey_manage_edit.php,journey_manage_delete.php,journey_manage_commit.php', 'journey_manage.php', 'N', 'Y', 'N', 'N', 'N', 'Y', 'N', 'N', 'N') ;end
INSERT INTO `gibbonPermission` (`permissionID` ,`gibbonRoleID` ,`gibbonActionID`) VALUES (NULL , '2', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Mastery Transcript' AND gibbonAction.name='Manage Journey_my'));end
ALTER TABLE `masteryTranscriptCredit` ADD `outcomes` text NOT NULL AFTER `description`;end
ALTER TABLE `masteryTranscriptOpportunity` ADD `outcomes` text NOT NULL AFTER `description`;end
UPDATE gibbonAction SET URLList='credits.php,credits_detail.php' WHERE name='Browse Credits' AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Mastery Transcript');end
UPDATE gibbonAction SET URLList='opportunities.php,opportunities_detail.php' WHERE name='Browse Opportunities' AND gibbonModuleID=(SELECT gibbonModuleID FROM gibbonModule WHERE name='Mastery Transcript');end
CREATE TABLE `masteryTranscriptJourneyLog` (`masteryTranscriptJourneyLogID` int(12) unsigned zerofill NOT NULL AUTO_INCREMENT, `masteryTranscriptJourneyID` int(10) unsigned zerofill DEFAULT NULL, `gibbonPersonID` int(10) unsigned zerofill NOT NULL, `type` enum('Comment','Evidence','Complete - Approved','Evidence Not Yet Approved') NOT NULL DEFAULT 'Comment', `comment` text NULL DEFAULT NULL, `evidenceType` enum('File','Link') NULL DEFAULT NULL, `evidenceLocation` text NULL DEFAULT NULL, `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP, PRIMARY KEY (`masteryTranscriptJourneyLogID`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;end
";

//v0.5.01
$sql[$count][0] = '0.5.01';
$sql[$count][1] = "";

//v0.5.02
$sql[$count][0] = '0.5.02';
$sql[$count][1] = "
INSERT INTO `gibbonDiscussion` (`foreignTable`, `foreignTableID`, `gibbonModuleID`, `gibbonPersonID`, `type`, `comment`, `attachmentType`, `attachmentLocation`, `timestamp`) SELECT 'masteryTranscriptJourney', masteryTranscriptJourneyLogID, (SELECT gibbonModuleID FROM gibbonModule WHERE name='Mastery Transcript'), gibbonPersonID, type, comment, evidenceType, evidenceLocation, timestamp FROM `masteryTranscriptJourneyLog`;end
DROP TABLE `masteryTranscriptJourneyLog`;end
";

//v0.5.03
$sql[$count][0] = '0.5.03';
$sql[$count][1] = "
UPDATE gibbonModule SET description='This module implements the Mastery Transcript (https://mastery.org), allowing schools to create and issue credits. Students undertake learning opportunities to earn credits, producing an evidenced portfolio, accessible via an online transcript.' WHERE name='Mastery Transcript';end
";

//v0.5.04
$sql[$count][0] = '0.5.04';
$sql[$count][1] = "";

//v0.5.05
$sql[$count][0] = '0.5.05';
$sql[$count][1] = "";

//v0.5.06
$sql[$count][0] = '0.5.06';
$sql[$count][1] = "
UPDATE gibbonModule SET description='This module allows schools to implement Mastery Transcript (https://mastery.org), with functionality to create, track and issue credits. Students undertake learning opportunities to earn credits, producing an evidenced portfolio within Gibbon. Future releases will integrate with the MTC\'s transcript platform.' WHERE name='Mastery Transcript';end
";

//v0.5.07
$sql[$count][0] = '0.5.07';
$sql[$count][1] = "
ALTER TABLE `masteryTranscriptCredit` ADD `level` enum('Foundational','Advanced') NOT NULL DEFAULT 'Foundational' AFTER `name`;end
";

//v0.5.08
$sql[$count][0] = '0.5.08';
$sql[$count][1] = "";

//v0.5.09
$sql[$count][0] = '0.5.09';
$sql[$count][1] = "";

//v0.5.10
$sql[$count][0] = '0.5.10';
$sql[$count][1] = "";

//v1.0.00
$sql[$count][0] = '1.0.00';
$sql[$count][1] = "
ALTER TABLE `masteryTranscriptJourney` ADD `statusKey` varchar(20) DEFAULT NULL AFTER `gibbonPersonIDSchoolMentor`;end
INSERT INTO `gibbonAction` (`gibbonModuleID`, `name`, `precedence`, `category`, `description`, `URLList`, `entryURL`, `defaultPermissionAdmin`, `defaultPermissionTeacher`, `defaultPermissionStudent`, `defaultPermissionParent`, `defaultPermissionSupport`, `categoryPermissionStaff`, `categoryPermissionStudent`, `categoryPermissionParent`, `categoryPermissionOther`) VALUES ((SELECT gibbonModuleID FROM gibbonModule WHERE name='Mastery Transcript'), 'Evidence Pending Approval_all', 1, 'Reports', 'Allows a user to see all evidence awaiting feedback.', 'report_evidencePendingApproval.php', 'report_evidencePendingApproval.php', 'Y', 'N', 'N', 'N', 'N', 'Y', 'N', 'N', 'N') ;end
INSERT INTO `gibbonPermission` (`permissionID` ,`gibbonRoleID` ,`gibbonActionID`) VALUES (NULL , '1', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Mastery Transcript' AND gibbonAction.name='Evidence Pending Approval_all'));end
INSERT INTO `gibbonAction` (`gibbonModuleID`, `name`, `precedence`, `category`, `description`, `URLList`, `entryURL`, `defaultPermissionAdmin`, `defaultPermissionTeacher`, `defaultPermissionStudent`, `defaultPermissionParent`, `defaultPermissionSupport`, `categoryPermissionStaff`, `categoryPermissionStudent`, `categoryPermissionParent`, `categoryPermissionOther`) VALUES ((SELECT gibbonModuleID FROM gibbonModule WHERE name='Mastery Transcript'), 'Evidence Pending Approval_my', 0, 'Reports', 'Allows a user to see evidence awaiting their feedback.', 'report_evidencePendingApproval.php', 'report_evidencePendingApproval.php', 'N', 'Y', 'N', 'N', 'N', 'Y', 'N', 'N', 'N') ;end
INSERT INTO `gibbonPermission` (`permissionID` ,`gibbonRoleID` ,`gibbonActionID`) VALUES (NULL , '2', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Mastery Transcript' AND gibbonAction.name='Evidence Pending Approval_my'));end
";

//v1.0.01
$sql[$count][0] = '1.0.01';
$sql[$count][1] = "";

//v1.0.02
$sql[$count][0] = '1.0.02';
$sql[$count][1] = "";

//v1.1.00
$sql[$count][0] = '1.1.00';
$sql[$count][1] = "";

//v1.1.01
$sql[$count][0] = '1.1.01';
$sql[$count][1] = "";

//v1.1.02
$sql[$count][0] = '1.1.02';
$sql[$count][1] = "";

//v1.1.03
$sql[$count][0] = '1.1.03';
$sql[$count][1] = "";

//v1.2.00
$sql[$count][0] = '1.2.00';
$sql[$count][1] = "";

//v1.2.01
$sql[$count][0] = '1.2.01';
$sql[$count][1] = "";

//v1.2.02
$sql[$count][0] = '1.2.02';
$sql[$count][1] = "";

//v1.3.00
$sql[$count][0] = '1.3.00';
$sql[$count][1] = "
CREATE TABLE `masteryTranscriptTranscript` (`masteryTranscriptTranscriptID` int(10) unsigned zerofill NOT NULL AUTO_INCREMENT,`gibbonPersonIDStudent` int(10) unsigned zerofill NULL DEFAULT NULL, `gibbonSchoolYearID` INT(3) UNSIGNED ZEROFILL NULL DEFAULT NULL, `status` enum('Complete') NOT NULL DEFAULT 'Complete', `code` varchar(10) NOT NULL, `date` date NOT NULL, PRIMARY KEY (`masteryTranscriptTranscriptID`), INDEX(`gibbonPersonIDStudent`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;end
INSERT INTO `gibbonAction` (`gibbonModuleID`, `name`, `precedence`, `category`, `description`, `URLList`, `entryURL`, `defaultPermissionAdmin`, `defaultPermissionTeacher`, `defaultPermissionStudent`, `defaultPermissionParent`, `defaultPermissionSupport`, `categoryPermissionStaff`, `categoryPermissionStudent`, `categoryPermissionParent`, `categoryPermissionOther`) VALUES ((SELECT gibbonModuleID FROM gibbonModule WHERE name='Mastery Transcript'), 'Manage Transcripts', 0, 'Manage', 'Manage and maintain a list of issued transcripts.', 'transcripts_manage.php,transcripts_manage_add.php,transcripts_manage_edit.php,transcripts_manage_delete.php', 'transcripts_manage.php', 'Y', 'N', 'N', 'N', 'N', 'Y', 'N', 'N', 'N');end
INSERT INTO `gibbonPermission` (`permissionID` ,`gibbonRoleID` ,`gibbonActionID`) VALUES (NULL , '1', (SELECT gibbonActionID FROM gibbonAction JOIN gibbonModule ON (gibbonAction.gibbonModuleID=gibbonModule.gibbonModuleID) WHERE gibbonModule.name='Mastery Transcript' AND gibbonAction.name='Manage Transcripts'));end
";

//v1.4.00
$sql[$count][0] = '1.4.00';
$sql[$count][1] = "
UPDATE gibbonDiscussion JOIN masteryTranscriptJourney ON (gibbonDiscussion.foreignTableID=masteryTranscriptJourney.masteryTranscriptJourneyID) SET gibbonDiscussion.gibbonPersonIDTarget=masteryTranscriptJourney.gibbonPersonIDStudent WHERE gibbonDiscussion.foreignTable='masteryTranscriptJourney';end
";

//v1.2.02
$sql[$count][0] = '1.4.01';
$sql[$count][1] = "";
