<?php

namespace App\Console\Commands;

use App\Models\ApiKey;
use Illuminate\Console\Command;

class ListApiKeysCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api-key:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'List all API keys';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $keys = ApiKey::orderBy('name')->get();

        if ($keys->isEmpty()) {
            $this->info('No API keys found.');

            return self::SUCCESS;
        }

        $rows = $keys->map(fn (ApiKey $key) => [
            $key->id,
            $key->name,
            $key->is_active ? 'active' : 'revoked',
            implode(',', (array) ($key->scopes ?? ['*'])),
            $key->last_used_at?->toDateTimeString() ?? 'never',
            $key->created_at->toDateTimeString(),
        ])->all();

        $this->table(['ID', 'Name', 'Status', 'Scopes', 'Last Used', 'Created'], $rows);

        return self::SUCCESS;
    }
}
