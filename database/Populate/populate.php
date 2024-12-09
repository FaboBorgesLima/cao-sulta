<?php

require __DIR__ . '/../../config/bootstrap.php';

use Core\Database\Database;
use Database\Populate\PetPopulate;
use Database\Populate\UserPopulate;
use Database\Populate\UserTokenPopulate;
use Database\Populate\VetPopulate;

Database::migrate();

UserPopulate::populate();
UserTokenPopulate::populate();
VetPopulate::populate();
PetPopulate::populate();

echo "populated\n";
