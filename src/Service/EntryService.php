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
use App\Model\OneEntryModel;
use App\Repository\EntryRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Class EntryService
 *
 * @package App\Service
 */
class EntryService
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var EntryRepositoryInterface
     */
    private $entryRepository;

    /**
     * EntryService constructor.
     * @param LoggerInterface $logger
     * @param EntryRepositoryInterface $entryRepository
     */
    public function __construct(LoggerInterface $logger, EntryRepositoryInterface $entryRepository)
    {
        $this->logger = $logger;
        $this->entryRepository = $entryRepository;
    }

    /**
     * @param array $dataArray
     * @return int
     */
    public function createEntry(array $dataArray): int
    {
        // TODO

        return -1;
    }

    /**
     * @return ListEntryModel
     */
    public function retrieveEntryList(): ListEntryModel
    {
        $all = $this->entryRepository->getAll();

        return (new ListEntryModel())
            ->setList($all);
    }

    /**
     * @param int $id
     * @return OneEntryModel
     */
    public function retrieveEntry(int $id): OneEntryModel
    {
        // TODO

        return (new OneEntryModel())
            ->setEntry(null);
    }

    /**
     * @param int $id
     * @param array $dataArray
     * @return bool
     */
    public function updateEntry(int $id, array $dataArray): bool
    {
        // TODO

        return false;
    }

    /**
     * @param int $id
     * @param array $dataArray
     * @return bool
     */
    public function deleteEntry(int $id, array $dataArray): bool
    {
        // TODO

        // $id === $dataArray['id']

        return false;
    }
}
