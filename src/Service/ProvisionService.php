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
use App\Repository\EntryRepositoryInterface;
use DateTime;
use Exception;
use Psr\Log\LoggerInterface;

/**
 * Class ProvisionService
 *
 * @package App\Service
 */
class ProvisionService
{
    use DatetimeDayOfWeekTrait;
    use EntryAccumulatorTrait;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var EntryRepositoryInterface
     */
    private $entryRepository;

    /**
     * @var DateTime
     */
    private $datetimeFrom;

    /**
     * @var DateTime
     */
    private $datetimeTo;

    /**
     * ProvisionService constructor.
     * @param LoggerInterface $logger
     * @param EntryRepositoryInterface $entryRepository
     * @param DateTime $datetimeFrom
     * @param DateTime $datetimeTo
     */
    public function __construct(LoggerInterface $logger, EntryRepositoryInterface $entryRepository, DateTime $datetimeFrom, DateTime $datetimeTo) {
        $this->logger = $logger;
        $this->entryRepository = $entryRepository;
        $this->datetimeFrom = $datetimeFrom;
        $this->datetimeTo = $datetimeTo;
    }

    // TODO: Check if time_limit will also be needed here.

    /**
     * @return bool
     */
    public function provision(): bool
    {
        $list = $this->getEntryList();

        foreach ($list as $entry) {
            if (null === $entry) {
                continue;
            }

            $this->importEntry($entry);
        }

        return true;
    }

    /**
     * @return array|Entry[]
     */
    private function getEntryList(): array
    {
        $list = [];

        $datetimeCurrent = $this->getDatetimeCurrent();

        while ($datetimeCurrent <= $this->datetimeTo) {
            $entry = new Entry();
            $entry->setId(-1);

            if (false === $datetimeCurrent->modify('next Monday')) {
                $this->logger->error('Could not modify current datetime with "next Monday".', [
                    'datetimeFrom' => $this->datetimeFrom,
                    'datetimeTo' => $this->datetimeTo,
                    'datetimeCurrent' => $datetimeCurrent,
                    'entry' => $entry,
                ]);

                break;
            }

            $entry->setDatetimeFrom(clone $datetimeCurrent);

            if (false === $datetimeCurrent->modify('next Friday')) {
                $this->logger->error('Could not modify current datetime with "next Friday".', [
                    'datetimeFrom' => $this->datetimeFrom,
                    'datetimeTo' => $this->datetimeTo,
                    'datetimeCurrent' => $datetimeCurrent,
                    'entry' => $entry,
                ]);

                break;
            }

            $entry->setDatetimeTo(clone $datetimeCurrent);

            $entry->setContent(null);
            $entry->setIssue(null);

            $list[] = $entry;
        }

        return $list;
    }

    /**
     * @return DateTime
     */
    private function getDatetimeCurrent(): DateTime
    {
        $datetimeCurrent = clone $this->datetimeFrom;

        if (!$this->isDatetimeValid($datetimeCurrent)) {
            $this->logger->info('Invalid current datetime detected (no weekend).');

            // Reset to 'last Sunday', so the 'next Monday' modification works correctly.
            if (false === $datetimeCurrent->modify('last Saturday')) {
                $this->logger->error('Could not modify current datetime with "next Monday".', [
                    'datetimeFrom' => $this->datetimeFrom,
                    'datetimeTo' => $this->datetimeTo,
                    'datetimeCurrent' => $datetimeCurrent,
                ]);
            } else {
                $this->logger->info('Could modify current datetime with "last Saturday"');
            }
        }

        return $datetimeCurrent;
    }

    /**
     * A datetime is seen as invalid, when it is a weekday. Monday through Friday is the timespan of an entry, so the
     * provisioning should start at a monday, which would skip a whole week due to the first `modify()` call inside the
     * while loop.
     *
     * @param DateTime $dateTime
     * @return bool
     */
    private function isDatetimeValid(DateTime $dateTime): bool
    {
        return $this->isWeekend($dateTime);
    }

    /**
     * @param Entry $entry
     * @return bool
     */
    private function importEntry(Entry $entry): bool
    {
        try {
            $list = $this->entryRepository->getAllBetweenDatetimeFromAndDatetimeTo($entry->getDatetimeFrom(), $entry->getDatetimeTo());

            // Call function from trait.
            $content = $this->accumulateEntryContent($list);

            $entry->setContent($content);
            $entry->setIssue(null);

            if ($this->entryRepository->insertOne($entry) > -1) {
                $this->logger->info('Could insert entry.', [
                    'content' => $content,
                    'entry' => $entry,
                ]);

                return true;
            } else {
                $this->logger->error('Could not insert entry.', [
                    'list' => $list,
                    'content' => $content,
                    'entry' => $entry,
                ]);
            }
        } catch (Exception $exception) {
            $this->logger->error('Could not insert entry.', [
                'exception' => $exception,
                'list' => $list ?? null,
                'content' => $content ?? null,
                'entry' => $entry ?? null,
            ]);
        }

        return false;
    }

    /**
     * @return DateTime
     */
    public function getDatetimeFrom(): DateTime
    {
        return $this->datetimeFrom;
    }

    /**
     * @return DateTime
     */
    public function getDatetimeTo(): DateTime
    {
        return $this->datetimeTo;
    }
}
