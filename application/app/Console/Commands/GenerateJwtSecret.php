<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateJwtSecret extends Command
{
    protected $signature = 'jwt:secret {--show : Display the key instead of modifying files}';

    protected $description = 'Generate a JWT secret key';

    public function handle()
    {
        $key = base64_encode(Str::random(32));

        if ($this->option('show')) {
            $this->line('<comment>'.$key.'</comment>');

            return;
        }

        $this->setKeyInEnvironmentFile($key);

        $this->info('JWT secret key set successfully.');
    }

    protected function setKeyInEnvironmentFile($key)
    {
        $currentKey = config('jwt.secret') ?: env('JWT_SECRET');

        if (strlen($currentKey) !== 0 && (! $this->confirmToProceed())) {
            return;
        }

        $this->writeNewEnvironmentFileWith($key);
    }

    protected function writeNewEnvironmentFileWith($key)
    {
        $replaced = preg_replace(
            $this->keyReplacementPattern(),
            'JWT_SECRET='.$key,
            $input = file_get_contents($this->laravel->environmentFilePath())
        );

        if ($replaced === $input || $replaced === null) {
            $this->error('Unable to set JWT key. No JWT_SECRET variable was found in the .env file.');
        } else {
            file_put_contents($this->laravel->environmentFilePath(), $replaced);
        }
    }

    protected function keyReplacementPattern()
    {
        $escaped = preg_quote('='.config('jwt.secret') ?: env('JWT_SECRET'), '/');

        return "/^JWT_SECRET{$escaped}/m";
    }
}
