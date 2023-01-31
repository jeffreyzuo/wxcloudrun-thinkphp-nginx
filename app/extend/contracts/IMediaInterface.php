<?php

namespace app\extend\contracts;

interface IMediaInterface extends IMsgInterface
{
    /**
     * @return string
     */
    public function getMediaId(): string;
}