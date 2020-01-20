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

namespace App\Mapper\Import;

use App\Entity\Entry;
use App\Exception\MapperException;
use App\Mapper\EntryMapper as MainEntryMapper;

/**
 * Class EntryMapper
 *
 * @package App\Mapper\Import
 */
class EntryMapper extends MainEntryMapper
{
    /**
     * Override the main mapper format.
     *
     * This is the usual format inside of the `Xlsx` file.
     */
    const DTS_FORMAT = 'Y-m-d H:i';

    const KEY_CONTENT_HINT = 'hint';

    /**
     * @var array
     */
    private $map;

    /**
     * EntryMapper constructor.
     * @param array $map
     */
    public function __construct(array $map)
    {
        $this->map = $map;
    }

    /**
     * @param array $data
     * @return Entry
     * @throws MapperException
     */
    public function fromData(array $data): Entry
    {
        $data[EntryMapper::KEY_ID] = $this->getIdFromData($data);
        $data[EntryMapper::KEY_DATETIME_FROM] = $this->getDateTimeFromFromData($data);
        $data[EntryMapper::KEY_DATETIME_TO] = $this->getDateTimeToFromData($data);
        $data[EntryMapper::KEY_CONTENT] = $this->getContentFromData($data);
        $data[EntryMapper::KEY_ISSUE] = $this->getIssueFromData($data);

        return parent::fromData($data);
    }

    /**
     * @param array $data
     * @return int
     */
    private function getIdFromData(array $data): int
    {
        $key = $this->map[static::KEY_ID] ?? -1;
        return (int)($data[$key] ?? -1);
    }

    /**
     * @param array $data
     * @return string
     */
    private function getDateTimeFromFromData(array $data): string
    {
        $key = $this->map[static::KEY_DATETIME_FROM] ?? -1;
        return $data[$key] ?? '';
    }

    /**
     * @param array $data
     * @return string
     */
    private function getDateTimeToFromData(array $data): string
    {
        $key = $this->map[static::KEY_DATETIME_TO] ?? -1;
        return $data[$key] ?? '';
    }

    /**
     * @param array $data
     * @return string
     */
    private function getContentFromData(array $data): string
    {
        $key = $this->map[static::KEY_CONTENT] ?? -1;

        $contentHint = $this->getContentHintFromData($data);
        $content = $data[$key] ?? '';

        if (!empty($contentHint)) {
            $content = "({$contentHint}) {$content}";
        }

        return $content;
    }

    /**
     * @param array $data
     * @return string
     */
    private function getContentHintFromData(array $data): string
    {
        $key = $this->map[static::KEY_CONTENT_HINT] ?? -1;
        return $data[$key] ?? '';
    }

    /**
     * @param array $data
     * @return string
     */
    private function getIssueFromData(array $data): string
    {
        $key = $this->map[static::KEY_ISSUE] ?? -1;
        return $data[$key] ?? '';
    }
}
