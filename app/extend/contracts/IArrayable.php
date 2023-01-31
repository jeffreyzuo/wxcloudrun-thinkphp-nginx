<?php

namespace app\extend\contracts;
use ArrayAccess;
interface IArrayable extends ArrayAccess
{
    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray();
}