<?php

declare(strict_types=1);

namespace AistsoftDemo\Database\Entity;

class FinancesEntity extends Entity
{
    public static function tableName(): string
    {
        return 'finances';
    }

    public static function columns(): array
    {
        $columns = self::companySummaryColumns();
        for ($year = 2011; $year <= 2025; $year++) {
            $columns['data_year_' . $year . '_json'] = [
                'path' => 'data.' . $year,
                'type' => 'TEXT',
            ];
        }

        return $columns + [
            'bo_nalog_report_id' => ['path' => 'bo.nalog.ru.ID', 'type' => 'INTEGER'],
            'bo_nalog_reports_json' => ['path' => 'bo.nalog.ru.Отчет', 'type' => 'TEXT'],
        ] + self::metadataColumns();
    }
}
