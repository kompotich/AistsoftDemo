<?php

declare(strict_types=1);

namespace AistsoftDemo\Client;

use AistsoftDemo\Dto\BankruptcyMessagesDto;
use AistsoftDemo\Dto\BankDto;
use AistsoftDemo\Dto\CompanyDto;
use AistsoftDemo\Dto\ContractsDto;
use AistsoftDemo\Dto\EnforcementsDto;
use AistsoftDemo\Dto\EntrepreneurDto;
use AistsoftDemo\Dto\FedresursDto;
use AistsoftDemo\Dto\FinancesDto;
use AistsoftDemo\Dto\InspectionsDto;
use AistsoftDemo\Dto\LegalCasesDto;
use AistsoftDemo\Dto\PersonDto;
use AistsoftDemo\Dto\ReliabilityDto;
use AistsoftDemo\Dto\SearchDto;
use AistsoftDemo\Dto\TimelineDto;
use ReflectionNamedType;
use ReflectionProperty;
use RuntimeException;
use stdClass;

class CheckoResponseMapper
{
    private const META_MAPPING = [
        'meta.status' => 'meta.status',
        'meta.today_request_count' => 'meta.today_request_count',
        'meta.balance' => 'meta.balance',
        'meta.message' => 'meta.message',
    ];

    private const COMPANY_MAPPING = [
        'company.ОГРН' => 'company.ogrn',
        'company.ИНН' => 'company.inn',
        'company.КПП' => 'company.kpp',
        'company.НаимСокр' => 'company.short_name',
        'company.НаимПолн' => 'company.full_name',
        'company.ДатаРег' => 'company.registration_date',
        'company.Статус' => 'company.status',
        'company.ДатаЛикв' => 'company.liquidation_date',
        'company.РегионКод' => 'company.region_code',
        'company.ЮрАдрес' => 'company.legal_address',
        'company.ОКВЭД' => 'company.okved',
    ];

    private const ENTREPRENEUR_MAPPING = [
        'entrepreneur.ОГРНИП' => 'entrepreneur.ogrnip',
        'entrepreneur.ИНН' => 'entrepreneur.inn',
        'entrepreneur.ФИО' => 'entrepreneur.full_name',
        'entrepreneur.Тип' => 'entrepreneur.type',
        'entrepreneur.ДатаРег' => 'entrepreneur.registration_date',
        'entrepreneur.Статус' => 'entrepreneur.status',
        'entrepreneur.ДатаПрекращ' => 'entrepreneur.termination_date',
        'entrepreneur.РегионКод' => 'entrepreneur.region_code',
        'entrepreneur.ОКВЭД' => 'entrepreneur.okved',
    ];

    private const FIELD_MAPPINGS = [
        BankruptcyMessagesDto::class => [
            ...self::COMPANY_MAPPING,
            ...self::ENTREPRENEUR_MAPPING,
            'data.ЗапВсего' => 'data.total_records',
            'data.СтрВсего' => 'data.total_pages',
            'data.СтрТекущ' => 'data.current_page',
            'data.Записи' => 'data.records',
            ...self::META_MAPPING,
        ],
        BankDto::class => [
            'data.БИК' => 'data.bic',
            'data.Наим' => 'data.name',
            'data.НаимАнгл' => 'data.english_name',
            'data.Адрес' => 'data.address',
            'data.Тип' => 'data.type',
            'data.КорСчет' => 'data.correspondent_account',
            'data.Счета' => 'data.accounts',
            ...self::META_MAPPING,
        ],
        CompanyDto::class => [
            'data.ОГРН' => 'data.ogrn',
            'data.ИНН' => 'data.inn',
            'data.КПП' => 'data.kpp',
            'data.ОКПО' => 'data.okpo',
            'data.ДатаРег' => 'data.registration_date',
            'data.ДатаОГРН' => 'data.ogrn_date',
            'data.НаимСокр' => 'data.short_name',
            'data.НаимПолн' => 'data.full_name',
            'data.НаимАнгл' => 'data.english_name',
            'data.Статус' => 'data.status',
            'data.Ликвид' => 'data.liquidation',
            'data.Регион' => 'data.region',
            'data.ЮрАдрес' => 'data.legal_address',
            'data.ОКВЭД' => 'data.okved',
            'data.ОКВЭДДоп' => 'data.additional_okved',
            'data.ОКОПФ' => 'data.okopf',
            'data.ОКФС' => 'data.okfs',
            'data.ОКОГУ' => 'data.okogu',
            'data.ОКАТО' => 'data.okato',
            'data.ОКТМО' => 'data.oktmo',
            'data.РегФНС' => 'data.registration_tax_office',
            'data.ТекФНС' => 'data.current_tax_office',
            'data.РегПФР' => 'data.pension_registration',
            'data.РегФСС' => 'data.social_insurance_registration',
            'data.УстКап' => 'data.authorized_capital',
            'data.УпрОрг' => 'data.managing_organization',
            'data.Руковод' => 'data.managers',
            'data.Учред' => 'data.founders',
            'data.СвязУпрОрг' => 'data.managing_organization_affiliations',
            'data.СвязУчред' => 'data.founder_affiliations',
            'data.ДержРеестрАО' => 'data.joint_stock_holder_register',
            'data.Лиценз' => 'data.licenses',
            'data.ТоварЗнак' => 'data.trademarks',
            'data.Подразд' => 'data.subdivisions',
            'data.Правопредш' => 'data.predecessor',
            'data.Правопреем' => 'data.successors',
            'data.ДатаВып' => 'data.extract_date',
            'data.Контакты' => 'data.contacts',
            'data.Налоги' => 'data.taxes',
            'data.РМСП' => 'data.sme_registry',
            'data.ПоддержМСП' => 'data.sme_support',
            'data.СЧР' => 'data.average_headcount',
            'data.ЕФРСБ' => 'data.bankruptcy_messages',
            'data.НедобПост' => 'data.unreliable_supplier',
            'data.НедобПостЗап' => 'data.unreliable_supplier_records',
            'data.ДисквЛица' => 'data.disqualified_persons',
            'data.МассРуковод' => 'data.mass_manager',
            'data.МассУчред' => 'data.mass_founder',
            'data.НелегалФин' => 'data.illegal_financial_activity',
            'data.НелегалФинСтатус' => 'data.illegal_financial_activity_status',
            'data.Санкции' => 'data.sanctions',
            'data.СанкцииСтраны' => 'data.sanctioned_countries',
            'data.СанкцУчр' => 'data.sanctioned_founder',
            'source_data' => 'source_data',
            ...self::META_MAPPING,
        ],
        ContractsDto::class => [
            ...self::COMPANY_MAPPING,
            ...self::ENTREPRENEUR_MAPPING,
            'data.ЗапВсего' => 'data.total_records',
            'data.СтрВсего' => 'data.total_pages',
            'data.СтрТекущ' => 'data.current_page',
            'data.Записи' => 'data.records',
            ...self::META_MAPPING,
        ],
        EnforcementsDto::class => [
            ...self::COMPANY_MAPPING,
            'data.СтрВсего' => 'data.total_pages',
            'data.СтрТекущ' => 'data.current_page',
            'data.Записи' => 'data.records',
            ...self::META_MAPPING,
        ],
        EntrepreneurDto::class => [
            'data.ОГРНИП' => 'data.ogrnip',
            'data.ИНН' => 'data.inn',
            'data.ОКПО' => 'data.okpo',
            'data.ДатаРег' => 'data.registration_date',
            'data.ДатаОГРНИП' => 'data.ogrnip_date',
            'data.ФИО' => 'data.full_name',
            'data.Тип' => 'data.type',
            'data.ТипСокр' => 'data.short_type',
            'data.Пол' => 'data.gender',
            'data.Статус' => 'data.status',
            'data.Прекращ' => 'data.termination',
            'data.Регион' => 'data.region',
            'data.НасПункт' => 'data.locality',
            'data.ОКВЭД' => 'data.okved',
            'data.ОКВЭДДоп' => 'data.additional_okved',
            'data.ОКОПФ' => 'data.okopf',
            'data.ОКФС' => 'data.okfs',
            'data.ОКОГУ' => 'data.okogu',
            'data.ОКАТО' => 'data.okato',
            'data.ОКТМО' => 'data.oktmo',
            'data.РегФНС' => 'data.registration_tax_office',
            'data.ТекФНС' => 'data.current_tax_office',
            'data.РегПФР' => 'data.pension_registration',
            'data.РегФСС' => 'data.social_insurance_registration',
            'data.СвязРуковод' => 'data.manager_affiliations',
            'data.СвязУчред' => 'data.founder_affiliations',
            'data.Лиценз' => 'data.licenses',
            'data.ТоварЗнак' => 'data.trademarks',
            'data.ДатаВып' => 'data.extract_date',
            'data.Налоги' => 'data.taxes',
            'data.РМСП' => 'data.sme_registry',
            'data.ПоддержМСП' => 'data.sme_support',
            'data.ЕФРСБ' => 'data.bankruptcy_messages',
            'data.НедобПост' => 'data.unreliable_supplier',
            'data.НедобПостЗап' => 'data.unreliable_supplier_records',
            'data.МассРуковод' => 'data.mass_manager',
            'data.МассУчред' => 'data.mass_founder',
            'source_data' => 'source_data',
            ...self::META_MAPPING,
        ],
        FedresursDto::class => [
            ...self::COMPANY_MAPPING,
            ...self::ENTREPRENEUR_MAPPING,
            'data.ЗапВсего' => 'data.total_records',
            'data.СтрВсего' => 'data.total_pages',
            'data.СтрТекущ' => 'data.current_page',
            'data.Записи' => 'data.records',
            ...self::META_MAPPING,
        ],
        FinancesDto::class => [
            ...self::COMPANY_MAPPING,
            'data.2011' => 'data.year_2011',
            'data.2012' => 'data.year_2012',
            'data.2013' => 'data.year_2013',
            'data.2014' => 'data.year_2014',
            'data.2015' => 'data.year_2015',
            'data.2016' => 'data.year_2016',
            'data.2017' => 'data.year_2017',
            'data.2018' => 'data.year_2018',
            'data.2019' => 'data.year_2019',
            'data.2020' => 'data.year_2020',
            'data.2021' => 'data.year_2021',
            'data.2022' => 'data.year_2022',
            'data.2023' => 'data.year_2023',
            'data.2024' => 'data.year_2024',
            'data.2025' => 'data.year_2025',
            'bo.nalog.ru.ID' => 'bo_nalog_ru.id',
            'bo.nalog.ru.Отчет' => 'bo_nalog_ru.reports',
            ...self::META_MAPPING,
        ],
        InspectionsDto::class => [
            ...self::COMPANY_MAPPING,
            ...self::ENTREPRENEUR_MAPPING,
            'data.ЗапВсего' => 'data.total_records',
            'data.СтрВсего' => 'data.total_pages',
            'data.СтрТекущ' => 'data.current_page',
            'data.Записи' => 'data.records',
            ...self::META_MAPPING,
        ],
        LegalCasesDto::class => [
            ...self::COMPANY_MAPPING,
            ...self::ENTREPRENEUR_MAPPING,
            'data.ЗапВсего' => 'data.total_records',
            'data.СтрВсего' => 'data.total_pages',
            'data.СтрТекущ' => 'data.current_page',
            'data.ОбщСуммИск' => 'data.total_claim_amount',
            'data.Записи' => 'data.records',
            ...self::META_MAPPING,
        ],
        PersonDto::class => [
            'data.ИНН' => 'data.inn',
            'data.ФИО' => 'data.full_name',
            'data.Руковод' => 'data.managers',
            'data.Учред' => 'data.founders',
            'data.ИП' => 'data.entrepreneurs',
            'data.ТоварЗнак' => 'data.trademarks',
            'data.ЕФРСБ' => 'data.bankruptcy_messages',
            'data.НедобПост' => 'data.unreliable_supplier',
            'data.НедобПостЗап' => 'data.unreliable_supplier_records',
            'data.МассРуковод' => 'data.mass_manager',
            'data.МассУчред' => 'data.mass_founder',
            'data.Санкции' => 'data.sanctions',
            'data.СанкцииСтраны' => 'data.sanctioned_countries',
            ...self::META_MAPPING,
        ],
        ReliabilityDto::class => [
            ...self::COMPANY_MAPPING,
            ...self::ENTREPRENEUR_MAPPING,
            'data.Масштаб' => 'data.business_scale',
            'data.Номинальность' => 'data.nominee_risk',
            'data.ФинРиски' => 'data.financial_risks',
            'data.Баланс' => 'data.risk_balance',
            'data.Факторы' => 'data.factors',
            ...self::META_MAPPING,
        ],
        SearchDto::class => [
            'data.СтрВсего' => 'data.total_pages',
            'data.СтрТекущ' => 'data.current_page',
            'data.Записи' => 'data.records',
            ...self::META_MAPPING,
        ],
        TimelineDto::class => [
            ...self::COMPANY_MAPPING,
            ...self::ENTREPRENEUR_MAPPING,
            'person.ИНН' => 'person.inn',
            'person.ФИО' => 'person.full_name',
            'data' => 'data',
            ...self::META_MAPPING,
        ],
    ];

    public function map(string $dtoClass, stdClass $response): object
    {
        $mapping = self::FIELD_MAPPINGS[$dtoClass] ?? null;
        if ($mapping === null) {
            throw new RuntimeException(sprintf('Не настроено преобразование ответа Checko для DTO %s', $dtoClass));
        }

        $dto = new $dtoClass();
        foreach ($mapping as $sourcePath => $dtoPath) {
            [$found, $value] = $this->readPath($response, $sourcePath);
            if (!$found) {
                [$found, $value] = $this->readPath($response, $dtoPath);
            }
            if (!$found) {
                continue;
            }

            $this->setDtoPath($dto, $dtoPath, $value);
        }

        return $dto;
    }

    private function readPath(mixed $value, string $path): array
    {
        foreach (explode('.', $path) as $segment) {
            if (is_object($value) && property_exists($value, $segment)) {
                $value = $value->{$segment};
            } elseif (is_array($value) && array_key_exists($segment, $value)) {
                $value = $value[$segment];
            } else {
                return [false, null];
            }
        }

        return [true, $value];
    }

    private function setDtoPath(object $dto, string $path, mixed $value): void
    {
        $segments = explode('.', $path);
        $property = array_pop($segments);
        $target = $dto;

        foreach ($segments as $segment) {
            $nested = $target->{$segment} ?? null;
            if ($nested === null) {
                $type = (new ReflectionProperty($target, $segment))->getType();
                if (!$type instanceof ReflectionNamedType || $type->isBuiltin()) {
                    throw new RuntimeException(sprintf('Не удалось определить вложенный DTO для поля %s', $segment));
                }

                $nested = new ($type->getName())();
                $target->{$segment} = $nested;
            }

            $target = $nested;
        }

        $type = (new ReflectionProperty($target, $property))->getType();
        if (!$type instanceof ReflectionNamedType) {
            throw new RuntimeException(sprintf('Не удалось определить тип поля DTO %s', $property));
        }
        if ($value === null && !$type->allowsNull()) {
            return;
        }

        $target->{$property} = $this->makeValue($value, $type);
    }

    private function makeValue(mixed $value, ReflectionNamedType $type): mixed
    {
        if ($value === null) {
            return null;
        }

        $typeName = $type->getName();
        if ($typeName === 'array') {
            if (!is_array($value) && !$value instanceof stdClass) {
                throw new RuntimeException('Checko API вернул значение, которое не соответствует типу массива DTO');
            }

            return $this->toArray($value);
        }

        if (!$type->isBuiltin()) {
            throw new RuntimeException('Checko API вернул значение, которое не соответствует вложенному DTO');
        }

        if ($typeName === 'string' && is_scalar($value)) {
            return (string) $value;
        }
        if ($typeName === 'int' && (is_int($value) || (is_string($value) && preg_match('/^-?\d+$/', $value)))) {
            return (int) $value;
        }
        if ($typeName === 'float' && is_numeric($value)) {
            return (float) $value;
        }
        if ($typeName === 'bool' && is_bool($value)) {
            return $value;
        }

        throw new RuntimeException(sprintf('Checko API вернул значение, которое не соответствует типу DTO %s', $typeName));
    }

    private function toArray(array|stdClass $value): array
    {
        $array = $value instanceof stdClass ? get_object_vars($value) : $value;
        foreach ($array as $key => $item) {
            if ($item instanceof stdClass || is_array($item)) {
                $array[$key] = $this->toArray($item);
            }
        }

        return $array;
    }
}
