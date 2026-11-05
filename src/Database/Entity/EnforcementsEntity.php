<?php

declare(strict_types=1);

namespace AistsoftDemo\Database\Entity;

class EnforcementsEntity extends Entity
{
    public static function tableName(): string
    {
        return 'enforcements';
    }

    public static function columns(): array
    {
        return self::companySummaryColumns()
            + self::paginationColumns(false)
            + self::fields('data', [
                'records_json' => ['Записи', 'TEXT'],
            ])
            + self::metadataColumns();
    }
}
