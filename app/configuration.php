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

use App\Mapper\Import\EntryMapper as ImportEntryMapper;
use App\Service\ImportService;
use App\Service\PrintService;
use Monolog\Logger;
use Slim\Views\Twig;

$extraDataPath = dirname(__DIR__) . '/app/print_service_extra_data.php';
$extraData = is_file($extraDataPath) ? require $extraDataPath : [];

return [
    Twig::class => [
        'path' => dirname(__DIR__) . '/view',
        'settings' => [
            'debug' => true,
            //'cache' => dirname(__DIR__) . '/var/cache/twig',
        ],
    ],

    Logger::class => [
        'name' => 'app',
        'filename' => dirname(__DIR__) . '/var/log/app.log',
    ],

    PDO::class => [
        'dsn' => 'mysql:host=127.0.0.1;dbname=apprenticeship_report;charset=utf8',
        'username' => 'root',
        'passwd' => '',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ],
    ],

    ImportService::class => [
        'time_limit' => 0,
        'path' => dirname(__DIR__) . '/import.xlsx',
    ],

    ImportEntryMapper::class => [
        'map' => [
            ImportEntryMapper::KEY_ID => -1,
            ImportEntryMapper::KEY_DATETIME_FROM => 'D',
            ImportEntryMapper::KEY_DATETIME_TO => 'D',
            ImportEntryMapper::KEY_CONTENT => 'W',
            ImportEntryMapper::KEY_CONTENT_HINT => 'T',
            ImportEntryMapper::KEY_ISSUE => 'A',
        ],
    ],

    PrintService::class => [
        'extra_data' => $extraData,
    ],
];
