<?php

declare(strict_types=1);

namespace App\DTO\Response;

class BooleanResponseDTO extends ResponseDTO
{
    public const OBJECT = 'bool';

    /**
     * @param bool $data
     */
    public function __construct(bool $data)
    {
        parent::__construct(self::OBJECT, $data);
    }
}