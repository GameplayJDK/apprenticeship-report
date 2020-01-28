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

use App\Entity\Entry;
use App\Exception\MapperException;
use App\Mapper\Modify\EntryMapper as ModifyEntryMapper;
use App\Model\EntryModel;
use App\Model\ListEntryModel;
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
     * @var ModifyEntryMapper
     */
    private $entryMapper;

    /**
     * @var EntryRepositoryInterface
     */
    private $entryRepository;

    /**
     * EntryService constructor.
     * @param LoggerInterface $logger
     * @param ModifyEntryMapper $entryMapper
     * @param EntryRepositoryInterface $entryRepository
     */
    public function __construct(LoggerInterface $logger, ModifyEntryMapper $entryMapper, EntryRepositoryInterface $entryRepository)
    {
        $this->logger = $logger;
        $this->entryMapper = $entryMapper;
        $this->entryRepository = $entryRepository;
    }

    /**
     * @param array $dataArray
     * @return int
     */
    public function createEntry(array $dataArray): int
    {
        $entry = $this->createEntryFromDataArray($dataArray);

        if (null !== $entry && ($id = $this->entryRepository->insertOne($entry)) > -1) {
            return $id;
        }

        return -1;
    }

    /**
     * @return ListEntryModel
     */
    public function retrieveEntryList(): ListEntryModel
    {
        $list = $this->entryRepository->getAll();

        return (new ListEntryModel())
            ->setList($list);
    }

    /**
     * @return ListEntryModel
     */
    public function retrieveEntryListManual(): ListEntryModel
    {
        $list = $this->entryRepository->getAllManual();

        return (new ListEntryModel())
            ->setList($list);
    }

    /**
     * @param int $id
     * @return EntryModel
     */
    public function retrieveEntry(int $id): EntryModel
    {
        $entry = $this->entryRepository->getOneById($id);

        return (new EntryModel())
            ->setEntry($entry);
    }

    /**
     * @param int $id
     * @param array $dataArray
     * @return bool
     */
    public function updateEntry(int $id, array $dataArray): bool
    {
        $entry = $this->createEntryFromDataArray($dataArray);

        return (null !== $entry) && ($entry->getId() === $id) &&
            $this->entryRepository->updateOne($entry);
    }

    /**
     * @param int $id
     * @param array $dataArray
     * @return bool
     */
    public function importEntry(int $id, array $dataArray): bool
    {
        $entry = $this->createEntryFromDataArray($dataArray);

        if (null !== $entry && $entry->getId() === $id) {
            $list = $this->entryRepository->getAllBetweenDatetimeFromAndDatetimeTo($entry->getDatetimeFrom(), $entry->getDatetimeTo());

            $content = $this->importEntryContentFromList($list);

            $entry->setContent($content);
            $entry->setIssue(null);

            return $this->entryRepository->updateOne($entry);
        }

        return false;
    }

    /**
     * @param int $id
     * @param array $dataArray
     * @return bool
     */
    public function deleteEntry(int $id, array $dataArray): bool
    {
        $entry = $this->createEntryFromDataArray($dataArray);

        return (null !== $entry) && ($entry->getId() === $id) &&
            $this->entryRepository->deleteOneById($id);
    }

    /**
     * @param array $dataArray
     * @return Entry|null
     */
    private function createEntryFromDataArray(array $dataArray): ?Entry
    {
        $entry = null;
        $validator = $this->entryMapper->createValidator();

        if ($validator->validate($dataArray)) {
            try {
                $entry = $this->entryMapper->fromData($dataArray);
            } catch (MapperException $exception) {
                $this->logger->error('Could not map entry from data array!', [
                    'exception' => $exception,
                    'dataArray' => $dataArray,
                    'entry' => $entry,
                ]);
            }
        } else {
            $this->logger->error('Could not validate entry from data array!', [
                'dataArray' => $dataArray,
                'entry' => $entry,
            ]);
        }

        return $entry;
    }

    /**
     * @param array|Entry[] $list
     * @return string
     */
    public function importEntryContentFromList(array $list)
    {
        $listContent = array_map(function (Entry $entry): string {
            $date = $entry->getDatetimeFrom()
                ->format('d-m-Y');
            $content = $entry->getContent();

            return "($date) $content";
        }, $list);

        return implode(PHP_EOL, $listContent);
    }
}
