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

namespace App\Service;

use App\Model\ListEntryModel;
use App\Repository\EntryRepositoryInterface;

/**
 * Class PrintService
 *
 * @package App\Service
 */
class PrintService
{
    /**
     * @var EntryRepositoryInterface
     */
    private $entryRepository;

    /**
     * @var array
     */
    private $extraData;

    /**
     * PrintService constructor.
     * @param EntryRepositoryInterface $entryRepository
     * @param array $extraData
     */
    public function __construct(EntryRepositoryInterface $entryRepository, array $extraData)
    {
        $this->entryRepository = $entryRepository;
        $this->extraData = $extraData;
    }

    /**
     * @return ListEntryModel
     */
    public function getEntryList(): ListEntryModel
    {
        $list = $this->entryRepository->getAllManual();

        return (new ListEntryModel())
            ->setList($list);
    }

    /**
     * @return array
     */
    public function getExtraData(): array
    {
        return $this->extraData;
    }
}
