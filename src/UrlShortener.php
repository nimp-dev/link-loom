<?php
namespace Nimp\LinkLoom;

use Nimp\LinkLoom\entities\UrlCodePair;
use Nimp\LinkLoom\exceptions\UrlShortenerException;
use Nimp\LinkLoom\interfaces\CodeGeneratorInterface;
use Nimp\LinkLoom\interfaces\IUrlDecode;
use Nimp\LinkLoom\interfaces\IUrlEncode;
use Nimp\LinkLoom\interfaces\RepositoryInterface;
use Nimp\LinkLoom\exceptions\RepositoryDataException;
use Nimp\LinkLoom\interfaces\UrlValidatorInterface;
use Nimp\LinkLoom\observer\dispatcher\EventDispatcher;
use Nimp\LinkLoom\observer\events\BaseShortenerEvent;

class UrlShortener implements IUrlDecode, IUrlEncode
{
    const string ENCODE_START_EVENT = 'encodeStartEvent';
    const string ENCODE_SUCCESS_EVENT = 'encodeSuccessEvent';
    const string DECODE_START_EVENT = 'decodeStartEvent';
    const string DECODE_SUCCESS_EVENT = 'decodeSuccessEvent';
    const string VALIDATE_ERROR_EVENT = 'validateError';
    const string SAVE_ERROR_EVENT = 'saveError';
    const string GET_FROM_STORAGE_ERROR_EVENT = 'getFromStorageError';



    protected RepositoryInterface $repository;
    protected UrlValidatorInterface $validator;
    protected CodeGeneratorInterface $codeGenerator;
    protected EventDispatcher  $eventDispatcher;

    /**
     * @param RepositoryInterface $repository
     * @param UrlValidatorInterface $validator
     * @param CodeGeneratorInterface $codeGenerator
     * @param EventDispatcher $eventDispatcher
     */
    public function __construct(
        RepositoryInterface $repository,
        UrlValidatorInterface $validator,
        CodeGeneratorInterface $codeGenerator,
        EventDispatcher $eventDispatcher,
    )
    {
        $this->repository = $repository;
        $this->validator = $validator;
        $this->codeGenerator = $codeGenerator;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @param string $url
     * @return string
     * @throws UrlShortenerException
     */
    public function encode(string $url): string
    {
        $this->eventDispatcher->dispatch(new BaseShortenerEvent(self::ENCODE_START_EVENT, $this));

        if(!$this->validator->validate($url)) {
            $message = $this->validator->getMessageError();

            $this->eventDispatcher->dispatch(new BaseShortenerEvent(self::VALIDATE_ERROR_EVENT, $this));

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
                $this->eventDispatcher->dispatch(new BaseShortenerEvent(self::SAVE_ERROR_EVENT, $this));

                throw new UrlShortenerException(
                    $message
                );
            }
        }

        $this->eventDispatcher->dispatch(new BaseShortenerEvent(self::ENCODE_SUCCESS_EVENT, $this));
        return $code;
    }

    /**
     * @param string $code
     * @return string
     * @throws UrlShortenerException
     */
    public function decode(string $code): string
    {
        $this->eventDispatcher->dispatch(new BaseShortenerEvent(self::DECODE_START_EVENT, $this));

        try {
            $code = $this->repository->getUrlByCode($code);
        } catch (RepositoryDataException $e) {

            $this->eventDispatcher->dispatch(new BaseShortenerEvent(self::VALIDATE_ERROR_EVENT, $this));

            throw new UrlShortenerException(
                $e->getMessage(),
            );
        }

        $this->eventDispatcher->dispatch(new BaseShortenerEvent(self::DECODE_SUCCESS_EVENT, $this));
        return $code;
    }

}