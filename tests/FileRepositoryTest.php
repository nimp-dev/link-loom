<?php

namespace Nimp\LinkLoom\Tests;
use Nimp\LinkLoom\implementation\FileRepository;
use Nimp\LinkLoomCore\entities\UrlCodePair;
use Nimp\LinkLoomCore\exceptions\RepositoryDataException;
use PHPUnit\Framework\TestCase;

class FileRepositoryTest extends TestCase
{
    private string $testFile;
    private FileRepository $repository;

    /**
     * @throws RepositoryDataException
     */
    protected function setUp(): void
    {
        $this->testFile = sys_get_temp_dir() . '/test_urls.json';

        // Создаем тестовый файл с данными
        file_put_contents($this->testFile, json_encode([
            'abc123' => 'https://example.com',
            'def456' => 'https://google.com',
        ]));

        $this->repository = new FileRepository($this->testFile, 100);
    }

    protected function tearDown(): void
    {
        // Удаляем тестовый файл после каждого теста
        if (file_exists($this->testFile)) {
            unlink($this->testFile);
        }
    }

    /**
     * @throws RepositoryDataException
     */
    public function testGetUrlByCodeReturnsCorrectUrl(): void
    {
        $url = $this->repository->getUrlByCode('abc123');

        $this->assertEquals('https://example.com', $url);
    }

    public function testGetUrlByCodeThrowsExceptionForUnknownCode(): void
    {
        $this->expectException(RepositoryDataException::class);
        $this->expectExceptionMessage('unknown key');

        $this->repository->getUrlByCode('unknown');
    }

    /**
     * @throws RepositoryDataException
     */
    public function testGetCodeByUrlReturnsCorrectCode(): void
    {
        $code = $this->repository->getCodeByUrl('https://example.com');

        $this->assertEquals('abc123', $code);
    }

    public function testGetCodeByUrlThrowsExceptionForUnknownUrl(): void
    {
        $this->expectException(RepositoryDataException::class);
        $this->expectExceptionMessage('unknown url');

        $this->repository->getCodeByUrl('https://unknown.com');
    }

    public function testSaveUrlEntitySavesNewPair(): void
    {
        $pair = new UrlCodePair('https://github.com', 'ghi789');

        $result = $this->repository->saveUrlEntity($pair);

        $this->assertTrue($result);
        $this->assertEquals('https://github.com', $this->repository->getUrlByCode('ghi789'));
        $this->assertEquals('ghi789', $this->repository->getCodeByUrl('https://github.com'));
    }

    public function testSaveUrlEntityReturnsFalseForDuplicateCode(): void
    {
        $pair = new UrlCodePair('https://new-url.com', 'abc123'); // Существующий код

        $result = $this->repository->saveUrlEntity($pair);

        $this->assertFalse($result);
    }

    public function testConstructorThrowsExceptionForMissingFile(): void
    {
        $this->expectException(RepositoryDataException::class);
        $this->expectExceptionMessage('does not found');

        new FileRepository('/nonexistent/file.json', 100);
    }

    /**
     * @throws RepositoryDataException
     */
    public function testDataIsPersistedOnDestruct(): void
    {
        // Добавляем новую пару
        $pair = new UrlCodePair('https://github.com', 'persist123');
        $this->repository->saveUrlEntity($pair);

        // Уничтожаем репозиторий (вызываем деструктор)
        unset($this->repository);

        // Создаем новый репозиторий и проверяем что данные сохранились
        $newRepository = new FileRepository($this->testFile, 100);
        $url = $newRepository->getUrlByCode('persist123');

        $this->assertEquals('https://github.com', $url);
    }

    /**
     * @throws RepositoryDataException
     */
    public function testCleanupDataRemovesOldEntries(): void
    {
        // Создаем тестовый файл с несколькими записями
        $testFile = sys_get_temp_dir() . '/cleanup_test.json';
        $initialData = [
            'first123' => 'https://first.com',
            'second456' => 'https://second.com',
            'third789' => 'https://third.com',
        ];
        file_put_contents($testFile, json_encode($initialData));

        // Создаем репозиторий с лимитом 2 (меньше чем текущие 3 записи)
        $repository = new FileRepository($testFile, 2);

        // Проверяем, что первая запись удалена (cleanup удаляет первые N записей)
        $this->expectException(RepositoryDataException::class);
        $repository->getUrlByCode('first123'); // Должна быть удалена

        // Убираем за собой
        unset($repository);
        unlink($testFile);
    }

    /**
     * @throws RepositoryDataException
     */
    public function testWorksWithEmptyFile(): void
    {
        // Создаем пустой файл
        $emptyFile = sys_get_temp_dir() . '/empty_test.json';
        file_put_contents($emptyFile, '');

        $repository = new FileRepository($emptyFile, 100);

        // Проверяем что можем сохранять в пустой репозиторий
        $pair = new UrlCodePair('https://test.com', 'test123');
        $result = $repository->saveUrlEntity($pair);

        $this->assertTrue($result);
        $this->assertEquals('https://test.com', $repository->getUrlByCode('test123'));

        // Убираем за собой
        unset($repository);
        unlink($emptyFile);
    }
}