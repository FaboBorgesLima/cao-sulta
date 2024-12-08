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
        $userToken = UserToken::findBy(["token" => $token]);
        if ($userToken) {
            $_SESSION['user']['id'] = $userToken->user()->get()->id;
            $_SESSION['user']['token'] = $userToken->token;
        }
    }

    public static function check(): bool
    {
        return isset($_SESSION['user']['id']) && self::user() !== null;
    }

    public static function isVet(): bool
    {
        $user = Auth::user();

        return $user && $user->vet();
    }

    public static function token(): ?string
    {
        if (isset($_SESSION['user']['token'])) {
            return $_SESSION['user']['token'];
        }
        return null;
    }

    public static function logout(): void
    {
        unset($_SESSION['user']['id']);

        $userToken = UserToken::findBy(["token" => Auth::token()]);

        if ($userToken) {
            $userToken->destroy();
        }
        unset($_SESSION['user']['token']);
    }
}
