<?php

declare(strict_types=1);

namespace App\DTO\Response;

class ResponseDTO implements \JsonSerializable
{
    /**
     * @var string
     */
    private $object;

    /**
     * @var mixed
     */
    private $data;

    /**
     * @inheritdoc
     */
    public function __construct(string $object, $data)
    {
        $this->object = $object;
        $this->data = $data;
    }

    /**
     * @inheritdoc
     */
    public function jsonSerialize()
    {
        return [
            'object' => $this->getObject(),
            'data' => $this->getData(),
        ];
    }

    /**
     * @return string
     */
    public function getObject(): string
    {
        return $this->object;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
}