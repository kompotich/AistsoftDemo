<?php

declare(strict_types=1);

namespace AistsoftDemo\Database\Entity;

class FedresursEntity extends Entity
{
    public static function tableName(): string
    {
        return 'fedresurs';
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
