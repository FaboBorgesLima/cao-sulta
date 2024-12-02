<?php

namespace Lib\Authentication;

use App\Models\User;
use App\Models\UserToken;

use function PHPUnit\Framework\returnSelf;

class Auth
{
    public static function user(): ?User
    {
        if (isset($_SESSION['user']['id'])) {
            $id = $_SESSION['user']['id'];
            return User::findById($id);
        }

        return null;
    }

    public static function login(string $token): void
    {
        $user_token = UserToken::findBy(["token" => $token]);
        if ($user_token) {
            $_SESSION['user']['id'] = $user_token->user()->get()->id;
            $_SESSION['user']['token'] = $user_token->token;
        }
    }

    public static function check(): bool
    {
        return isset($_SESSION['user']['id']) && self::user() !== null;
    }

    public static function token(): ?string
    {
        if (isset($_SESSION['user']['token'])) {
            return $_SESSION['user']['token'];
        }
    }

    public static function logout(): void
    {
        unset($_SESSION['user']['id']);
        $user_token = UserToken::findBy(["token" => Auth::token()]);

        if ($user_token) {
            $user_token->destroy();
        }
    }
}
