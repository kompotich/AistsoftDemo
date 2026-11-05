<?php

declare(strict_types=1);

namespace AistsoftDemo\Database\Entity;

class SearchEntity extends Entity
{
    public static function tableName(): string
    {
        return 'search';
    }

    public static function columns(): array
    {
        return self::fields('data', [
            'total_pages' => ['СтрВсего', 'INTEGER'],
            'current_page' => ['СтрТекущ', 'INTEGER'],
            'records_json' => ['Записи', 'TEXT'],
        ]) + self::metadataColumns();
    }
}
