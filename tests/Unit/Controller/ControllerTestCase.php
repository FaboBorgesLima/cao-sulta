<?php

namespace Tests\Unit\Controllers;

use Core\Constants\Constants;
use Core\Http\Request;
use Core\Http\Response;
use Tests\TestCase;

abstract class ControllerTestCase extends TestCase
{
    private Request $request;

    public function setUp(): void
    {
        parent::setUp();
        require Constants::rootPath()->join('config/routes.php');

        $_SERVER['REQUEST_METHOD'] = 'GET';
        $_SERVER['REQUEST_URI'] = '/';
        $this->request = new Request();
    }

    public function tearDown(): void
    {
        unset($_SERVER['REQUEST_METHOD']);
        unset($_SERVER['REQUEST_URI']);
    }

    public function get(string $controller, string $action): string
    {
        $controller = new $controller();

        ob_start();
        try {
            $res = $controller->$action($this->request);

            if ($res instanceof Response) {
                $res->send();
            }

            return ob_get_contents();
        } catch (\Exception $e) {
            throw $e;
        } finally {
            ob_end_clean();
        }
    }
}
