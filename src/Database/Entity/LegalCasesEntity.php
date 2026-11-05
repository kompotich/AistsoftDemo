<?php

declare(strict_types=1);

namespace AistsoftDemo\Database\Entity;

class LegalCasesEntity extends Entity
{
    public static function tableName(): string
    {
        return 'legal_cases';
    }

    public static function columns(): array
    {
        return self::companySummaryColumns()
            + self::entrepreneurSummaryColumns()
            + self::paginationColumns()
            + self::fields('data', [
                'data_total_claim_amount' => ['ОбщСуммИск', 'REAL'],
                'data_records_json' => ['Записи', 'TEXT'],
            ])
            + self::metadataColumns();
    }
}
