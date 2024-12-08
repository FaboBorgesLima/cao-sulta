<?php

namespace App\Models;

use Core\Database\ActiveRecord\BelongsTo;
use Core\Database\ActiveRecord\Model;
use Lib\Validations;

class CRMVRegister extends Model
{
    protected static string $table = "crmv_registers";
    protected static array $columns = ["vet_id", "crmv", "state"];

    public function validates(): void
    {
        Validations::notEmpty("vet_id", $this);
        Validations::notEmpty("crmv", $this);
        Validations::notEmpty("state", $this);
        Validations::state("state", $this);
        Validations::numeric("crmv", $this);
        Validations::uniqueness(["crmv", "state"], $this);
    }

    public function vet(): BelongsTo
    {
        return $this->belongsTo(Vet::class, "vet_id");
    }

    public function destroy(): bool
    {
        $crmvs = CRMVRegister::where(["vet_id" => $this->vet_id]);

        if (count($crmvs) < 2) {
            return false;
        }
        return parent::destroy();
    }
}
