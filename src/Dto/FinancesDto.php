<?php

declare(strict_types=1);

namespace AistsoftDemo\Dto;

class FinancesDto
{
    public ?CompanySummary $company = null;
    public ?FinancialData $data = null;
    public ?BoNalogReport $bo_nalog_ru = null;
    public ?ApiMeta $meta = null;
}

class FinancialData
{
    public ?array $year_2011 = null;
    public ?array $year_2012 = null;
    public ?array $year_2013 = null;
    public ?array $year_2014 = null;
    public ?array $year_2015 = null;
    public ?array $year_2016 = null;
    public ?array $year_2017 = null;
    public ?array $year_2018 = null;
    public ?array $year_2019 = null;
    public ?array $year_2020 = null;
    public ?array $year_2021 = null;
    public ?array $year_2022 = null;
    public ?array $year_2023 = null;
    public ?array $year_2024 = null;
    public ?array $year_2025 = null;
}

class BoNalogReport
{
    public ?int $id = null;
    public ?array $reports = null;
}
