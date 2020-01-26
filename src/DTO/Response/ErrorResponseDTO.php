<?php

declare(strict_types=1);

namespace App\DTO\Response;

class ErrorResponseDTO implements \JsonSerializable
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $message;

    /**
     * @var array|null
     */
    private $error;

    /**
     * @inheritdoc
     */
    public function __construct(string $code, string $message, array $error = null)
    {
        $this->code = $code;
        $this->message = $message;
        $this->error = $error;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        $result = [
            'code' => $this->getCode(),
            'message' => $this->getMessage(),
        ];

        if ($this->getError() !== null) {
            $result['error'] = $this->getError();
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @return array|null
     */
    public function getError(): ?array
    {
        return $this->error;
    }
}