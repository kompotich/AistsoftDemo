<?php

declare(strict_types=1);

namespace AistsoftDemo\Dto;

class TimelineDto
{
    public ?CompanySummary $company = null;
    public ?EntrepreneurSummary $entrepreneur = null;
    public ?PersonSummary $person = null;

    public array $data = [];

    public ?ApiMeta $meta = null;
}
