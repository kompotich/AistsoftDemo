<?php

declare(strict_types=1);

namespace AistsoftDemo\Database\Entity;

class PersonEntity extends Entity
{
    public static function tableName(): string
    {
        return 'person';
    }

    public static function columns(): array
    {
        return self::fields('data', [
            'inn' => ['ИНН', 'TEXT'],
            'full_name' => ['ФИО', 'TEXT'],
            'managers_json' => ['Руковод', 'TEXT'],
            'founders_json' => ['Учред', 'TEXT'],
            'entrepreneurs_json' => ['ИП', 'TEXT'],
            'trademarks_json' => ['ТоварЗнак', 'TEXT'],
            'bankruptcy_messages_json' => ['ЕФРСБ', 'TEXT'],
            'unreliable_supplier' => ['НедобПост', 'INTEGER'],
            'unreliable_supplier_records_json' => ['НедобПостЗап', 'TEXT'],
            'mass_manager' => ['МассРуковод', 'INTEGER'],
            'mass_founder' => ['МассУчред', 'INTEGER'],
            'sanctions' => ['Санкции', 'INTEGER'],
            'sanctioned_countries_json' => ['СанкцииСтраны', 'TEXT'],
        ]) + self::metadataColumns();
    }
}
