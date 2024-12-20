<?php

namespace Lib;

use Core\Database\Database;
use Exception;

class Validations
{
    public static function notEmpty($attribute, $obj)
    {
        if ($obj->$attribute === null || $obj->$attribute === '') {
            $obj->addError($attribute, 'não pode ser vazio!');
            return false;
        }

        return true;
    }

    public static function passwordConfirmation($obj)
    {
        if ($obj->password !== $obj->password_confirmation) {
            $obj->addError('password', 'as senhas devem ser idênticas!');
            return false;
        }

        return true;
    }

    public static function email($attribute, $obj): void
    {
        if (!$obj->$attribute) {
            return;
        }

        if (gettype($obj->$attribute) != "string") {
            $obj->addError($attribute, "o e-mail deve ser uma palavra.");
            return;
        }

        if (!filter_var($obj->$attribute, FILTER_VALIDATE_EMAIL)) {
            $obj->addError($attribute, 'deve ser um e-mail válido');
        }
    }

    public static function name($attribute, $obj): void
    {
        if ($obj->$attribute === null || $obj->$attribute === '') {
            return;
        }

        if (gettype($obj->$attribute) != "string") {
            $obj->addError($attribute, "o nome deve ser uma palavra.");
            return;
        }

        if (strlen($obj->$attribute) > 255) {
            $obj->addError($attribute, "o nome deve ter no máximo 255 letras.");
            return;
        }

        $obj->$attribute = trim($obj->$attribute);

        if (strlen($obj->$attribute) < 3) {
            $obj->addError($attribute, "o nome deve ter no minimo 3 letras.");
            return;
        }

        preg_match("/^[\p{L}\p{M}\s-]+$/u", $obj->$attribute, $matches);

        if (!$matches) {
            $obj->addError(
                $attribute,
                "o nome deve conter somente letras maiúsculas e minúsculas com ou sem acentuação."
            );
            return;
        }

        if ($matches[0] != $obj->$attribute) {
            $obj->addError(
                $attribute,
                "o nome deve conter somente letras maiúsculas e minúsculas com ou sem acentuação."
            );
            return;
        }
    }

    public static function CPF($attribute, $object): void
    {

        if ($object->$attribute === null || $object->$attribute === '') {
            return;
        }

        if (!CPF::isValid($object->$attribute)) {
            $object->addError($attribute, "não é um CPF válido");
            return;
        }
    }

    public static function state($attribute, $object): void
    {

        if ($object->$attribute === null || $object->$attribute === '') {
            return;
        }

        $states = [
            "AC",
            "AL",
            "AP",
            "AM",
            "BA",
            "CE",
            "DF",
            "ES",
            "GO",
            "MA",
            "MT",
            "MS",
            "MG",
            "PA",
            "PB",
            "PR",
            "PE",
            "PI",
            "RJ",
            "RN",
            "RS",
            "RO",
            "RR",
            "SC",
            "SP",
            "SE",
            "TO"
        ];

        foreach ($states as $state) {
            if ($object->$attribute == $state) {
                return;
            }
        }

        $allStates = implode(", ", $states);

        $object->addError($attribute, "Deve ser uma sigla como: $allStates");
    }

    public static function crmv($attribute, $object): void
    {

        if ($object->$attribute === null || $object->$attribute === '') {
            return;
        }

        if (!CRMV::isValid($object->$attribute)) {
            $object->addError($attribute, "Deve ser um CRMV válido.");
        }
    }

    public static function numeric($attribute, $object): void
    {

        if ($object->$attribute === null) {
            return;
        }

        if (!StringUtils::isNumeric($object->$attribute)) {
            $object->addError($attribute, "deve ser um número");
            return;
        }
    }

    public static function uniqueness($fields, $object)
    {
        if (!is_array($fields)) {
            $fields = [$fields];
        }

        $table = $object::table();
        $conditions = implode(' AND ', array_map(fn($field) => "{$field} = :{$field}", $fields));

        $sql = <<<SQL
            SELECT id FROM {$table} WHERE {$conditions};
        SQL;

        if (!$object->newRecord()) {
            $sql = <<<SQL
                SELECT id FROM {$table} WHERE {$conditions} AND NOT id = :id;
            SQL;
        }

        $pdo = Database::getDatabaseConn();
        $stmt = $pdo->prepare($sql);

        foreach ($fields as $field) {
            $stmt->bindValue($field, $object->$field);
        }

        if (!$object->newRecord()) {
            $stmt->bindValue(":id", $object->id);
        }

        $stmt->execute();

        if ($stmt->rowCount() !== 0) {
            foreach ($fields as $field) {
                $object->addError($field, 'já existe um registro com esse dado');
            }
            return false;
        }

        return true;
    }
}
