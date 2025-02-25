<?php

namespace Core\Http;

use App\Models\User;
use Core\Constants\Constants;
use Lib\Authentication\Auth;

class Response
{
    protected static bool $sended = false;
    protected ?User $user = null;

    /**
     * @param array<string,string> $headers
     * @param array<string,mixed> $data
     */
    public function __construct(
        public int $code = 200,
        protected array $headers = [],
        protected ?string $file = null,
        protected ?array $data = null
    ) {
        /** */
    }

    public function setHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;

        return $this;
    }

    public static function redirectTo(string $location): Response
    {
        return new Response(code: 302, headers: ["location" => $location]);
    }

    public static function goBack(): Response
    {
        return static::redirectTo($_SERVER['HTTP_REFERER'] ?? '/');
    }

    /**
     * @param array<string,mixed> $data
     */
    public static function render(string $view, array $data = null): Response
    {

        return new Response(data: $data, file: Constants::rootPath()->join('app/views/' . $view . '.phtml'));
    }

    /**
     * @param array<string,mixed> $json
     */
    public static function json(array $json): Response
    {
        $res = new Response(data: ["json" => $json], file: Constants::rootPath()->join('app/views/json.php'));

        $res->setHeader("Content-Type", "application/json; chartset=utf-8");

        return $res;
    }

    public static function error(int $code, string $msg = "", bool $json = false): Response
    {
        $data = [
            "msg" => $msg,
            "code" => $code
        ];

        $res = $json ? static::json($data) : static::render("error", $data);

        $res->code = $code;

        return $res;
    }

    public static function notFound(string $msg = "", bool $json = false): Response
    {
        return static::error(404, $msg, $json);
    }

    public static function badRequest(string $msg = "", bool $json = false): Response
    {
        return static::error(400, $msg, $json);
    }

    public static function forbidden(string $msg = "", bool $json = false): Response
    {
        return static::error(403, $msg, $json);
    }

    public function withUser(): self
    {
        $this->user = Auth::user();

        return $this;
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

    public function getHeader(string $header): ?string
    {
        if (array_key_exists($header, $this->headers)) {
            return $this->headers[$header];
        }

        return null;
    }

    public function getFile(): ?string
    {
        return $this->file;
    }

    /** @return array<string, mixed> | null */
    public function getData(): ?array
    {
        return $this->data;
    }

    public function send(): void
    {
        if (!static::$sended) {
            foreach ($this->headers as $name => $value) {
                header(implode(":", [$name, $value]));
            }
            header("http/" . (string)$this->code);
        }

        static::$sended = true;

        if ($this->data) {
            extract($this->data);
        }

        if ($this->user) {
            extract(["user" => $this->user->toArray()]);
        }

        if ($this->file) {
            require $this->file;
        }
    }
}
