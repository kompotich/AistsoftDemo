<?php

declare(strict_types=1);

namespace AistsoftDemo\Http\Parameter;

enum ContractsSort: string
{
    case DateAscending = 'date';
    case DateDescending = '-date';
    case PriceAscending = 'price';
    case PriceDescending = '-price';
}
