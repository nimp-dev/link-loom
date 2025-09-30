<?php

namespace Nimp\LinkLoom;

use Nimp\LinkLoom\entities\UrlCodePair;
use Nimp\LinkLoom\exceptions\UrlShortenerException;
use Nimp\LinkLoom\interfaces\CodeGeneratorInterface;
use Nimp\LinkLoom\interfaces\UrlDecodeInterface;
use Nimp\LinkLoom\interfaces\UrlEncodeInterface;
use Nimp\LinkLoom\interfaces\RepositoryInterface;
use Nimp\LinkLoom\exceptions\RepositoryDataException;
use Nimp\LinkLoom\interfaces\UrlValidatorInterface;
use Nimp\LinkLoom\observer\events\DecodeStartEvent;
use Nimp\LinkLoom\observer\events\DecodeSuccessEvent;
use Nimp\LinkLoom\observer\events\EncodeStartEvent;
use Nimp\LinkLoom\observer\events\EncodeSuccessEvent;
use Nimp\LinkLoom\observer\events\GetFromStorageErrorEvent;
use Nimp\LinkLoom\observer\events\SaveErrorEvent;
use Nimp\LinkLoom\observer\events\ValidateErrorEvent;
use Psr\EventDispatcher\EventDispatcherInterface;

class UrlShortener implements UrlDecodeInterface, UrlEncodeInterface
{
    protected RepositoryInterface $repository;
    protected UrlValidatorInterface $validator;
    protected CodeGeneratorInterface $codeGenerator;
    protected EventDispatcherInterface $eventDispatcher;

    /**
     * @param RepositoryInterface $repository
     * @param UrlValidatorInterface $validator
     * @param CodeGeneratorInterface $codeGenerator
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        RepositoryInterface      $repository,
        UrlValidatorInterface    $validator,
        CodeGeneratorInterface   $codeGenerator,
        EventDispatcherInterface $eventDispatcher,
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
        $this->eventDispatcher->dispatch(new EncodeStartEvent($this, $url));

        if (!$this->validator->validate($url)) {
            $message = $this->validator->getMessageError();

            $this->eventDispatcher->dispatch(new ValidateErrorEvent($this, $url, $message));

            throw new UrlShortenerException(
                $message
            );
        }

        try {
            $code = $this->repository->getCodeByUrl($url);
        } catch (RepositoryDataException) {
            $code = $this->codeGenerator->generate($url);
            $urlCodePair = new UrlCodePair($url, $code);
            if (!$this->repository->saveUrlEntity($urlCodePair)) {

                $message = 'save entity error';
                $this->eventDispatcher->dispatch(new SaveErrorEvent($this, $message));

                throw new UrlShortenerException(
                    $message
                );
            }
        }

        $this->eventDispatcher->dispatch(new EncodeSuccessEvent($this, $code));
        return $code;
    }

    /**
     * @param string $code
     * @return string
     * @throws UrlShortenerException
     */
    public function decode(string $code): string
    {
        $this->eventDispatcher->dispatch(new DecodeStartEvent($this, $code));

        try {
            $url = $this->repository->getUrlByCode($code);
        } catch (RepositoryDataException $e) {

            $this->eventDispatcher->dispatch(new GetFromStorageErrorEvent($this, $code, $e->getMessage()));

            throw new UrlShortenerException(
                $e->getMessage(),
            );
        }

        $this->eventDispatcher->dispatch(new DecodeSuccessEvent($this, $url));
        return $url;
    }

}