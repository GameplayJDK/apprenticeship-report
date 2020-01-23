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

namespace App\Mapper;

use App\Entity\Entry;
use App\Exception\MapperException;
use DateTime;
use Respect\Validation\Validator;
use Respect\Validation\Validator as v;

/**
 * Class EntryMapper
 *
 * @package App\Mapper
 */
class EntryMapper
{
    const KEY_ID = 'id';
    const KEY_DATETIME_FROM = 'datetime_from';
    const KEY_DATETIME_TO = 'datetime_to';
    const KEY_CONTENT = 'content';
    const KEY_ISSUE = 'issue';

    const DTS_FORMAT = 'Y-m-d H:i:s';

    /**
     * TODO: Use createValidator() instead of manual validation.
     *
     * @param array $data
     * @return Entry
     * @throws MapperException
     */
    public function fromData(array $data): Entry
    {
        $entry = new Entry();

        if (isset($data[static::KEY_ID]) && is_int($data[static::KEY_ID])) {
            $entry->setId($data[static::KEY_ID]);
        } else {
            throw new MapperException('Missing key: ' . static::KEY_ID);
        }

        if (isset($data[static::KEY_DATETIME_FROM]) && is_string($data[static::KEY_DATETIME_FROM])
            && null !== ($dateTime = $this->createDateTime($data[static::KEY_DATETIME_FROM]))) {

            $entry->setDatetimeFrom($dateTime);
        } else {
            throw new MapperException('Missing key: ' . static::KEY_DATETIME_FROM);
        }

        if (isset($data[static::KEY_DATETIME_TO]) && is_string($data[static::KEY_DATETIME_TO])
            && null !== ($dateTime = $this->createDateTime($data[static::KEY_DATETIME_TO]))) {

            $entry->setDatetimeTo($dateTime);
        } else {
            throw new MapperException('Missing key: ' . static::KEY_DATETIME_TO);
        }

        if (isset($data[static::KEY_CONTENT]) && is_string($data[static::KEY_CONTENT])) {
            $entry->setContent($data[static::KEY_CONTENT]);
        }

        if (isset($data[static::KEY_ISSUE]) && is_string($data[static::KEY_ISSUE])) {
            $entry->setIssue($data[static::KEY_ISSUE]);
        }

        return $entry;
    }

    /**
     * @param array|array[] $array
     * @return array|Entry[]
     * @throws MapperException
     */
    public function fromDataArray(array $array): array
    {
        $entryList = [];

        foreach ($array as $data) {
            $entryList[] = $this->fromData($data);
        }

        return $entryList;
    }

    /**
     * @param Entry $entry
     * @return array
     */
    public function toData(Entry $entry): array
    {
        $data = [];

        $data[static::KEY_ID] = $entry->getId();
        $data[static::KEY_DATETIME_FROM] = $entry->getDatetimeFrom()
            ->format(static::DTS_FORMAT);
        $data[static::KEY_DATETIME_TO] = $entry->getDatetimeTo()
            ->format(static::DTS_FORMAT);
        $data[static::KEY_CONTENT] = $entry->getContent();
        $data[static::KEY_ISSUE] = $entry->getIssue();

        return $data;
    }

    /**
     * @param array|Entry[] $array
     * @return array|array[]
     */
    public function toDataArray(array $array): array
    {
        $dataList = [];

        foreach ($array as $data) {
            $dataList[] = $this->toData($data);
        }

        return $dataList;
    }

    /**
     * @param $string
     * @return DateTime|false
     */
    private function createDateTime(string $string): ?DateTime
    {
        return DateTime::createFromFormat(static::DTS_FORMAT, $string) ?: null;
    }

    /**
     * @param bool $idMandatory
     * @return Validator
     */
    public function createValidator(bool $idMandatory = true): Validator
    {
        return v::arrayType()
            ->keySet(...[
                v::key(static::KEY_ID, v::intVal(), $idMandatory),
                v::key(static::KEY_DATETIME_FROM, v::date(static::DTS_FORMAT), true),
                v::key(static::KEY_DATETIME_TO, v::date(static::DTS_FORMAT), true),
                v::key(static::KEY_CONTENT, v::stringType()->length(0, 4096, true), false),
                v::key(static::KEY_ISSUE, v::stringType()->length(0, 256, true), false),
            ]);
    }
}
