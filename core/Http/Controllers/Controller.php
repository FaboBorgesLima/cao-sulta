<?php

namespace Core\Http\Controllers;

use Core\Constants\Constants;

class Controller
{
    protected string $layout = 'application';


    public function __construct() {}

    /**
     * @param array<string, mixed> $data
     */
    protected function renderJson(string $view, array $data = []): void
    {
        extract($data);

        $view = Constants::rootPath()->join('app/views/' . $view . '.json.php');
        $json = [];

        header('Content-Type: application/json; chartset=utf-8');
        require $view;
        echo json_encode($json);
        return;
    }
}
