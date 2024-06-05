<?php
namespace Nimp\LinkLoom;

use Nimp\LinkLoom\entities\UrlCodePair;
use Nimp\LinkLoom\exceptions\UrlShortenerException;
use Nimp\LinkLoom\interfaces\IUrlDecode;
use Nimp\LinkLoom\interfaces\IUrlEncode;
use Nimp\LinkLoom\interfaces\RepositoryInterface;
use Nimp\LinkLoom\exceptions\RepositoryDataException;
use Nimp\LinkLoom\interfaces\UrlValidatorInterface;

class UrlShortener implements IUrlDecode, IUrlEncode
{
    protected RepositoryInterface $repository;
    protected UrlValidatorInterface $validator;

    /**
     * @param RepositoryInterface $repository
     * @param UrlValidatorInterface $validator
     */
    public function __construct(RepositoryInterface $repository, UrlValidatorInterface $validator)
    {
        $this->repository = $repository;
        $this->validator = $validator;
    }

    /**
     * @param string $url
     * @return string
     * @throws UrlShortenerException
     */
    public function encode(string $url): string
    {
        if(!$this->validator->validate($url)) {
            throw new UrlShortenerException(
                $this->validator->getMessageError()
            );
        }

        try {
            $code = $this->repository->getCodeByUrl($url);
        }catch (RepositoryDataException) {
            $code = $this->generateCode($url);
            $urlCodePair = new UrlCodePair($url, $code);
            if (!$this->repository->saveUrlEntity($urlCodePair)) {
                throw new UrlShortenerException(
                    'save entity error'
                );
            }
        }

        return $code;
    }

    /**
     * @param string $code
     * @return string
     * @throws UrlShortenerException
     */
    public function decode(string $code): string
    {
        try {
            $code = $this->repository->getUrlByCode($code);
        } catch (RepositoryDataException $e) {
            throw new UrlShortenerException(
                $e->getMessage(),
                $e->getCode(),
                $e->getPrevious()
            );
        }
        return $code;
    }

    /**
     * @param string $url
     * @return string
     */
    protected function generateCode(string $url): string
    {
        $hash = base64_encode($url . time());
        return mb_substr($hash,0,8);
    }

}