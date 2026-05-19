<?php

namespace Database\Seeders;

use App\Models\ApiKey;
use Illuminate\Database\Seeder;

class ApiKeySeeder extends Seeder
{
    public function run(): void
    {
        $plainKey = 'mkt_RXhtSymLQm4bXXg8mGtEZ4kywB5qL1pNN17Ep0Pn';

        ApiKey::updateOrCreate(
            ['key_prefix' => substr($plainKey, 0, 12)],
            [
                'name'      => 'Flutter App',
                'key_hash'  => hash('sha256', $plainKey),
                'is_active' => true,
            ]
        );
    }
}
