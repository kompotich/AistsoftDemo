<?php

declare(strict_types=1);

namespace AistsoftDemo\Database\Entity;

class ContractsEntity extends Entity
{
    public static function tableName(): string
    {
        return 'contracts';
    }

    public static function columns(): array
    {
        return self::companySummaryColumns()
            + self::entrepreneurSummaryColumns()
            + self::paginationColumns()
            + self::fields('data', [
                'records_json' => ['Записи', 'TEXT'],
            ])
            + self::metadataColumns();
    }
}
