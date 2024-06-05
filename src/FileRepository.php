<?php

namespace Nimp\LinkLoom;

use Nimp\LinkLoom\entities\UrlCodePair;
use Nimp\LinkLoom\exceptions\RepositoryDataException;
use Nimp\LinkLoom\interfaces\RepositoryInterface;

class FileRepository implements RepositoryInterface
{

    protected array $db;
    protected string $file;
    /**
     * @var int Maximum file size in bytes
     */
    protected int $maxItemCount;

    /**
     * @param string $file
     * @param int $maxItemCount Maximum count urls in storage
     * @throws RepositoryDataException
     */
    public function __construct(string $file, int $maxItemCount)
    {
        if (!file_exists($file)) {
            throw new RepositoryDataException('file does not found');
        }
        $this->file = $file;
        $this->maxItemCount = $maxItemCount;

        $this->db = (array)json_decode(
            file_get_contents($this->file),
            true
        );
        $this->checkFileSize();
    }


    /**
     * Write to a file only when the program is running
     */
    public function __destruct()
    {
        $this->checkFileSize();
        file_put_contents(
            $this->file,
            json_encode($this->db,JSON_PRETTY_PRINT)
        );
    }

    /**
     * @inheritdoc
     */
    public function getUrlByCode(string $code): string
    {
        if (!$this->issetCode($code)) {
            throw new RepositoryDataException('unknown key');
        }
        return $this->db[$code];
    }

    /**
     * @inheritdoc
     */
    public function getCodeByUrl(string $url): string
    {
        $code = array_search($url, $this->db);
        if ($code == false) {
            throw new RepositoryDataException('unknown url');
        }
        return $code;
    }

    /**
     * @inheritdoc
     */
    public function saveUrlEntity(UrlCodePair $urlCodePair): bool
    {
        /** TODO добавить в entity дату expiredTime */
        /** TODO entity тогда можно просто назвать UrlEntity */
        if ($this->issetCode($urlCodePair->getCode())) {
            return false;
        }

        $this->db[$urlCodePair->getCode()] = $urlCodePair->getUrl();
        return true;
    }

    /**
     * @param string $code
     * @return bool
     */
    public function issetCode(string $code): bool
    {
        return array_key_exists($code, $this->db);
    }

    protected function checkFileSize(): void
    {
        $currentSize = count($this->db);

        $itemsToRemove = $currentSize - $this->maxItemCount;

        if ($itemsToRemove > 0) {
            $this->cleanupData($itemsToRemove);
        }
    }

    protected function cleanupData($count): void
    {
        $this->db = array_slice($this->db, $count, null, true);
    }

}