<?php

declare(strict_types=1);

namespace AistsoftDemo\Database\Entity;

abstract class Entity
{
    public ?int $id = null;
    public array $requestParameters = [];
    public array $values = [];
    public string $fetchedAt = '';

    abstract public static function tableName(): string;

    abstract public static function columns(): array;

    protected static function fields(string $prefix, array $fields): array
    {
        $columns = [];

        foreach ($fields as $column => [$sourceField, $type]) {
            $columns[$column] = [
                'path' => $prefix === '' ? $sourceField : $prefix . '.' . $sourceField,
                'type' => $type,
            ];
        }

        return $columns;
    }

    protected static function metadataColumns(): array
    {
        return [
            'meta_status' => ['path' => 'meta.status', 'type' => 'TEXT'],
            'meta_today_request_count' => ['path' => 'meta.today_request_count', 'type' => 'INTEGER'],
            'meta_balance' => ['path' => 'meta.balance', 'type' => 'REAL'],
            'meta_message' => ['path' => 'meta.message', 'type' => 'TEXT'],
        ];
    }

    protected static function companySummaryColumns(): array
    {
        return self::fields('company', [
            'company_ogrn' => ['ОГРН', 'TEXT'],
            'company_inn' => ['ИНН', 'TEXT'],
            'company_kpp' => ['КПП', 'TEXT'],
            'company_short_name' => ['НаимСокр', 'TEXT'],
            'company_full_name' => ['НаимПолн', 'TEXT'],
            'company_registration_date' => ['ДатаРег', 'TEXT'],
            'company_status' => ['Статус', 'TEXT'],
            'company_liquidation_date' => ['ДатаЛикв', 'TEXT'],
            'company_region_code' => ['РегионКод', 'TEXT'],
            'company_legal_address' => ['ЮрАдрес', 'TEXT'],
            'company_okved' => ['ОКВЭД', 'TEXT'],
        ]);
    }

    protected static function entrepreneurSummaryColumns(): array
    {
        return self::fields('entrepreneur', [
            'entrepreneur_ogrnip' => ['ОГРНИП', 'TEXT'],
            'entrepreneur_inn' => ['ИНН', 'TEXT'],
            'entrepreneur_full_name' => ['ФИО', 'TEXT'],
            'entrepreneur_type' => ['Тип', 'TEXT'],
            'entrepreneur_registration_date' => ['ДатаРег', 'TEXT'],
            'entrepreneur_status' => ['Статус', 'TEXT'],
            'entrepreneur_termination_date' => ['ДатаПрекращ', 'TEXT'],
            'entrepreneur_region_code' => ['РегионКод', 'TEXT'],
            'entrepreneur_okved' => ['ОКВЭД', 'TEXT'],
        ]);
    }

    protected static function paginationColumns(bool $hasTotalRecords = true): array
    {
        $columns = [];
        if ($hasTotalRecords) {
            $columns['data_total_records'] = ['path' => 'data.ЗапВсего', 'type' => 'INTEGER'];
        }

        return $columns + [
            'data_total_pages' => ['path' => 'data.СтрВсего', 'type' => 'INTEGER'],
            'data_current_page' => ['path' => 'data.СтрТекущ', 'type' => 'INTEGER'],
        ];
    }
}
