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

namespace App\Controller;

use App\ControllerInterface;
use App\Service\ImportService;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Exception\HttpNotFoundException;

/**
 * Class ImportController
 *
 * @package App\Controller
 */
class ImportController implements ControllerInterface
{
    const ROUTE_INDEX = 'import.index';

    /**
     * @param App $app
     */
    public static function register(App $app): void
    {
        $app->post('/import', static::class . ':indexAction')
            ->setName(static::ROUTE_INDEX);
    }

    /**
     * @var ImportService
     */
    private $importService;

    /**
     * ImportController constructor.
     * @param ImportService $importService
     */
    public function __construct(ImportService $importService)
    {
        $this->importService = $importService;
    }


    /**
     * import.index
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     * @throws HttpNotFoundException
     */
    public function indexAction(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if ($request->getHeaderLine('X-Requested-With') !== 'XMLHttpRequest') {
            throw new HttpNotFoundException($request);
        }

        $result = $this->importService->import();
        $data = [
            'result' => $result,
        ];

        $payload = json_encode($data) ?: null;

        $response->getBody()
            ->write($payload);

        return $response
            ->withHeader('Content-Type', 'application/json');
    }
}
