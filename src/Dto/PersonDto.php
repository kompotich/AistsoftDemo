<?php

declare(strict_types=1);

namespace AistsoftDemo\Dto;

class PersonDto
{
    public ?PersonData $data = null;
    public ?ApiMeta $meta = null;
}

class PersonData
{
    public ?string $inn = null;
    public ?string $full_name = null;
    public array $managers = [];
    public array $founders = [];
    public array $entrepreneurs = [];
    public array $trademarks = [];
    public array $bankruptcy_messages = [];
    public ?bool $unreliable_supplier = null;
    public array $unreliable_supplier_records = [];
    public ?bool $mass_manager = null;
    public ?bool $mass_founder = null;
    public ?bool $sanctions = null;
    public array $sanctioned_countries = [];
}
