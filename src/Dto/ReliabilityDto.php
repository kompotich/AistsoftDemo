<?php

declare(strict_types=1);

namespace AistsoftDemo\Dto;

class ReliabilityDto
{
    public ?CompanySummary $company = null;
    public ?EntrepreneurSummary $entrepreneur = null;
    public ?ReliabilityData $data = null;
    public ?ApiMeta $meta = null;
}

class ReliabilityData
{
    public ?int $business_scale = null;
    public ?array $nominee_risk = null;
    public ?array $financial_risks = null;
    public ?array $risk_balance = null;
    public array $factors = [];
}
