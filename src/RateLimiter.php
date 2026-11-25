<?php

declare(strict_types=1);

namespace App;

final class RateLimiter
{
    public static function tooMany(string $bucket, int $max, int $windowSeconds, ?string $key = null): bool
    {
        $key ??= Request::ipHash();
        self::cleanup();
        $count = (int) Database::value(
            'SELECT COUNT(*) FROM rate_limits WHERE bucket = ? AND key_hash = ? AND created_at > (NOW() - INTERVAL ? SECOND)',
            [$bucket, $key, $windowSeconds]
        );

        return $count >= $max;
    }

    public static function hit(string $bucket, ?string $key = null): void
    {
        Database::run(
            'INSERT INTO rate_limits (bucket, key_hash, created_at) VALUES (?, ?, NOW())',
            [$bucket, $key ?? Request::ipHash()]
        );
    }

    public static function clear(string $bucket, ?string $key = null): void
    {
        Database::run('DELETE FROM rate_limits WHERE bucket = ? AND key_hash = ?', [$bucket, $key ?? Request::ipHash()]);
    }

    private static function cleanup(): void
    {
        if (random_int(1, 50) === 1) {
            Database::run('DELETE FROM rate_limits WHERE created_at < (NOW() - INTERVAL 24 HOUR)');
        }
    }
}
