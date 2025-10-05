<?php

namespace Nimp\LinkLoom\Tests;

use Nimp\LinkLoom\implementation\RedisRepository;
use Nimp\LinkLoomCore\entities\UrlCodePair;
use Nimp\LinkLoomCore\exceptions\RepositoryDataException;
use PHPUnit\Framework\TestCase;
use Redis;

class RedisRepositoryTest extends TestCase
{
    private Redis $redisMock;
    private RedisRepository $repository;

    protected function setUp(): void
    {
        $this->redisMock = $this->createMock(Redis::class);
        $this->repository = new RedisRepository($this->redisMock, 0, 'test');
    }

    /**
     * @throws RepositoryDataException
     */
    public function testGetUrlByCodeReturnsUrl(): void
    {
        $this->redisMock
            ->method('get')
            ->with('test:code:abc123')
            ->willReturn('https://example.com');

        $url = $this->repository->getUrlByCode('abc123');

        $this->assertEquals('https://example.com', $url);
    }

    public function testGetUrlByCodeThrowsExceptionWhenNotFound(): void
    {
        $this->redisMock
            ->method('get')
            ->with('test:code:unknown')
            ->willReturn(false);

        $this->expectException(RepositoryDataException::class);
        $this->expectExceptionMessage('Unknown code: unknown');

        $this->repository->getUrlByCode('unknown');
    }

    /**
     * @throws RepositoryDataException
     */
    public function testGetCodeByUrlReturnsCode(): void
    {
        $url = 'https://example.com';
        $urlKey = 'test:url:' . sha1($url);

        $this->redisMock
            ->method('get')
            ->with($urlKey)
            ->willReturn('abc123');

        $code = $this->repository->getCodeByUrl($url);

        $this->assertEquals('abc123', $code);
    }

    public function testSaveUrlEntitySavesNewPair(): void
    {
        $pair = new UrlCodePair('https://example.com', 'abc123');
        $codeKey = 'test:code:abc123';
        $urlKey = 'test:url:' . sha1('https://example.com');

        // Мокаем проверку существования
        $this->redisMock
            ->method('exists')
            ->with($codeKey)
            ->willReturn(0);

        // Мокаем multi/exec
        $this->redisMock
            ->method('multi')
            ->willReturn($this->redisMock);

        // Используем callback для проверки вызовов set
        $setCalls = [];
        $this->redisMock
            ->method('set')
            ->willReturnCallback(function($key, $value) use (&$setCalls) {
                $setCalls[] = [$key, $value];
                return true;
            });

        $this->redisMock
            ->method('exec')
            ->willReturn([true, true]);

        $result = $this->repository->saveUrlEntity($pair);

        $this->assertTrue($result);
        $this->assertCount(2, $setCalls);
        $this->assertEquals([$codeKey, 'https://example.com'], $setCalls[0]);
        $this->assertEquals([$urlKey, 'abc123'], $setCalls[1]);
    }

    public function testSaveUrlEntityReturnsFalseForDuplicateCode(): void
    {
        $pair = new UrlCodePair('https://example.com', 'abc123');
        $codeKey = 'test:code:abc123';

        $this->redisMock
            ->method('exists')
            ->with($codeKey)
            ->willReturn(1);

        $result = $this->repository->saveUrlEntity($pair);

        $this->assertFalse($result);
    }

    public function testConstructorSetsPrefixWithoutTrailingColon(): void
    {
        $repository = new RedisRepository($this->redisMock, 0, 'test:');

        // Проверяем что префикс обрезается через вызов метода
        $this->redisMock
            ->method('get')
            ->with('test:code:abc123')
            ->willReturn('https://example.com');

        $url = $repository->getUrlByCode('abc123');

        $this->assertEquals('https://example.com', $url);
    }
}