<?php

namespace App\Http\Controllers;

use Symfony\Component\Process\Process;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Process\Exception\ProcessFailedException;


use Illuminate\Support\Facades\Log;

use Illuminate\Http\Request;
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
    public function autoUpdate()
    {
        $user = ('ezechieldef');
        $token = ('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');

        $process = new Process([
            'git',
            'pull',
            'https://' . $token . '@github.com/BELVIDAKOUGBLENOU/ProjetDjallonke.git',
        ]);

        $process->setWorkingDirectory(base_path()); // Répertoire racine de votre application Laravel.

        try {
            $process->mustRun();
            // La mise à jour a réussi.
            dump($process->getOutput());
        } catch (ProcessFailedException $exception) {
            // En cas d'échec de la mise à jour.
            dump("Erreur lors de la mise à jour", $exception->getMessage());

        }

        $artisanCommands = [
            'optimize:clear',
            'optimize',
            'config:clear',
            'migrate --force',
        ];
        if (request()->has('artisan')) {
            $artisanCommands[] = request()->input('artisan');
        }


        $artisanResults = [];
        foreach ($artisanCommands as $cmd) {
            Artisan::call($cmd);
            $artisanResults[$cmd] = Artisan::output();
            echo "Command executed: $cmd\n <br>";
            echo "Output: " . $artisanResults[$cmd] . "\n <br><br>";
        }

        return response()->json(['message' => 'Mise à jour réussie']);
    }
}
