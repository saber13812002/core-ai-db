<?php

namespace App\Console\Commands;

use App\Models\ApiKey;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class CreateApiKeyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api-key:create {name : The human-readable key name}
                            {--scopes= : Comma-separated route scopes (default: all)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new API key. The plaintext key is printed once.';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = $this->argument('name');

        if (ApiKey::where('name', $name)->exists()) {
            $this->error("An API key named [{$name}] already exists.");

            return self::FAILURE;
        }

        $scopes = $this->option('scopes');
        $scopes = $scopes === null || $scopes === '' ? null : array_map('trim', explode(',', $scopes));

        $plain = 'sk_live_'.Str::lower(Str::random(32));

        $key = ApiKey::create([
            'name' => $name,
            'key_hash' => ApiKey::hashKey($plain),
            'scopes' => $scopes,
        ]);

        $this->info('API key created.');
        $this->line('ID:   '.$key->id);
        $this->line('Name: '.$key->name);
        $this->newLine();
        $this->warn('Plaintext key (shown once, not stored again):');
        $this->info($plain);

        return self::SUCCESS;
    }
}
