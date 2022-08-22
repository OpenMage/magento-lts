<?php

namespace OpenMage\Scripts\Composer;

use Composer\Script\Event;

interface ScriptInterface
{
    public static function run(Event $event);
}
