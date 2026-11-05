<?php

declare(strict_types=1);

namespace AistsoftDemo\Dto;

class FedresursDto
{
    public ?CompanySummary $company = null;
    public ?EntrepreneurSummary $entrepreneur = null;
    public ?FedresursData $data = null;
    public ?ApiMeta $meta = null;
}

class FedresursData
{
    public ?int $total_records = null;
    public ?int $total_pages = null;
    public ?int $current_page = null;

    public array $records = [];
}
