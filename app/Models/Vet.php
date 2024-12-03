<?php

namespace App\Models;

use Core\Database\ActiveRecord\BelongsTo;
use Core\Database\ActiveRecord\HasMany;
use Core\Database\ActiveRecord\Model;
use Lib\Validations;

class Vet extends Model
{
    protected static string $table = "vets";
    protected static array $columns = ["user_id"];
    /** @var array<int,Model> */
    private array $attachedCRMVRegisters = [];

    public function validates(): void
    {
        if ($this->newRecord() && !$this->willCreateWithCRMVRegisters()) {
            $this->addError("crmv_registers", "veterinário precisa de pelo menos um registro de crmv");
        }
        Validations::notEmpty("user_id", $this);
        Validations::uniqueness("user_id", $this);
    }

    public function attachCRMVRegister(CRMVRegister $crmv): void
    {
        $this->attachedCRMVRegisters[] = $crmv;
    }

    public function willCreateWithCRMVRegisters(): bool
    {
        if ($this->newRecord() && $this->attachedCRMVRegisters) {
            foreach ($this->attachedCRMVRegisters as $CRMVRegister) {
                $CRMVRegister->vet_id = 0;
                if (!$CRMVRegister->isValid()) {
                    return false;
                }
                $CRMVRegister->vet_id = null;
            }
            return true;
        }

        return false;
    }
    public function save(): bool
    {
        $save = parent::save();

        if (!$save) {
            return false;
        }

        foreach ($this->attachedCRMVRegisters as $CRMVRegister) {
            $CRMVRegister->vet_id = $this->id;
            $CRMVRegister->save();
        }

        return true;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, "user_id");
    }

    public function CRMVRegisters(): HasMany
    {
        return $this->hasMany(CRMVRegister::class, "vet_id");
    }
}
