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
use DateTime;

/**
 * Interface EntryRepositoryInterface
 *
 * @package App\Repository
 */
interface EntryRepositoryInterface
{
    /**
     * @param Entry $entry
     * @return int
     */
    public function insertOne(Entry $entry): int;

    /**
     * @return array|Entry[]
     */
    public function getAll(): array;

    /**
     * @param DateTime $datetimeFrom
     * @param DateTime $datetimeTo
     * @return array|Entry[]
     */
    public function getAllBetweenDatetimeFromAndDatetimeTo(DateTime $datetimeFrom, DateTime $datetimeTo): array;

    /**
     * @return array|Entry[]
     */
    public function getAllManual(): array;

    /**
     * @param int $id
     * @return Entry|null
     */
    public function getOneById(int $id): ?Entry;

    /**
     * @param Entry $entry
     * @return bool
     */
    public function updateOne(Entry $entry): bool;

    /**
     * @param int $id
     * @return bool
     */
    public function deleteOneById(int $id): bool;
}
