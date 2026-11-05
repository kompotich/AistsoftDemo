<?php

declare(strict_types=1);

namespace AistsoftDemo\Dto;

class EnforcementsDto
{
    public ?CompanySummary $company = null;
    public ?EnforcementsData $data = null;
    public ?ApiMeta $meta = null;
}

class EnforcementsData
{
    public ?int $total_pages = null;
    public ?int $current_page = null;

    public array $records = [];
}
