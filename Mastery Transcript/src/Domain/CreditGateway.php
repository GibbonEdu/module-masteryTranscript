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

class CreditGateway extends QueryableGateway
{
    use TableAware;

    private static $tableName = 'masteryTranscriptCredit';
    private static $primaryKey = 'masteryTranscriptCreditID';
    private static $searchableColumns = ['masteryTranscriptCredit.name'];

    /**
     * @param QueryCriteria $criteria
     * @param bool $inactive
     * @return DataSet
     */
    public function queryCredits(QueryCriteria $criteria, $all = true)
    {
        $query = $this
            ->newQuery()
            ->cols(['masteryTranscriptCredit.*', 'masteryTranscriptDomain.name AS domain', 'backgroundColour', 'accentColour'])
            ->from($this->getTableName())
            ->innerJoin('masteryTranscriptDomain', 'masteryTranscriptCredit.masteryTranscriptDomainID=masteryTranscriptDomain.masteryTranscriptDomainID');

        if (!$all) {
            $query->where('masteryTranscriptCredit.active=:active')
                  ->bindValue('active', 'Y');
        }

        $criteria->addFilterRules([
            'masteryTranscriptDomainID' => function ($query, $masteryTranscriptDomainID) {
                return $query
                    ->where('masteryTranscriptCredit.masteryTranscriptDomainID = :masteryTranscriptDomainID')
                    ->bindValue('masteryTranscriptDomainID', $masteryTranscriptDomainID);
            }
        ]);

        return $this->runQuery($query, $criteria);
    }

    public function selectCreditByID(int $masteryTranscriptCreditID)
    {
        $query = $this
            ->newQuery()
            ->cols(['masteryTranscriptCredit.*', 'masteryTranscriptDomain.name AS domain', 'backgroundColour', 'accentColour'])
            ->from($this->getTableName())
            ->innerJoin('masteryTranscriptDomain', 'masteryTranscriptCredit.masteryTranscriptDomainID=masteryTranscriptDomain.masteryTranscriptDomainID')
            ->where('masteryTranscriptCredit.masteryTranscriptCreditID = :masteryTranscriptCreditID')
            ->bindValue('masteryTranscriptCreditID', $masteryTranscriptCreditID);

        return $this->runSelect($query);
    }
}
