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
     * @inheritDoc
     */
    public function insertOne(Entry $entry): bool
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
        $statement->execute($data);

        return $statement->rowCount() > 0;
    }

    /**
     * @inheritDoc
     * @throws MapperException
     */
    public function getAll(): array
    {
        $query = 'SELECT id, datetime_from, datetime_to, content, issue FROM entry';

        $statement = $this->database->prepare($query);
        $statement->execute();

        $array = $statement->fetchAll();

        return $this->entryMapper->fromDataArray($array);
    }
}
