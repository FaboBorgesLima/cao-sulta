<?php

namespace Core\Http;

use App\Models\User;
use Lib\Authentication\Auth;

class Request
{
    private string $method;
    private string $uri;

    /** @var mixed[] */
    private array $params;

    /** @var array<string, string> */
    private array $headers;

    private ?User $user;

    public function __construct()
    {
        $this->method = $_REQUEST['_method'] ?? $_SERVER['REQUEST_METHOD'];
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->params = $_REQUEST;
        $this->headers = function_exists('getallheaders') ? getallheaders() : [];
        $this->user = Auth::user();
    }

    public function file(string $name): mixed
    {
        return isset($_FILES[$name]) ? $_FILES[$name] : null;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    /** @return mixed[] */
    public function getParams(): array
    {
        return $this->params;
    }

    /** @return array<string, string>*/
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /** @param mixed[] $params*/
    public function addParams(array $params): void
    {
        $this->params = array_merge($this->params, $params);
    }

    /**
     * @param string[] $params
     * @return array<string,mixed>
     * */
    public function only(array $params): array
    {
        $out = [];

        foreach ($params as $key) {
            if (isset($this->params[$key])) {
                $out[$key] = $this->params[$key];
            }
        }

        return $out;
    }

    public function acceptJson(): bool
    {
        return (isset($_SERVER['HTTP_ACCEPT']) && $_SERVER['HTTP_ACCEPT'] === 'application/json');
    }

    public function getParam(string $key, mixed $default = null): mixed
    {
        return $this->params[$key] ?? $default;
    }

    public function user(): ?User
    {
        return $this->user;
    }
}
