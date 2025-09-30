<?php

namespace Nimp\LinkLoom\implementation;

use Nimp\LinkLoomCore\entities\UrlCodePair;
use Nimp\LinkLoomCore\exceptions\RepositoryDataException;
use Nimp\LinkLoomCore\interfaces\RepositoryInterface;
use Redis;

/**
 * Redis-backed repository implementation.
 *
 * Stores bidirectional mapping:
 *  - {prefix}:code:{code} => url
 *  - {prefix}:url:{sha1(url)} => code
 *
 * TTL can be configured; both keys get the same expiration.
 */
final class RedisRepository implements RepositoryInterface
{
    private Redis $redis;
    private string $prefix;
    private int $ttl; // seconds; 0 means no expiration

    public function __construct(Redis $redis, int $ttl = 0, string $prefix = 'linkloom')
    {
        $this->redis  = $redis;
        $this->prefix = rtrim($prefix, ':');
        $this->ttl    = max(0, $ttl);
    }

    /**
     * @inheritdoc
     */
    public function getUrlByCode(string $code): string
    {
        $url = $this->redis->get($this->codeKey($code));

        if ($url === false || $url === null) {
            throw new RepositoryDataException("Unknown code: {$code}");
        }

        return $url;
    }

    /**
     * @inheritdoc
     */
    public function getCodeByUrl(string $url): string
    {
        $code = $this->redis->get($this->urlKey($url));

        if ($code === false || $code === null) {
            throw new RepositoryDataException("Unknown url: {$url}");
        }

        return $code;
    }

    /**
     * @inheritdoc
     */
    public function saveUrlEntity(UrlCodePair $urlCodePair): bool
    {
        $codeKey = $this->codeKey($urlCodePair->getCode());
        $urlKey  = $this->urlKey($urlCodePair->getUrl());

        // if code already exists -> fail (same as FileRepository)
        if ($this->redis->exists($codeKey) > 0) {
            return false;
        }

        $this->redis->multi();

        $this->setKey($codeKey, $urlCodePair->getUrl());
        $this->setKey($urlKey, $urlCodePair->getCode());

        $exec = $this->redis->exec();

        if (!is_array($exec) || count($exec) < 2) {
            return false;
        }

        return $this->isSetOk($exec[0]) && $this->isSetOk($exec[1]);
    }

    private function setKey(string $key, string $value): void
    {
        if ($this->ttl > 0) {
            $this->redis->set($key, $value, ['EX' => $this->ttl]);
        } else {
            $this->redis->set($key, $value);
        }
    }

    private function isSetOk(mixed $result): bool
    {
        return $result === true || $result === 'OK';
    }

    private function codeKey(string $code): string
    {
        return "{$this->prefix}:code:{$code}";
    }

    private function urlKey(string $url): string
    {
        return "{$this->prefix}:url:" . sha1($url);
    }
}
