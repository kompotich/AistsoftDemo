<?php

declare(strict_types=1);

namespace AistsoftDemo\Database\Entity;

class ReliabilityEntity extends Entity
{
    public static function tableName(): string
    {
        return 'reliability';
    }

    public static function columns(): array
    {
        return self::companySummaryColumns()
            + self::entrepreneurSummaryColumns()
            + self::fields('data', [
                'business_scale' => ['Масштаб', 'INTEGER'],
                'nominee_risk_json' => ['Номинальность', 'TEXT'],
                'financial_risks_json' => ['ФинРиски', 'TEXT'],
                'risk_balance_json' => ['Баланс', 'TEXT'],
                'factors_json' => ['Факторы', 'TEXT'],
            ])
            + self::metadataColumns();
    }
}
