<?php

declare(strict_types=1);

namespace AistsoftDemo\Http\Parameter;

enum LegalCasesRole: string
{
    case Plaintiff = 'plaintiff';
    case Defendant = 'defendant';
}
