<?php

declare(strict_types=1);

namespace AistsoftDemo\Dto;

class LegalCasesDto
{
    public ?CompanySummary $company = null;
    public ?EntrepreneurSummary $entrepreneur = null;
    public ?LegalCasesData $data = null;
    public ?ApiMeta $meta = null;
}

class LegalCasesData
{
    public ?int $total_records = null;
    public ?int $total_pages = null;
    public ?int $current_page = null;
    public ?float $total_claim_amount = null;

    public array $records = [];
}
