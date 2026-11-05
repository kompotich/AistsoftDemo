<?php

declare(strict_types=1);

namespace AistsoftDemo\Database\Entity;

class BankruptcyMessagesEntity extends Entity
{
    public static function tableName(): string
    {
        return 'bankruptcy_messages';
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
