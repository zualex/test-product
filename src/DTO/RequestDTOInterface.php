<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\HttpFoundation\Request;

interface RequestDTOInterface
{
    /**
     * @param Request $request
     */
    public function __construct(Request $request);
}