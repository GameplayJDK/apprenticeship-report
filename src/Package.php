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

namespace App;

use App\Mapper\EntryMapper;
use App\Repository\EntryRepository;
use App\Repository\EntryRepositoryInterface;
use App\Service\ImportService;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;
use PDO;
use PDOException;
use Pimple\Container;
use Pimple\Package\Exception\PackageException;
use Pimple\Package\PackageAbstract;
use Psr\Log\LoggerInterface;
use Slim\App;
use Slim\Views\Twig;

/**
 * Class Package
 *
 * @package App
 */
class Package extends PackageAbstract
{
    /**
     * @var App
     */
    private $app;

    /**
     * Package constructor.
     * @param App $app
     * @param Container|null $container
     */
    public function __construct(App $app, ?Container $container = null)
    {
        parent::__construct($container);

        $this->app = $app;
    }

    /**
     * @param Container $pimple
     * @throws PackageException
     */
    public function register(Container $pimple): void
    {
        parent::register($pimple);

        $this->container[App::class] = $this->app;

        $this->register3rdPartyService();

        $this->registerDatabase();

        $this->registerMapper();

        $this->registerRepository();

        $this->registerImportService();
    }

    /**
     * @throws PackageException
     */
    private function register3rdPartyService(): void
    {
        $this->registerConfiguration(Twig::class, [
            'path' => dirname(__DIR__) . '/view',
            'settings' => [
                'debug' => true,
                //'cache' => dirname(__DIR__) . '/var/cache/twig',
            ],
        ]);

        $this->registerConfiguration(Logger::class, [
            'name' => 'app',
            'filename' => dirname(__DIR__) . '/var/log/app.log',
        ]);

        /** @var array $configuration */
        $configuration = $this->container[static::SERVICE_NAME_CONFIGURATION];

        $this->registerService(Twig::class, function (Container $container) use ($configuration): Twig {
            /** @var array $settings */
            $settings = $configuration[Twig::class];

            $twig = Twig::create($settings['path'], $settings);

            return $twig;
        });

        $this->registerService(Logger::class, function (Container $container) use ($configuration): Logger {
            /** @var array $settings */
            $settings = $configuration[Logger::class];
            /** @var Logger $logger */
            $logger = new Logger($settings['name']);

            /** @var RotatingFileHandler $handler */
            $handler = new RotatingFileHandler($settings['filename']);
            $handler->setFilenameFormat('{date}_{filename}', 'Ymd');

            $logger->pushHandler($handler);

            return $logger;
        });

        $this->registerServiceAlias(LoggerInterface::class, Logger::class);
    }

    /**
     * @throws PackageException
     */
    private function registerDatabase(): void
    {
        $this->registerConfiguration(PDO::class, [
            'dsn' => 'mysql:host=127.0.0.1;dbname=apprenticeship_report;charset=utf8',
            'username' => 'root',
            'passwd' => '',
            'options' => [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ],
        ]);

        /** @var array $configuration */
        $configuration = $this->container[static::SERVICE_NAME_CONFIGURATION];

        $this->registerService(PDO::class, function (Container $container) use ($configuration): PDO {
            /** @var array $settings */
            $settings = $configuration[PDO::class];

            // Pointless?
            try {
                return new PDO($settings['dsn'], $settings['username'], $settings['passwd'], $settings['options']);
            } catch (PDOException $ex) {
                throw new PDOException($ex->getMessage(), $ex->getCode(), $ex);
            }
        });
    }

    private function registerMapper(): void
    {
        $this->registerService(EntryMapper::class, function (Container $container): EntryMapper {
            return new EntryMapper();
        });
    }

    private function registerRepository(): void
    {
        $this->registerService(EntryRepository::class, function (Container $container): EntryRepository {
            /** @var PDO $database */
            $database = $container[PDO::class];
            /** @var EntryMapper $entryMapper */
            $entryMapper = $container[EntryMapper::class];

            return new EntryRepository($database, $entryMapper);
        });

        $this->registerServiceAlias(EntryRepositoryInterface::class, EntryRepository::class);
    }

    /**
     * @throws PackageException
     */
    private function registerImportService(): void
    {
        $this->registerConfiguration(ImportService::class, [
            // Only Xslx support for now.
            'path' => dirname(__DIR__) . '/import.xslx',
        ]);

        /** @var array $configuration */
        $configuration = $this->container[static::SERVICE_NAME_CONFIGURATION];

        $this->registerService(ImportService::class, function (Container $container) use ($configuration): ImportService {
            /** @var array $settings */
            $settings = $configuration[ImportService::class];

            /** @var LoggerInterface $logger */
            $logger = $container[LoggerInterface::class];
            /** @var EntryRepositoryInterface $entryRepository */
            $entryRepository = $container[EntryRepositoryInterface::class];
            /** @var EntryMapper $entryMapper */
            $entryMapper = $container[EntryMapper::class];

            return new ImportService($logger, $entryRepository, $entryMapper, $settings['path']);
        });
    }
}
