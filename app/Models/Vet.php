<?php

namespace App\Models;

use Core\Database\ActiveRecord\BelongsTo;
use Core\Database\ActiveRecord\HasFactory;
use Core\Database\ActiveRecord\HasMany;
use Core\Database\ActiveRecord\HasOne;
use Core\Database\ActiveRecord\Model;
use Lib\Random;
use Lib\Validations;

class Vet extends Model
{
    protected static string $table = "vets";
    protected static array $columns = ["user_id"];
    /** @var array<int,Model> */
    private array $attached_CRMV_registers = [];

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
        $this->attached_CRMV_registers[] = $crmv;
    }

    private function willCreateWithCRMVRegisters(): bool
    {
        if ($this->newRecord() && $this->attached_CRMV_registers) {
            foreach ($this->attached_CRMV_registers as $CRMV_register) {
                $CRMV_register->vet_id = 0;
                if (!$CRMV_register->isValid()) {
                    return false;
                }
                $CRMV_register->vet_id = null;
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

        foreach ($this->attached_CRMV_registers as $CRMV_register) {
            $CRMV_register->vet_id = $this->id;
            $CRMV_register->save();
        }

        return true;
    }

    /**
     * @return BelongsTo<User>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, "user_id");
    }

    /**
     * @return HasMany<CRMVRegister>
     */
    public function CRMVRegisters(): HasMany
    {
        return $this->hasMany(CRMVRegister::class, "vet_id");
    }
}
