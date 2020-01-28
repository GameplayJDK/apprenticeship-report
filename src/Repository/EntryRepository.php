<?php
/**
 * The MIT License (MIT)
 * Copyright (c) 2020 GameplayJDK
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation the
 * rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the
 * Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
 * WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
 * COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
 * OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

namespace App\Repository;

use App\Entity\Entry;
use App\Exception\MapperException;
use App\Mapper\EntryMapper;
use DateTime;
use PDO;

/**
 * Class EntryRepository
 *
 * @package App\Repository
 */
class EntryRepository implements EntryRepositoryInterface
{
    /**
     * @var PDO
     */
    private $database;

    /**
     * @var EntryMapper
     */
    private $entryMapper;

    /**
     * EntryRepository constructor.
     * @param PDO $database
     * @param EntryMapper $entryMapper
     */
    public function __construct(PDO $database, EntryMapper $entryMapper)
    {
        $this->database = $database;
        $this->entryMapper = $entryMapper;
    }

    /**
     * @param Entry $entry
     * @return int
     */
    public function insertOne(Entry $entry): int
    {
        $query = 'INSERT INTO entry (datetime_from, datetime_to, content, issue) VALUES (:datetime_from, :datetime_to, :content, :issue)';
        $parameter = [
            EntryMapper::KEY_DATETIME_FROM,
            EntryMapper::KEY_DATETIME_TO,
            EntryMapper::KEY_CONTENT,
            EntryMapper::KEY_ISSUE,
        ];

        $data = array_intersect_key($this->entryMapper->toData($entry), array_flip($parameter));

        $statement = $this->database->prepare($query);
        $result = $statement->execute($data);

        if ($statement->rowCount() > 0 && $result) {
            return $this->database->lastInsertId();
        }

        return -1;
    }

    /**
     * @return array|Entry[]
     * @throws MapperException
     */
    public function getAll(): array
    {
        $query = 'SELECT id, datetime_from, datetime_to, content, issue FROM entry';

        $statement = $this->database->prepare($query);
        $result = $statement->execute();

        if (false !== ($array = $statement->fetchAll()) && $result) {
            return $this->entryMapper->fromDataArray($array);
        }

        return [];
    }

    /**
     * @param DateTime $datetimeFrom
     * @param DateTime $datetimeTo
     * @return array|Entry[]
     * @throws MapperException
     */
    public function getAllBetweenDatetimeFromAndDatetimeTo(DateTime $datetimeFrom, DateTime $datetimeTo): array
    {
        $query = 'SELECT id, datetime_from, datetime_to, content, issue FROM entry WHERE datetime_from BETWEEN :datetime_from_begin AND :datetime_from_end AND datetime_to BETWEEN :datetime_to_begin AND :datetime_to_end ORDER BY DATE(datetime_from), issue DESC';

        $data = [
            EntryMapper::KEY_DATETIME_FROM . '_begin' => $datetimeFrom->format(EntryMapper::DTS_FORMAT),
            EntryMapper::KEY_DATETIME_FROM . '_end' => $datetimeTo->format(EntryMapper::DTS_FORMAT),
            EntryMapper::KEY_DATETIME_TO . '_begin' => $datetimeFrom->format(EntryMapper::DTS_FORMAT),
            EntryMapper::KEY_DATETIME_TO . '_end' => $datetimeTo->format(EntryMapper::DTS_FORMAT),
        ];

        $statement = $this->database->prepare($query);
        $result = $statement->execute($data);

        if (false !== ($array = $statement->fetchAll()) && $result) {
            return $this->entryMapper->fromDataArray($array);
        }

        return [];
    }


    /**
     * @return array|Entry[]
     * @throws MapperException
     */
    public function getAllManual(): array
    {
        $query = 'SELECT id, datetime_from, datetime_to, content, issue FROM entry WHERE datetime_from != datetime_to';

        $statement = $this->database->prepare($query);
        $result = $statement->execute();

        if (false !== ($array = $statement->fetchAll()) && $result) {
            return $this->entryMapper->fromDataArray($array);
        }

        return [];
    }

    /**
     * @param int $id
     * @return Entry|null
     * @throws MapperException
     */
    public function getOneById(int $id): ?Entry
    {
        $query = 'SELECT id, datetime_from, datetime_to, content, issue FROM entry WHERE id = :id';

        $data = [
            EntryMapper::KEY_ID => $id,
        ];

        $statement = $this->database->prepare($query);
        $result = $statement->execute($data);

        if (false !== ($array = $statement->fetch()) && $result) {
            return $this->entryMapper->fromData($array);
        }

        return null;
    }

    /**
     * @param Entry $entry
     * @return bool
     */
    public function updateOne(Entry $entry): bool
    {
        $query = 'UPDATE entry SET datetime_from = :datetime_from, datetime_to = :datetime_to, content = :content, issue = :issue WHERE id = :id';
        $parameter = [
            EntryMapper::KEY_ID,
            EntryMapper::KEY_DATETIME_FROM,
            EntryMapper::KEY_DATETIME_TO,
            EntryMapper::KEY_CONTENT,
            EntryMapper::KEY_ISSUE,
        ];

        $data = array_intersect_key($this->entryMapper->toData($entry), array_flip($parameter));

        $statement = $this->database->prepare($query);
        $result = $statement->execute($data);

        // In the case that nothing changed, the affected rows could be 0, thus use the result instead.
        return $statement->rowCount() > 0 || $result;
    }

    /**
     * @param int $id
     * @return bool
     */
    public function deleteOneById(int $id): bool
    {
        $query = 'DELETE FROM entry WHERE id = :id';

        $data = [
            EntryMapper::KEY_ID => $id,
        ];

        $statement = $this->database->prepare($query);
        $result = $statement->execute($data);

        return $statement->rowCount() > 0 && $result;
    }
}
