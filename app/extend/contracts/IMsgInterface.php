<?php
namespace app\extend\contracts;
interface IMsgInterface
{
    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @return array
     */
    public function transformForJsonRequest(): array;

    /**
     * @return string
     */
    public function transformToXml(): string;
}