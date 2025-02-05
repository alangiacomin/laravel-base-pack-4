<?php

namespace App\Events;

use AlanGiacomin\LaravelBasePack\Events\Event;

class AzioneFatta extends Event //  implements ShouldBroadcast
{
    public string $laMiaVariabile = 'test';
}
