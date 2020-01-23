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
use App\Service\EntryService;
use Fig\Http\Message\StatusCodeInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Exception\HttpNotFoundException;
use Slim\Interfaces\RouteCollectorProxyInterface;
use Slim\Interfaces\RouteParserInterface;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class EntryController
 *
 * @package App\Controller
 */
class EntryController implements ControllerInterface
{
    const ROUTE_INDEX = 'entry.index';
    const ROUTE_CREATE = 'entry.create';
    const ROUTE_VIEW = 'entry.view';
    const ROUTE_EDIT = 'entry.edit';
    const ROUTE_DELETE = 'entry.delete';

    /**
     * @var array|string[]
     */
    const METHOD = [
        'GET',
        'POST',
    ];

    /**
     * @param App $app
     */
    public static function register(App $app): void
    {
        $app->group('/entry', function (RouteCollectorProxyInterface $group): void {
            $group->get('', EntryController::class . ':indexAction')
                ->setName(EntryController::ROUTE_INDEX);

            $group->map(EntryController::METHOD, '/create', EntryController::class . ':createAction')
                ->setName(EntryController::ROUTE_CREATE);

            $group->get('/view/{id:[0-9]+}', EntryController::class . ':viewAction')
                ->setName(EntryController::ROUTE_VIEW);

            $group->map(EntryController::METHOD, '/edit/{id:[0-9]+}', EntryController::class . ':editAction')
                ->setName(EntryController::ROUTE_EDIT);

            $group->map(EntryController::METHOD, '/delete/{id:[0-9]+}', EntryController::class . ':deleteAction')
                ->setName(EntryController::ROUTE_DELETE);
        });
    }

    /**
     * @var RouteParserInterface
     */
    private $routeParser;

    /**
     * @var Twig
     */
    private $twig;

    /**
     * @var EntryService
     */
    private $entryService;

    /**
     * EntryController constructor.
     * @param RouteParserInterface $routeParser
     * @param Twig $twig
     * @param EntryService $entryService
     */
    public function __construct(RouteParserInterface $routeParser, Twig $twig, EntryService $entryService)
    {
        $this->routeParser = $routeParser;
        $this->twig = $twig;
        $this->entryService = $entryService;
    }

    /**
     * entry.index
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
        $data = $this->entryService->retrieveEntryList();

        return $this->twig
            ->render($response, 'entry/index.html.twig', [
                'data' => $data,
            ]);
    }

    /**
     * entry.create
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
    public function createAction(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $error = false;

        if ('POST' === $request->getMethod()) {
            if (null !== ($dataArray = $request->getParsedBody()['entry'] ?? null) && is_array($dataArray)) {
                $id = $this->entryService->createEntry($dataArray);

                if ($id > -1) {
                    $path = $this->routeParser->urlFor(static::ROUTE_VIEW, [
                        'id' => $id,
                    ]);

                    return $response
                        ->withHeader('Location', $path)
                        ->withStatus(StatusCodeInterface::STATUS_FOUND);
                }
            }

            $error = true;
        }

        return $this->twig
            ->render($response, 'entry/create.html.twig', [
                'error' => $error,
            ]);
    }

    /**
     * entry.view
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     *
     * @throws HttpNotFoundException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function viewAction(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int)($args['id'] ?? -1);

        if (!($id > -1)) {
            throw new HttpNotFoundException($request);
        }

        $data = $this->entryService->retrieveEntry($id);

        if (!$data->hasEntry()) {
            throw new HttpNotFoundException($request);
        }

        return $this->twig
            ->render($response, 'entry/view.html.twig', [
                'data' => $data,
            ]);
    }

    /**
     * entry.edit
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     *
     * @throws HttpNotFoundException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function editAction(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int)($args['id'] ?? -1);

        if (!($id > -1)) {
            throw new HttpNotFoundException($request);
        }

        $error = false;

        if ('POST' === $request->getMethod()) {
            if (null !== ($dataArray = $request->getParsedBody()['entry'] ?? null) && is_array($dataArray)) {
                $button = $request->getParsedBody()['submit'] ?? 'save';
                $result = false;

                if ('import' === $button) {
                    $result = $this->entryService->importEntry($id, $dataArray);
                }
                if ('save' === $button) {
                    $result = $this->entryService->updateEntry($id, $dataArray);
                }

                if (true === $result) {
                    $path = $this->routeParser->urlFor(static::ROUTE_VIEW, [
                        'id' => $id,
                    ]);

                    return $response
                        ->withHeader('Location', $path)
                        ->withStatus(StatusCodeInterface::STATUS_FOUND);
                }
            }

            $error = true;
        }

        $data = $this->entryService->retrieveEntry($id);

        if (!$data->hasEntry()) {
            throw new HttpNotFoundException($request);
        }

        return $this->twig
            ->render($response, 'entry/edit.html.twig', [
                'data' => $data,
                'error' => $error,
            ]);
    }

    /**
     * entry.delete
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param array $args
     * @return ResponseInterface
     *
     * @throws HttpNotFoundException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function deleteAction(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $id = (int)($args['id'] ?? -1);

        if (!($id > -1)) {
            throw new HttpNotFoundException($request);
        }

        $error = false;

        if ('POST' === $request->getMethod()) {
            if (null !== ($confirm = $request->getParsedBody()['confirm'] ?? null) && false !== ($confirm = (bool)$confirm)) {
                if (null !== ($dataArray = $request->getParsedBody()['entry'] ?? null) && is_array($dataArray)) {
                    $result = $this->entryService->deleteEntry($id, $dataArray);

                    if (true === $result) {
                        $path = $this->routeParser->urlFor(static::ROUTE_INDEX);

                        return $response
                            ->withHeader('Location', $path)
                            ->withStatus(StatusCodeInterface::STATUS_FOUND);
                    }
                }
            }

            $error = true;
        }

        $data = $this->entryService->retrieveEntry($id);

        if (!$data->hasEntry()) {
            throw new HttpNotFoundException($request);
        }

        return $this->twig
            ->render($response, 'entry/delete.html.twig', [
                'data' => $data,
                'error' => $error,
            ]);
    }
}
