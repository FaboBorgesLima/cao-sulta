<?php

namespace App\Models;

use Core\Database\ActiveRecord\BelongsTo;
use Core\Database\ActiveRecord\Model;
use Lib\Validations;

class Permission extends Model
{
    protected static string $table = "permissions";
    protected static array $columns = [
        "user_id",
        "vet_id",
        "accepted"
    ];

    protected static array $default = [
        "accepted" => 0
    ];

    public function validates(): void
    {
        Validations::notEmpty("user_id", $this);
        Validations::int("user_id", $this);
        Validations::notEmpty("vet_id", $this);
        Validations::int("user_id", $this);
        Validations::notEmpty("accepted", $this);
        Validations::bool("accepted", $this);
        Validations::uniqueness(["user_id", "vet_id"], $this);
    }

    /**
     * @return BelongsTo<User>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, "user_id");
    }

    /**
     * @return BelongsTo<Vet>
     */
    public function vet(): BelongsTo
    {
        return $this->belongsTo(Vet::class, "vet_id");
    }
}
