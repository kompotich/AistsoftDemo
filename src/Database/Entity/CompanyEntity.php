<?php

declare(strict_types=1);

namespace AistsoftDemo\Database\Entity;

class CompanyEntity extends Entity
{
    public static function tableName(): string
    {
        return 'company';
    }

    public static function columns(): array
    {
        return self::fields('data', [
            'ogrn' => ['ОГРН', 'TEXT'],
            'inn' => ['ИНН', 'TEXT'],
            'kpp' => ['КПП', 'TEXT'],
            'okpo' => ['ОКПО', 'TEXT'],
            'registration_date' => ['ДатаРег', 'TEXT'],
            'ogrn_date' => ['ДатаОГРН', 'TEXT'],
            'short_name' => ['НаимСокр', 'TEXT'],
            'full_name' => ['НаимПолн', 'TEXT'],
            'english_name' => ['НаимАнгл', 'TEXT'],
            'status_json' => ['Статус', 'TEXT'],
            'liquidation_json' => ['Ликвид', 'TEXT'],
            'region_json' => ['Регион', 'TEXT'],
            'legal_address_json' => ['ЮрАдрес', 'TEXT'],
            'okved_json' => ['ОКВЭД', 'TEXT'],
            'additional_okved_json' => ['ОКВЭДДоп', 'TEXT'],
            'okopf_json' => ['ОКОПФ', 'TEXT'],
            'okfs_json' => ['ОКФС', 'TEXT'],
            'okogu_json' => ['ОКОГУ', 'TEXT'],
            'okato_json' => ['ОКАТО', 'TEXT'],
            'oktmo_json' => ['ОКТМО', 'TEXT'],
            'registration_tax_office_json' => ['РегФНС', 'TEXT'],
            'current_tax_office_json' => ['ТекФНС', 'TEXT'],
            'pension_registration_json' => ['РегПФР', 'TEXT'],
            'social_insurance_registration_json' => ['РегФСС', 'TEXT'],
            'authorized_capital_json' => ['УстКап', 'TEXT'],
            'managing_organization_json' => ['УпрОрг', 'TEXT'],
            'managers_json' => ['Руковод', 'TEXT'],
            'founders_json' => ['Учред', 'TEXT'],
            'managing_organization_affiliations_json' => ['СвязУпрОрг', 'TEXT'],
            'founder_affiliations_json' => ['СвязУчред', 'TEXT'],
            'joint_stock_holder_register_json' => ['ДержРеестрАО', 'TEXT'],
            'licenses_json' => ['Лиценз', 'TEXT'],
            'trademarks_json' => ['ТоварЗнак', 'TEXT'],
            'subdivisions_json' => ['Подразд', 'TEXT'],
            'predecessor_json' => ['Правопредш', 'TEXT'],
            'successors_json' => ['Правопреем', 'TEXT'],
            'extract_date' => ['ДатаВып', 'TEXT'],
            'contacts_json' => ['Контакты', 'TEXT'],
            'taxes_json' => ['Налоги', 'TEXT'],
            'sme_registry_json' => ['РМСП', 'TEXT'],
            'sme_support_json' => ['ПоддержМСП', 'TEXT'],
            'average_headcount' => ['СЧР', 'INTEGER'],
            'bankruptcy_messages_json' => ['ЕФРСБ', 'TEXT'],
            'unreliable_supplier' => ['НедобПост', 'INTEGER'],
            'unreliable_supplier_records_json' => ['НедобПостЗап', 'TEXT'],
            'disqualified_persons' => ['ДисквЛица', 'INTEGER'],
            'mass_manager' => ['МассРуковод', 'INTEGER'],
            'mass_founder' => ['МассУчред', 'INTEGER'],
            'illegal_financial_activity' => ['НелегалФин', 'INTEGER'],
            'illegal_financial_activity_status' => ['НелегалФинСтатус', 'TEXT'],
            'sanctions' => ['Санкции', 'INTEGER'],
            'sanctioned_countries_json' => ['СанкцииСтраны', 'TEXT'],
            'sanctioned_founder' => ['СанкцУчр', 'INTEGER'],
        ]) + [
            'source_data_json' => ['path' => 'source_data', 'type' => 'TEXT'],
        ] + self::metadataColumns();
    }
}
