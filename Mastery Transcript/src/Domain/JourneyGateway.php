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

namespace Gibbon\Module\MasteryTranscript\Domain;

use Gibbon\Domain\Traits\TableAware;
use Gibbon\Domain\QueryCriteria;
use Gibbon\Domain\QueryableGateway;

class JourneyGateway extends QueryableGateway
{
    use TableAware;

    private static $tableName = 'masteryTranscriptJourney';
    private static $primaryKey = 'masteryTranscriptJourneyID';
    private static $searchableColumns = ['name'];

    public function selectJourneyByStudent(QueryCriteria $criteria, $gibbonPersonID)
    {
        $query = $this
            ->newQuery()
            ->cols(['masteryTranscriptJourney.*', '\'Credit\' AS type', 'masteryTranscriptCredit.name AS name', 'logo'])
            ->from($this->getTableName())
            ->innerJoin('gibbonPerson', 'masteryTranscriptJourney.gibbonPersonIDStudent=gibbonPerson.gibbonPersonID')
            ->innerJoin('masteryTranscriptCredit','masteryTranscriptJourney.masteryTranscriptCreditID=masteryTranscriptCredit.masteryTranscriptCreditID AND type=\'Credit\'')
            ->where('masteryTranscriptJourney.gibbonPersonIDStudent = :gibbonPersonID')
            ->bindValue('gibbonPersonID', $gibbonPersonID);

        $this->unionAllWithCriteria($query, $criteria)
            ->cols(['masteryTranscriptJourney.*', '\'Opportunity\' AS type', 'masteryTranscriptOpportunity.name AS name', 'logo'])
            ->from($this->getTableName())
            ->innerJoin('gibbonPerson', 'masteryTranscriptJourney.gibbonPersonIDStudent=gibbonPerson.gibbonPersonID')
            ->innerJoin('masteryTranscriptOpportunity','masteryTranscriptJourney.masteryTranscriptOpportunityID=masteryTranscriptOpportunity.masteryTranscriptOpportunityID AND type=\'Opportunity\'')
            ->where('masteryTranscriptJourney.gibbonPersonIDStudent = :gibbonPersonID')
            ->bindValue('gibbonPersonID', $gibbonPersonID);

        return $this->runQuery($query, $criteria);
    }

    public function selectJourneyByStaff(QueryCriteria $criteria, $gibbonPersonID, $highestAction)
    {
        if ($highestAction == 'Manage Journey_all') {
            $query = $this
                ->newQuery()
                ->cols(['masteryTranscriptJourney.*', '\'Credit\' AS type', 'masteryTranscriptCredit.name AS name', 'logo', 'surname', 'preferredName'])
                ->from($this->getTableName())
                ->innerJoin('gibbonPerson', 'masteryTranscriptJourney.gibbonPersonIDStudent=gibbonPerson.gibbonPersonID')
                ->innerJoin('masteryTranscriptCredit','masteryTranscriptJourney.masteryTranscriptCreditID=masteryTranscriptCredit.masteryTranscriptCreditID AND type=\'Credit\'');

            $this->unionAllWithCriteria($query, $criteria)
                ->cols(['masteryTranscriptJourney.*', '\'Opportunity\' AS type', 'masteryTranscriptOpportunity.name AS name', 'logo', 'surname', 'preferredName'])
                ->from($this->getTableName())
                ->innerJoin('gibbonPerson', 'masteryTranscriptJourney.gibbonPersonIDStudent=gibbonPerson.gibbonPersonID')
                ->innerJoin('masteryTranscriptOpportunity','masteryTranscriptJourney.masteryTranscriptOpportunityID=masteryTranscriptOpportunity.masteryTranscriptOpportunityID AND type=\'Opportunity\'');

        }
        else {
            $query = $this
                ->newQuery()
                ->cols(['masteryTranscriptJourney.*', '\'Credit\' AS type', 'masteryTranscriptCredit.name AS name', 'logo', 'surname', 'preferredName'])
                ->from($this->getTableName())
                ->innerJoin('gibbonPerson', 'masteryTranscriptJourney.gibbonPersonIDStudent=gibbonPerson.gibbonPersonID')
                ->innerJoin('masteryTranscriptCredit','masteryTranscriptJourney.masteryTranscriptCreditID=masteryTranscriptCredit.masteryTranscriptCreditID AND type=\'Credit\'')
                ->where('masteryTranscriptJourney.gibbonPersonIDSchoolMentor = :gibbonPersonID')
                ->bindValue('gibbonPersonID', $gibbonPersonID);

            $this->unionAllWithCriteria($query, $criteria)
                ->cols(['masteryTranscriptJourney.*', '\'Opportunity\' AS type', 'masteryTranscriptOpportunity.name AS name', 'logo', 'surname', 'preferredName'])
                ->from($this->getTableName())
                ->innerJoin('gibbonPerson', 'masteryTranscriptJourney.gibbonPersonIDStudent=gibbonPerson.gibbonPersonID')
                ->innerJoin('masteryTranscriptOpportunity','masteryTranscriptJourney.masteryTranscriptOpportunityID=masteryTranscriptOpportunity.masteryTranscriptOpportunityID AND type=\'Opportunity\'')
                ->where('masteryTranscriptJourney.gibbonPersonIDSchoolMentor = :gibbonPersonID')
                ->bindValue('gibbonPersonID', $gibbonPersonID);
        }

        return $this->runQuery($query, $criteria);
    }

    public function selectJourneyByID($masteryTranscriptJourneyID, $statusKey = null)
    {
        if (empty($statusKey)) {
            $query = $this
                ->newQuery()
                ->cols(['masteryTranscriptJourney.*', '\'Credit\' AS type', 'masteryTranscriptCredit.name AS name', 'logo', 'surname', 'preferredName'])
                ->from($this->getTableName())
                ->innerJoin('gibbonPerson', 'masteryTranscriptJourney.gibbonPersonIDStudent=gibbonPerson.gibbonPersonID')
                ->innerJoin('masteryTranscriptCredit','masteryTranscriptJourney.masteryTranscriptCreditID=masteryTranscriptCredit.masteryTranscriptCreditID AND type=\'Credit\'')
                ->where('masteryTranscriptJourneyID = :masteryTranscriptJourneyID')
                ->bindValue('masteryTranscriptJourneyID', $masteryTranscriptJourneyID);

            $query->unionAll()
                ->cols(['masteryTranscriptJourney.*', '\'Opportunity\' AS type', 'masteryTranscriptOpportunity.name AS name', 'logo', 'surname', 'preferredName'])
                ->from($this->getTableName())
                ->innerJoin('gibbonPerson', 'masteryTranscriptJourney.gibbonPersonIDStudent=gibbonPerson.gibbonPersonID')
                ->innerJoin('masteryTranscriptOpportunity','masteryTranscriptJourney.masteryTranscriptOpportunityID=masteryTranscriptOpportunity.masteryTranscriptOpportunityID AND type=\'Opportunity\'')
                ->where('masteryTranscriptJourneyID = :masteryTranscriptJourneyID')
                ->bindValue('masteryTranscriptJourneyID', $masteryTranscriptJourneyID);
        }
        else {
            $query = $this
                ->newQuery()
                ->cols(['masteryTranscriptJourney.*', '\'Credit\' AS type', 'masteryTranscriptCredit.name AS name', 'logo', 'surname', 'preferredName'])
                ->from($this->getTableName())
                ->innerJoin('gibbonPerson', 'masteryTranscriptJourney.gibbonPersonIDStudent=gibbonPerson.gibbonPersonID')
                ->innerJoin('masteryTranscriptCredit','masteryTranscriptJourney.masteryTranscriptCreditID=masteryTranscriptCredit.masteryTranscriptCreditID AND type=\'Credit\'')
                ->where('masteryTranscriptJourneyID = :masteryTranscriptJourneyID AND statusKey = :statusKey')
                ->bindValue('masteryTranscriptJourneyID', $masteryTranscriptJourneyID)
                ->bindValue('statusKey', $statusKey);

            $query->unionAll()
                ->cols(['masteryTranscriptJourney.*', '\'Opportunity\' AS type', 'masteryTranscriptOpportunity.name AS name', 'logo', 'surname', 'preferredName'])
                ->from($this->getTableName())
                ->innerJoin('gibbonPerson', 'masteryTranscriptJourney.gibbonPersonIDStudent=gibbonPerson.gibbonPersonID')
                ->innerJoin('masteryTranscriptOpportunity','masteryTranscriptJourney.masteryTranscriptOpportunityID=masteryTranscriptOpportunity.masteryTranscriptOpportunityID AND type=\'Opportunity\'')
                ->where('masteryTranscriptJourneyID = :masteryTranscriptJourneyID AND statusKey = :statusKey')
                ->bindValue('masteryTranscriptJourneyID', $masteryTranscriptJourneyID)
                ->bindValue('statusKey', $statusKey);
        }

        return $this->runSelect($query);
    }

    public function selectEvidencePending(QueryCriteria $criteria, $gibbonPersonID = null)
    {
        if (is_null($gibbonPersonID)) {
            $query = $this
                ->newQuery()
                ->cols(['masteryTranscriptJourneyID', 'student.surname AS studentsurname', 'student.preferredName AS studentpreferredName', 'mentor.title AS mentortitle', 'mentor.surname AS mentorsurname', 'mentor.preferredName AS mentorpreferredName', 'masteryTranscriptCredit.logo', 'masteryTranscriptCredit.name', 'timestampJoined', 'type'])
                ->from($this->getTableName())
                ->innerJoin('masteryTranscriptCredit', 'masteryTranscriptJourney.masteryTranscriptCreditID=masteryTranscriptCredit.masteryTranscriptCreditID')
                ->innerJoin('gibbonPerson AS student','masteryTranscriptJourney.gibbonPersonIDStudent=student.gibbonPersonID')
                ->innerJoin('gibbonPerson AS mentor','masteryTranscriptJourney.gibbonPersonIDSchoolMentor=mentor.gibbonPersonID')
                ->where('type=\'Credit\' AND masteryTranscriptJourney.status=\'Complete - Pending\' AND student.status=\'Full\' AND (student.dateStart IS NULL OR student.dateStart<=:date) AND (student.dateEnd IS NULL OR student.dateEnd>=:date)')
                ->bindValue('gibbonPersonID', $gibbonPersonID)
                ->bindValue('date', date("Y-m-d"));

            $this->unionAllWithCriteria($query, $criteria)
                ->cols(['masteryTranscriptJourneyID', 'student.surname AS studentsurname', 'student.preferredName AS studentpreferredName', 'mentor.title AS mentortitle', 'mentor.surname AS mentorsurname', 'mentor.preferredName AS mentorpreferredName', 'masteryTranscriptOpportunity.logo', 'masteryTranscriptOpportunity.name', 'timestampJoined', 'type'])
                ->from($this->getTableName())
                ->innerJoin('masteryTranscriptOpportunity', 'masteryTranscriptJourney.masteryTranscriptOpportunityID=masteryTranscriptOpportunity.masteryTranscriptOpportunityID')
                ->innerJoin('gibbonPerson AS student','masteryTranscriptJourney.gibbonPersonIDStudent=student.gibbonPersonID')
                ->innerJoin('gibbonPerson AS mentor','masteryTranscriptJourney.gibbonPersonIDSchoolMentor=mentor.gibbonPersonID')
                ->where('type=\'Opportunity\' AND masteryTranscriptJourney.status=\'Complete - Pending\' AND student.status=\'Full\' AND (student.dateStart IS NULL OR student.dateStart<=:date) AND (student.dateEnd IS NULL OR student.dateEnd>=:date)')
                ->bindValue('gibbonPersonID', $gibbonPersonID)
                ->bindValue('date', date("Y-m-d"));
        }
        else {
            $query = $this
                ->newQuery()
                ->cols(['masteryTranscriptJourneyID', 'student.surname AS studentsurname', 'student.preferredName AS studentpreferredName', 'mentor.title AS mentortitle', 'mentor.surname AS mentorsurname', 'mentor.preferredName AS mentorpreferredName', 'masteryTranscriptCredit.logo', 'masteryTranscriptCredit.name', 'timestampJoined', 'type'])
                ->from($this->getTableName())
                ->innerJoin('masteryTranscriptCredit', 'masteryTranscriptJourney.masteryTranscriptCreditID=masteryTranscriptCredit.masteryTranscriptCreditID')
                ->innerJoin('gibbonPerson AS student','masteryTranscriptJourney.gibbonPersonIDStudent=student.gibbonPersonID')
                ->innerJoin('gibbonPerson AS mentor','masteryTranscriptJourney.gibbonPersonIDSchoolMentor=mentor.gibbonPersonID')
                ->where('type=\'Credit\' AND masteryTranscriptJourney.status=\'Complete - Pending\' AND student.status=\'Full\' AND (student.dateStart IS NULL OR student.dateStart<=:date) AND (student.dateEnd IS NULL OR student.dateEnd>=:date) AND gibbonPersonIDSchoolMentor=:gibbonPersonIDSchoolMentor')
                ->bindValue('gibbonPersonID', $gibbonPersonID)
                ->bindValue('date', date("Y-m-d"))
                ->bindValue('gibbonPersonIDSchoolMentor', $gibbonPersonID);

            $this->unionAllWithCriteria($query, $criteria)
                ->cols(['masteryTranscriptJourneyID', 'student.surname AS studentsurname', 'student.preferredName AS studentpreferredName', 'mentor.title AS mentortitle', 'mentor.surname AS mentorsurname', 'mentor.preferredName AS mentorpreferredName', 'masteryTranscriptOpportunity.logo', 'masteryTranscriptOpportunity.name', 'timestampJoined', 'type'])
                ->from($this->getTableName())
                ->innerJoin('masteryTranscriptOpportunity', 'masteryTranscriptJourney.masteryTranscriptOpportunityID=masteryTranscriptOpportunity.masteryTranscriptOpportunityID')
                ->innerJoin('gibbonPerson AS student','masteryTranscriptJourney.gibbonPersonIDStudent=student.gibbonPersonID')
                ->innerJoin('gibbonPerson AS mentor','masteryTranscriptJourney.gibbonPersonIDSchoolMentor=mentor.gibbonPersonID')
                ->where('type=\'Opportunity\' AND masteryTranscriptJourney.status=\'Complete - Pending\' AND student.status=\'Full\' AND (student.dateStart IS NULL OR student.dateStart<=:date) AND (student.dateEnd IS NULL OR student.dateEnd>=:date) AND gibbonPersonIDSchoolMentor=:gibbonPersonIDSchoolMentor')
                ->bindValue('gibbonPersonID', $gibbonPersonID)
                ->bindValue('date', date("Y-m-d"))
                ->bindValue('gibbonPersonIDSchoolMentor', $gibbonPersonID);
        }

        return $this->runQuery($query, $criteria);
    }
}
