<?php

namespace Core\Http;

use Core\Constants\Constants;

class Response
{

    protected static bool $sended = false;


    /**
     * @param array<string,string> $headers
     * @param array<string,string> $data
     */
    function __construct(public int $code = 200, protected array $headers = [], protected ?string $file = null, protected ?array $data = null) {}

    public function addHeader(string $name, string $value)
    {
        $this->headers[$name] = $value;
    }

    public static function redirectTo(string $location): Response
    {
        return new Response(code: 302, headers: ["location" => $location]);
    }

    /**
     * @param array<string,string> $data
     */
    public static function render(string $view, array $data = null): Response
    {

        return new Response(data: $data, file: Constants::rootPath()->join('app/views/' . $view . '.phtml'));
    }

    public function removeHeader(string $name): ?string
    {
        if (array_key_exists($name, $this->headers)) {
            $value = $this->headers[$name];

            unset($this->headers[$name]);

            return $value;
        }

        return null;
    }

    public function send(): void
    {
        if (static::$sended)
            return;

        static::$sended = true;

        header("http/" . (string)$this->code);

        foreach ($this->headers as $name => $value) {
            header(implode(":", [$name, $value]));
        }

        if ($this->data) {
            extract($this->data);
        }

        if ($this->file) {
            require $this->file;
        }
        exit;
    }
}
