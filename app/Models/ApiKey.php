<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ApiKey extends Model
{
    protected $fillable = [
        'name',
        'key_hash',
        'key_prefix',
        'is_active',
        'last_used_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_used_at' => 'datetime',
        ];
    }

    public static function generate(string $name): array
    {
        $plainKey = 'mkt_'.Str::random(40);

        $record = self::create([
            'name' => $name,
            'key_hash' => hash('sha256', $plainKey),
            'key_prefix' => substr($plainKey, 0, 12),
            'is_active' => true,
        ]);

        return [
            'model' => $record,
            'plain_key' => $plainKey,
        ];
    }

    public static function findByPlainKey(string $plainKey): ?self
    {
        $hash = hash('sha256', $plainKey);

        return self::query()
            ->where('key_hash', $hash)
            ->where('is_active', true)
            ->first();
    }

    public function touchLastUsed(): void
    {
        $this->forceFill(['last_used_at' => now()])->save();
    }
}
