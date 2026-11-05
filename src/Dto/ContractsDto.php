<?php

declare(strict_types=1);

namespace AistsoftDemo\Dto;

class ContractsDto
{
    public ?CompanySummary $company = null;
    public ?EntrepreneurSummary $entrepreneur = null;
    public ?ContractsData $data = null;
    public ?ApiMeta $meta = null;
}

class ContractsData
{
    public ?int $total_records = null;
    public ?int $total_pages = null;
    public ?int $current_page = null;

    public array $records = [];
}
