<?php

declare(strict_types=1);

namespace AistsoftDemo\Dto;

class EntrepreneurDto
{
    public ?EntrepreneurData $data = null;
    public ?array $source_data = null;
    public ?ApiMeta $meta = null;
}

class EntrepreneurData
{
    public ?string $ogrnip = null;
    public ?string $inn = null;
    public ?string $okpo = null;
    public ?string $registration_date = null;
    public ?string $ogrnip_date = null;
    public ?string $full_name = null;
    public ?string $type = null;
    public ?string $short_type = null;
    public ?string $gender = null;
    public ?array $status = null;
    public ?array $termination = null;
    public ?array $region = null;
    public ?string $locality = null;
    public ?array $okved = null;
    public array $additional_okved = [];
    public ?array $okopf = null;
    public ?array $okfs = null;
    public ?array $okogu = null;
    public ?array $okato = null;
    public ?array $oktmo = null;
    public ?array $registration_tax_office = null;
    public ?array $current_tax_office = null;
    public ?array $pension_registration = null;
    public ?array $social_insurance_registration = null;
    public array $manager_affiliations = [];
    public array $founder_affiliations = [];
    public array $licenses = [];
    public array $trademarks = [];
    public ?string $extract_date = null;
    public ?array $taxes = null;
    public ?array $sme_registry = null;
    public array $sme_support = [];
    public array $bankruptcy_messages = [];
    public ?bool $unreliable_supplier = null;
    public array $unreliable_supplier_records = [];
    public ?bool $mass_manager = null;
    public ?bool $mass_founder = null;
}
