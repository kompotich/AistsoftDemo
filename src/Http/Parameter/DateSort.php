<?php

declare(strict_types=1);

namespace AistsoftDemo\Http\Parameter;

enum DateSort: string
{
    case Ascending = 'date';
    case Descending = '-date';
}
