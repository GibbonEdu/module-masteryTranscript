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

namespace Gibbon\Module\MasteryTranscript\Domain;

use Gibbon\Domain\Traits\TableAware;
use Gibbon\Domain\QueryCriteria;
use Gibbon\Domain\QueryableGateway;

class OpportunityCreditGateway extends QueryableGateway
{
    use TableAware;

    private static $tableName = 'masteryTranscriptOpportunityCredit';
    private static $primaryKey = 'masteryTranscriptOpportunityCreditID';

    /**
     * @param int masteryTranscriptOpportunityID
     * @return DataSet
     */
    public function selectCreditsByOpportunity($masteryTranscriptOpportunityID)
    {
        $query = $this
            ->newQuery()
            ->cols(['masteryTranscriptOpportunityCredit.masteryTranscriptCreditID', 'masteryTranscriptCredit.name', 'backgroundColour', 'accentColour'])
            ->from($this->getTableName())
            ->innerJoin('masteryTranscriptCredit', 'masteryTranscriptOpportunityCredit.masteryTranscriptCreditID=masteryTranscriptCredit.masteryTranscriptCreditID')
            ->innerJoin('masteryTranscriptDomain', 'masteryTranscriptCredit.masteryTranscriptDomainID=masteryTranscriptDomain.masteryTranscriptDomainID')
            ->where('masteryTranscriptOpportunityID=:masteryTranscriptOpportunityID')
            ->bindValue ('masteryTranscriptOpportunityID', $masteryTranscriptOpportunityID)
            ->orderBy(['masteryTranscriptDomain.sequenceNumber', 'masteryTranscriptDomain.name', 'masteryTranscriptCredit.name']);

        return $this->runSelect($query);
    }

    /**
     * @param int masteryTranscriptOpportunityID
     * @return bool
     */
    public function deleteCreditsByOpportunity($masteryTranscriptOpportunityID)
    {
        $data = ['masteryTranscriptOpportunityID' => $masteryTranscriptOpportunityID];
        $sql = "DELETE FROM masteryTranscriptOpportunityCredit
                WHERE masteryTranscriptOpportunityID = :masteryTranscriptOpportunityID";

        return $this->db()->delete($sql, $data);
    }
}
