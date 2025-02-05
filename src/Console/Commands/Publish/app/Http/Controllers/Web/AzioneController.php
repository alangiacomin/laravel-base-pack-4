<?php

namespace App\Http\Controllers\Web;

use AlanGiacomin\LaravelBasePack\Controllers\Controller;
use App\Commands\EseguiAzione;
use Illuminate\Support\Facades\Log;
use Spatie\RouteAttributes\Attributes\Get;
use Spatie\RouteAttributes\Attributes\Prefix;

#[Prefix('azione')]
class AzioneController extends Controller
{
    #[Get('esegui')]
    public function esegui()
    {
        Log::debug('AZIONE CONTROLLER');
        $this->executeCommand(new EseguiAzione());
    }
}
