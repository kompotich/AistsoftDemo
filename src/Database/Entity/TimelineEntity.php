<?php

declare(strict_types=1);

namespace AistsoftDemo\Database\Entity;

class TimelineEntity extends Entity
{
    public static function tableName(): string
    {
        return 'timeline';
    }

    public static function columns(): array
    {
        return self::companySummaryColumns()
            + self::entrepreneurSummaryColumns()
            + self::fields('person', [
                'person_inn' => ['ИНН', 'TEXT'],
                'person_full_name' => ['ФИО', 'TEXT'],
            ])
            + self::fields('', [
                'events_json' => ['data', 'TEXT'],
            ])
            + self::metadataColumns();
    }
}
