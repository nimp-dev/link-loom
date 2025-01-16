<?php
namespace Nimp\LinkLoom;

use Nimp\LinkLoom\entities\UrlCodePair;
use Nimp\LinkLoom\exceptions\UrlShortenerException;
use Nimp\LinkLoom\helpers\LoomLogger;
use Nimp\LinkLoom\helpers\Publisher;
use Nimp\LinkLoom\interfaces\CodeGeneratorInterface;
use Nimp\LinkLoom\interfaces\IUrlDecode;
use Nimp\LinkLoom\interfaces\IUrlEncode;
use Nimp\LinkLoom\interfaces\RepositoryInterface;
use Nimp\LinkLoom\exceptions\RepositoryDataException;
use Nimp\LinkLoom\interfaces\UrlValidatorInterface;

class UrlShortener extends Publisher implements IUrlDecode, IUrlEncode
{
    const ENCODE_START_EVENT = 'encodeStartEvent';
    const ENCODE_SUCCESS_EVENT = 'encodeSuccessEvent';
    const DECODE_START_EVENT = 'decodeStartEvent';
    const DECODE_SUCCESS_EVENT = 'decodeSuccessEvent';
    const VALIDATE_ERROR_EVENT = 'validateError';
    const SAVE_ERROR_EVENT = 'saveError';
    const GET_FROM_STORAGE_ERROR_EVENT = 'getFromStorageError';



    protected RepositoryInterface $repository;
    protected UrlValidatorInterface $validator;
    protected CodeGeneratorInterface $codeGenerator;

    /**
     * @param RepositoryInterface $repository
     * @param UrlValidatorInterface $validator
     */
    public function __construct(
        RepositoryInterface $repository,
        UrlValidatorInterface $validator,
        CodeGeneratorInterface $codeGenerator
    )
    {
        $this->repository = $repository;
        $this->validator = $validator;
        $this->codeGenerator = $codeGenerator;
    }

    /**
     * @param string $url
     * @return string
     * @throws UrlShortenerException
     */
    public function encode(string $url): string
    {
        $this->notify(self::ENCODE_START_EVENT, $url);

        if(!$this->validator->validate($url)) {
            $message = $this->validator->getMessageError();

            $this->notify(self::VALIDATE_ERROR_EVENT, $message);

            throw new UrlShortenerException(
                $message
            );
        }

        try {
            $code = $this->repository->getCodeByUrl($url);
        }catch (RepositoryDataException) {
            $code = $this->codeGenerator->generate($url);
            $urlCodePair = new UrlCodePair($url, $code);
            if (!$this->repository->saveUrlEntity($urlCodePair)) {

                $message = 'save entity error';
                $this->notify(self::SAVE_ERROR_EVENT, $message);

                throw new UrlShortenerException(
                    $message
                );
            }
        }

        $this->notify(self::ENCODE_SUCCESS_EVENT, $code);
        return $code;
    }

    /**
     * @param string $code
     * @return string
     * @throws UrlShortenerException
     */
    public function decode(string $code): string
    {
        $this->notify(self::DECODE_START_EVENT, $code);

        try {
            $code = $this->repository->getUrlByCode($code);
        } catch (RepositoryDataException $e) {

            $this->notify(self::GET_FROM_STORAGE_ERROR_EVENT, $e->getMessage());

            throw new UrlShortenerException(
                $e->getMessage(),
            );
        }

        $this->notify(self::DECODE_SUCCESS_EVENT, $code);
        return $code;
    }

}