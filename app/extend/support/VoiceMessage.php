<?php

namespace app\extend\support;

class VoiceMessage extends MediaMessage
{
    /**
     * Messages type.
     *
     * @var string
     */
    protected $type = 'voice';

    /**
     * Properties.
     *
     * @var array
     */
    protected $properties = [
        'media_id',
        'recognition',
    ];
}