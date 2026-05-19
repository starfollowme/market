<?php

namespace App\Console\Commands;

use App\Models\ApiKey;
use Illuminate\Console\Command;

class GenerateMarketApiKey extends Command
{
    protected $signature = 'market:api-key {name=Flutter App}';

    protected $description = 'Generate API key for Flutter / external clients';

    public function handle(): int
    {
        $result = ApiKey::generate($this->argument('name'));

        $this->newLine();
        $this->info('API key created successfully.');
        $this->line('Name: '.$result['model']->name);
        $this->warn('Copy this key now (shown once):');
        $this->line($result['plain_key']);
        $this->newLine();
        $this->comment('Flutter: set in market/lib/config/api_config.dart or --dart-define=MARKET_API_KEY=...');
        $this->comment('Header: X-API-Key: '.$result['plain_key']);

        return self::SUCCESS;
    }
}
