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
use App\Service\PrintService;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\App;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class PrintController
 *
 * @package App\Controller
 */
class PrintController implements ControllerInterface
{
    const ROUTE_INDEX = 'print.index';

    /**
     * @param App $app
     */
    public static function register(App $app): void
    {
        $app->get('/print', PrintController::class . ':indexAction')
            ->setName(PrintController::ROUTE_INDEX);
    }

    /**
     * @var Twig
     */
    private $twig;

    /**
     * @var PrintService
     */
    private $printService;

    /**
     * PrintController constructor.
     * @param Twig $twig
     * @param PrintService $printService
     */
    public function __construct(Twig $twig, PrintService $printService)
    {
        $this->twig = $twig;
        $this->printService = $printService;
    }

    /**
     * print.index
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
            ->render($response, 'print/index.html.twig', []);
    }
}
