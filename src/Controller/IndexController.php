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

use App\Service\ImportService;
use Psr\Container\ContainerInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Views\Twig;

/**
 * Class IndexController
 *
 * @package App\Controller
 */
class IndexController
{
    const ROUTE_INDEX = 'index.index';
    const ROUTE_EDIT = 'index.edit';
    const ROUTE_IMPORT = 'index.import';

    /**
     * @var Twig
     */
    private $view;

    /**
     * @var ImportService
     */
    private $importService;

    /**
     * IndexController constructor.
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->view = $container->get(Twig::class);

        $this->importService = $container->get(ImportService::class);

        //$routeParser = $request->getAttribute(RouteContext::ROUTE_PARSER);
    }

    /**
     * index.index
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function indexAction(Request $request, Response $response, array $args): Response
    {
        // TODO

        $response->getBody()
            ->write('index');

        return $response;
    }

    /**
     * index.edit
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function editAction(Request $request, Response $response, array $args): Response
    {
        // TODO

        $response->getBody()
            ->write('edit');

        return $response;
    }

    /**
     * index.import
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function importAction(Request $request, Response $response, array $args): Response
    {
        $result = false;

        if (false !== ini_set('max_execution_time', 0)) {
            $result = $this->importService->import();
        }

        $response->getBody()
            ->write('import result: ' . ($result ? 'true' : 'false'));

        return $response;
    }
}
