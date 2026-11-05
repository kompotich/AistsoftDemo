<?php

declare(strict_types=1);

namespace AistsoftDemo\Dto;

class CompanyDto
{
    public ?CompanyData $data = null;
    public ?array $source_data = null;
    public ?ApiMeta $meta = null;
}

class CompanyData
{
    public ?string $ogrn = null;
    public ?string $inn = null;
    public ?string $kpp = null;
    public ?string $okpo = null;
    public ?string $registration_date = null;
    public ?string $ogrn_date = null;
    public ?string $short_name = null;
    public ?string $full_name = null;
    public ?string $english_name = null;
    public ?array $status = null;
    public ?array $liquidation = null;
    public ?array $region = null;
    public ?array $legal_address = null;
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
    public ?array $authorized_capital = null;
    public ?array $managing_organization = null;
    public array $managers = [];
    public ?array $founders = null;
    public array $managing_organization_affiliations = [];
    public array $founder_affiliations = [];
    public ?array $joint_stock_holder_register = null;
    public array $licenses = [];
    public array $trademarks = [];
    public array $subdivisions = [];
    public array $predecessor = [];
    public array $successors = [];
    public ?string $extract_date = null;
    public ?array $contacts = null;
    public ?array $taxes = null;
    public ?array $sme_registry = null;
    public array $sme_support = [];
    public ?int $average_headcount = null;
    public array $bankruptcy_messages = [];
    public ?bool $unreliable_supplier = null;
    public array $unreliable_supplier_records = [];
    public ?bool $disqualified_persons = null;
    public ?bool $mass_manager = null;
    public ?bool $mass_founder = null;
    public ?bool $illegal_financial_activity = null;
    public ?string $illegal_financial_activity_status = null;
    public ?bool $sanctions = null;
    public array $sanctioned_countries = [];
    public ?bool $sanctioned_founder = null;
}
