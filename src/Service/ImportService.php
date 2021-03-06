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

use App\Mapper\Import\EntryMapper as ImportEntryMapper;
use App\Repository\EntryRepositoryInterface;
use Exception;
use PhpOffice\PhpSpreadsheet\Exception as SpreadsheetException;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Psr\Log\LoggerInterface;

/**
 * Class ImportService
 *
 * @package App\Service
 */
class ImportService
{
    /**
     * Only `Xlsx` support for now.
     */
    const TYPE_READER = 'Xlsx';

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var ImportEntryMapper
     */
    private $entryMapper;

    /**
     * @var EntryRepositoryInterface
     */
    private $entryRepository;

    /**
     * @var string
     */
    private $path;

    /**
     * @var int
     */
    private $timeLimit;

    /**
     * ImportService constructor.
     * @param LoggerInterface $logger
     * @param EntryRepositoryInterface $entryRepository
     * @param ImportEntryMapper $entryMapper
     * @param string $path
     * @param int $timeLimit
     */
    public function __construct(LoggerInterface $logger, ImportEntryMapper $entryMapper, EntryRepositoryInterface $entryRepository, string $path, int $timeLimit)
    {
        $this->logger = $logger;
        $this->entryMapper = $entryMapper;
        $this->entryRepository = $entryRepository;
        $this->path = $path;
        $this->timeLimit = $timeLimit;
    }

    /**
     * @return bool
     */
    public function import(): bool
    {
        if (!$this->setTimeLimit()) {
            $this->logger->error('Could not apply time limit.', [
                'timeLimit' => $this->timeLimit,
            ]);

            return false;
        }

        $spreadsheet = $this->getSpreadsheet();

        if (null === $spreadsheet) {
            $this->logger->error('The spreadsheet is null.', [
                'spreadsheet' => $spreadsheet,
            ]);

            return false;
        }

        $content = $this->getSpreadsheetContent($spreadsheet);

        $this->logger->info('Content header.', [
            'content' => reset($content),
        ]);

        foreach ($content as $index => $row) {
            if (1 === $index || empty($row)) {
                continue;
            }

            $this->createEntry($index, $row);
        }

        return true;
    }

    /**
     * @return bool
     */
    private function setTimeLimit(): bool
    {
        // TODO: Test the behaviour of the following function:
        //ignore_user_abort(true);

        return set_time_limit($this->timeLimit);
    }

    /**
     * @return Spreadsheet
     */
    private function getSpreadsheet(): ?Spreadsheet
    {
        try {
            $reader = IOFactory::createReader(static::TYPE_READER);
            if ($reader->canRead($this->path)) {
                return $reader->load($this->path);
            }
        } catch (Exception $exception) {
            $this->logger->error('Could not read spreadsheet.', [
                'exception' => $exception,
                'path' => $this->path,
            ]);
        }

        return null;
    }

    /**
     * @param Spreadsheet $spreadsheet
     * @return array|array[]
     */
    private function getSpreadsheetContent(Spreadsheet $spreadsheet): array
    {
        try {
            $worksheet = $spreadsheet->getActiveSheet();

            return $worksheet->toArray(null, false, true, true);
        } catch (SpreadsheetException $exception) {
            $this->logger->error('Could not get active sheet.', [
                'exception' => $exception,
            ]);
        }

        return [];
    }

    /**
     * @param int $index
     * @param array $row
     * @return bool
     */
    private function createEntry(int $index, array $row): bool
    {
        // This is for better readability.
        $data = $row;

        try {
            $entry = $this->entryMapper->fromData($data);

            if ($this->entryRepository->insertOne($entry) > -1) {
                $this->logger->info('Could insert entry.', [
                    'index' => $index,
                    'row' => $row,
                    'data' => $data,
                    'entry' => $entry,
                ]);

                return true;
            } else {
                $this->logger->error('Could not insert entry.', [
                    'index' => $index,
                    'row' => $row,
                    'data' => $data,
                    'entry' => $entry,
                ]);
            }
        } catch (Exception $exception) {
            $this->logger->error('Could not insert entry.', [
                'exception' => $exception,
                'index' => $index,
                'row' => $row,
                'data' => $data ?? null,
                'entry' => $entry ?? null,
            ]);
        }

        return false;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @return int
     */
    public function getTimeLimit(): int
    {
        return $this->timeLimit;
    }
}
