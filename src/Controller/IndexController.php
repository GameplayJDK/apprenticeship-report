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
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Exception\HttpMethodNotAllowedException;
use Slim\Exception\HttpNotFoundException;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class IndexController
 *
 * @package App\Controller
 */
class IndexController implements ControllerInterface
{
    const ROUTE_INDEX = 'index.index';
    const ROUTE_IMPORT = 'index.import';

    /**
     * @param App $app
     */
    public static function register(App $app): void
    {
        $app->get('/', IndexController::class . ':indexAction')
            ->setName(IndexController::ROUTE_INDEX);

        $app->get('/import', IndexController::class . ':importAction')
            ->setName(IndexController::ROUTE_IMPORT);
    }

    /**
     * @var Twig
     */
    private $twig;

    /**
     * @var ImportService
     */
    private $importService;

    /**
     * IndexController constructor.
     * @param Twig $twig
     * @param ImportService $importService
     */
    public function __construct(Twig $twig, ImportService $importService)
    {
        $this->twig = $twig;
        $this->importService = $importService;
    }

    /**
     * index.index
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     *
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function indexAction(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        return $this->twig
            ->render($response, 'index/index.html.twig', [
                'import' => [
                    'configuration' => [
                        'time_limit' => $this->importService->getTimeLimit(),
                        'path' => $this->importService->getPath(),
                    ],
                ],
            ]);
    }

    /**
     * index.import
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     * @throws HttpMethodNotAllowedException
     */
    public function importAction(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if ($request->getHeaderLine('X-Requested-With') !== 'XMLHttpRequest') {
            throw new HttpMethodNotAllowedException($request);
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
