<?php

declare(strict_types=1);

namespace AistsoftDemo\Dto;

class BankDto
{
    public ?BankData $data = null;
    public ?ApiMeta $meta = null;
}

class BankData
{
    public ?string $bic = null;
    public ?string $name = null;
    public ?string $english_name = null;
    public ?string $address = null;
    public ?string $type = null;
    public ?array $correspondent_account = null;
    public array $accounts = [];
}
