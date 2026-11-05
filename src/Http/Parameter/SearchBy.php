<?php

declare(strict_types=1);

namespace AistsoftDemo\Http\Parameter;

enum SearchBy: string
{
    case Name = 'name';
    case FounderName = 'founder-name';
    case LeaderName = 'leader-name';
    case Okved = 'okved';
    case RegistrationDate = 'reg-date';
    case UpdateDate = 'upd-date';
    case Phone = 'phone';
    case Email = 'email';
}
