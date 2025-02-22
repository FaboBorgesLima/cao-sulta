<?php

namespace Tests\Unit\Controller;

use App\Models\User;
use App\Models\UserToken;
use Core\Constants\Constants;
use Core\Http\Request;
use Core\Http\Response;
use Lib\Authentication\Auth;
use Lib\Random;
use Tests\TestCase;

abstract class ControllerTestCase extends TestCase
{
    private Request $request;
    public bool $render = false;

    public function setUp(): void
    {
        parent::setUp();
        Auth::logout();
        $this->render = false;
        require Constants::rootPath()->join('config/routes.php');
        $_SERVER['REQUEST_URI'] = '/';
    }

    public function tearDown(): void
    {
        unset($_SERVER['REQUEST_METHOD']);
        unset($_SERVER['REQUEST_URI']);
    }

    public function login(): User
    {
        $user = User::factory()->create();

        $token = UserToken::withRandomToken($user->id);

        $token->save();

        Auth::login($token->token);

        return $user;
    }

    public function loginVet(): User
    {
        $user = $this->login();

        $vet = $user->vet()->new([]);

        $crmv_register = $vet->CRMVRegisters()->new([
            'crmv' => Random::CRMV(),
            "state" => Random::state()
        ]);

        $vet->attachCRMVRegister($crmv_register);

        $vet->save();

        return $user;
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
                if (!$this->render) {
                    return $res;
                }

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
