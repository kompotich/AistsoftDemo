<?php

declare(strict_types=1);

namespace AistsoftDemo\Database\Entity;

class EntrepreneurEntity extends Entity
{
    public static function tableName(): string
    {
        return 'entrepreneur';
    }

    public static function columns(): array
    {
        return self::fields('data', [
            'ogrnip' => ['ОГРНИП', 'TEXT'],
            'inn' => ['ИНН', 'TEXT'],
            'okpo' => ['ОКПО', 'TEXT'],
            'registration_date' => ['ДатаРег', 'TEXT'],
            'ogrnip_date' => ['ДатаОГРНИП', 'TEXT'],
            'full_name' => ['ФИО', 'TEXT'],
            'type' => ['Тип', 'TEXT'],
            'short_type' => ['ТипСокр', 'TEXT'],
            'gender' => ['Пол', 'TEXT'],
            'status_json' => ['Статус', 'TEXT'],
            'termination_json' => ['Прекращ', 'TEXT'],
            'region_json' => ['Регион', 'TEXT'],
            'locality' => ['НасПункт', 'TEXT'],
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
            'manager_affiliations_json' => ['СвязРуковод', 'TEXT'],
            'founder_affiliations_json' => ['СвязУчред', 'TEXT'],
            'licenses_json' => ['Лиценз', 'TEXT'],
            'trademarks_json' => ['ТоварЗнак', 'TEXT'],
            'extract_date' => ['ДатаВып', 'TEXT'],
            'taxes_json' => ['Налоги', 'TEXT'],
            'sme_registry_json' => ['РМСП', 'TEXT'],
            'sme_support_json' => ['ПоддержМСП', 'TEXT'],
            'bankruptcy_messages_json' => ['ЕФРСБ', 'TEXT'],
            'unreliable_supplier' => ['НедобПост', 'INTEGER'],
            'unreliable_supplier_records_json' => ['НедобПостЗап', 'TEXT'],
            'mass_manager' => ['МассРуковод', 'INTEGER'],
            'mass_founder' => ['МассУчред', 'INTEGER'],
        ]) + [
            'source_data_json' => ['path' => 'source_data', 'type' => 'TEXT'],
        ] + self::metadataColumns();
    }
}
