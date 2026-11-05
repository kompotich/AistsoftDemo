<?php

declare(strict_types=1);

namespace AistsoftDemo\Http\Parameter;

enum SearchObject: string
{
    case Organizations = 'org';
    case Entrepreneurs = 'ent';
}
