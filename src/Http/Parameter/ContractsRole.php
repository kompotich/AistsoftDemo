<?php

declare(strict_types=1);

namespace AistsoftDemo\Http\Parameter;

enum ContractsRole: string
{
    case Customer = 'customer';
    case Supplier = 'supplier';
}
