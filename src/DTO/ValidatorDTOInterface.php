<?php

declare(strict_types=1);

namespace App\DTO;

use Symfony\Component\Validator\Mapping\ClassMetadata;

interface ValidatorDTOInterface
{
    /**
     * @param ClassMetadata $metadata
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata): void;
}