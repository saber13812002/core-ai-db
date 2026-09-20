<?php

namespace App\Console\Commands;

use App\Models\ApiKey;
use Illuminate\Console\Command;

class RevokeApiKeyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api-key:revoke {key : The key ID or name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Revoke an API key (deactivate it)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $key = ApiKey::where('id', $this->argument('key'))
            ->orWhere('name', $this->argument('key'))
            ->first();

        if ($key === null) {
            $this->error('API key not found.');

            return self::FAILURE;
        }

        if (! $key->is_active) {
            $this->warn("Key [{$key->name}] is already revoked.");

            return self::SUCCESS;
        }

        $key->update(['is_active' => false]);

        $this->info("API key [{$key->name}] revoked.");

        return self::SUCCESS;
    }
}
