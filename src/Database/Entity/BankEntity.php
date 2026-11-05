<?php

declare(strict_types=1);

namespace AistsoftDemo\Database\Entity;

class BankEntity extends Entity
{
    public static function tableName(): string
    {
        return 'bank';
    }

    public static function columns(): array
    {
        return self::fields('data', [
            'bic' => ['БИК', 'TEXT'],
            'name' => ['Наим', 'TEXT'],
            'english_name' => ['НаимАнгл', 'TEXT'],
            'address' => ['Адрес', 'TEXT'],
            'type' => ['Тип', 'TEXT'],
            'correspondent_account_json' => ['КорСчет', 'TEXT'],
            'accounts_json' => ['Счета', 'TEXT'],
        ]) + self::metadataColumns();
    }
}
