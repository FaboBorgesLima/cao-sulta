<?php

namespace Tests\Unit\Controller;

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
        $_SERVER['REQUEST_URI'] = '/';
    }

    public function tearDown(): void
    {
        unset($_SERVER['REQUEST_METHOD']);
        unset($_SERVER['REQUEST_URI']);
    }

    /**
     * @param array<string,mixed> $params
     */
    public function get(string $controller, string $action, array $params = []): string | Response
    {
        foreach ($params as $key => $value) {
            $_GET[$key] = $value;
        }
        $_SERVER['REQUEST_METHOD'] = 'GET';
        return $this->makeRequest($controller, $action, $params);
    }

    /**
     * @param array<string,mixed> $params
     */
    public function post(string $controller, string $action, array $params = []): string | Response
    {
        foreach ($params as $key => $value) {
            $_POST[$key] = $value;
        }
        $_SERVER['REQUEST_METHOD'] = 'POST';
        return $this->makeRequest($controller, $action, $params);
    }

    /**
     * @param array<string,mixed> $params
     */
    private function makeRequest(string $controller, string $action, array $params = []): string | Response
    {
        foreach ($params as $key => $value) {
            $_REQUEST[$key] = $value;
        }

        $this->request = new Request();
        $controller = new $controller();

        ob_start();
        try {
            $res = $controller->$action($this->request);

            if ($res instanceof Response) {
                return $res;
            }

            return ob_get_contents();
        } catch (\Exception $e) {
            throw $e;
        } finally {
            ob_end_clean();
        }
    }
}
