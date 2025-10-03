<?php

namespace Nimp\LinkLoom\interfaces;

use Nimp\LinkLoom\exceptions\ShorterBuildException;
use Nimp\LinkLoomCore\interfaces\CodeGeneratorInterface;
use Nimp\LinkLoomCore\interfaces\RepositoryInterface;
use Nimp\LinkLoomCore\interfaces\UrlValidatorInterface;
use Nimp\LinkLoomCore\UrlShortener;
use Psr\EventDispatcher\EventDispatcherInterface;

interface ShortenerBuilderInterface
{
    /**
     * @throws ShorterBuildException
     */
    public function build(): UrlShortener;

    /**
     * @param EventDispatcherInterface $dispatcher
     * @return self
     */
    public function withEventDispatcher(EventDispatcherInterface $dispatcher): self;

    /**
     * @param CodeGeneratorInterface $codeGenerator
     * @return self
     */
    public function withCustomCodeGenerator(CodeGeneratorInterface $codeGenerator): self;

    /**
     * @param RepositoryInterface $repository
     * @return self
     */
    public function withCustomRepository(RepositoryInterface $repository): self;

    /**
     * @param UrlValidatorInterface $validator
     * @return self
     */
    public function withCustomValidator(UrlValidatorInterface $validator): self;

}