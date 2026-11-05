<?php

declare(strict_types=1);

namespace AistsoftDemo\Dto;

class SearchDto
{
    public ?SearchData $data = null;
    public ?ApiMeta $meta = null;
}

class SearchData
{
    public ?int $total_pages = null;
    public ?int $current_page = null;

    public array $records = [];
}
