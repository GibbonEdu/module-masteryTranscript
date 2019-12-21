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

class OpportunityGateway extends QueryableGateway
{
    use TableAware;

    private static $tableName = 'masteryTranscriptOpportunity';
    private static $primaryKey = 'masteryTranscriptOpportunityID';
    private static $searchableColumns = ['masteryTranscriptOpportunity.name'];

    /**
     * @param QueryCriteria $criteria
     * @param bool $inactive
     * @return DataSet
     */
    public function queryOpportunities(QueryCriteria $criteria, $all = true)
    {
        $query = $this
            ->newQuery()
            ->cols(['*', '(SELECT GROUP_CONCAT(gibbonYearGroup.nameShort ORDER BY gibbonYearGroup.sequenceNumber SEPARATOR \', \') FROM gibbonYearGroup WHERE FIND_IN_SET(gibbonYearGroup.gibbonYearGroupID, masteryTranscriptOpportunity.gibbonYearGroupIDList)) as yearGroups'])
            ->from($this->getTableName());

        if (!$all) {
            $query->where('active=:active')
                  ->bindValue('active', 'Y');
        }

        return $this->runQuery($query, $criteria);
    }

    public function selectOpportunityByID(int $masteryTranscriptOpportunityID)
    {
        $query = $this
            ->newQuery()
            ->cols(['*', '(SELECT GROUP_CONCAT(gibbonYearGroup.nameShort ORDER BY gibbonYearGroup.sequenceNumber SEPARATOR \', \') FROM gibbonYearGroup WHERE FIND_IN_SET(gibbonYearGroup.gibbonYearGroupID, masteryTranscriptOpportunity.gibbonYearGroupIDList)) as yearGroups'])
            ->from($this->getTableName())
            ->where('masteryTranscriptOpportunity.masteryTranscriptOpportunityID = :masteryTranscriptOpportunityID')
            ->bindValue('masteryTranscriptOpportunityID', $masteryTranscriptOpportunityID);

        return $this->runSelect($query);
    }
}
