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

namespace App\Mapper\Modify;

use App\Entity\Entry;
use App\Mapper\EntryMapper as MainEntryMapper;

/**
 * Class EntryMapper
 *
 * @package App\Mapper\Modify
 */
class EntryMapper extends MainEntryMapper
{
    /**
     * Override the main mapper format.
     *
     * From [developer.mozilla.org](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input/date) about the
     * html date input field:
     *
     * > The displayed date format will differ from the actual `value` — the displayed date is formatted based on the locale
     * > of the user's browser, but the parsed value is always formatted `yyyy-mm-dd`.
     */
    const DTS_FORMAT = 'Y-m-d';

    public function fromData(array $data): Entry
    {
        $data[static::KEY_ID] = $this->getIdFromData($data);

        return parent::fromData($data);
    }

    /**
     * @param array $data
     * @return int
     */
    private function getIdFromData(array $data): int
    {
        $key = $data[static::KEY_ID] ?? -1;
        return (int)($data[$key] ?? -1);
    }
}
