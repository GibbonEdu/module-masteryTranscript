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

class JourneyLogGateway extends QueryableGateway
{
    use TableAware;

    private static $tableName = 'masteryTranscriptJourneyLog';
    private static $primaryKey = 'masteryTranscriptJourneyLogID';

    public function selectJourneyLogByJourney($masteryTranscriptJourneyID)
    {
        $query = $this
            ->newQuery()
            ->cols(['masteryTranscriptJourneyLog.*', 'surname', 'preferredName', 'image_240'])
            ->from($this->getTableName())
            ->innerJoin('gibbonPerson', 'masteryTranscriptJourneyLog.gibbonPersonID=gibbonPerson.gibbonPersonID')
            ->where('masteryTranscriptJourneyID = :masteryTranscriptJourneyID')
            ->bindValue('masteryTranscriptJourneyID', $masteryTranscriptJourneyID)
            ->orderBy(['timestamp']);

        return $this->runSelect($query);
    }
}
