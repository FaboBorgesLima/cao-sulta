<?php

require __DIR__ . '/../../config/bootstrap.php';

use Core\Database\Database;
use Database\Populate\UserPopulate;
use Database\Populate\VetPopulate;

Database::migrate();

UserPopulate::populate();
VetPopulate::populate();

echo "populated\n";
