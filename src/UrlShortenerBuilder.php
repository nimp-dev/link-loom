<?php

namespace Nimp\LinkLoom;

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Nimp\LinkLoom\exceptions\ShorterBuildException;
use Nimp\LinkLoom\implementation\{BaseCodeGenerator, FileRepository, RedisRepository, UrlValidator as BaseValidator};
use Nimp\LinkLoom\interfaces\ShortenerBuilderInterface;
use Nimp\LinkLoom\observer\listeners\LoggerListener;
use Nimp\LinkLoomCore\exceptions\RepositoryDataException;
use Nimp\LinkLoomCore\interfaces\{CodeGeneratorInterface, RepositoryInterface, UrlValidatorInterface};
use Nimp\LinkLoomCore\UrlShortener;
use Nimp\Observer\EventDispatcher;
use Nimp\Observer\EventListenerInterface;
use Nimp\Observer\ListenerProvider;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Redis;

class UrlShortenerBuilder implements ShortenerBuilderInterface
{
    private ?RepositoryInterface $repository = null;
    private ?UrlValidatorInterface $validator = null;
    private ?CodeGeneratorInterface $codeGenerator = null;
    private ?EventDispatcherInterface $eventDispatcher = null;
    /**
     * @var array<EventListenerInterface>
     */
    private array $listeners = [];

    //=========== Event Dispatcher ===========
    /**
     * @param EventDispatcherInterface $dispatcher
     * @return $this
     */
    public function withEventDispatcher(EventDispatcherInterface $dispatcher): self
    {
        $this->eventDispatcher = $dispatcher;
        return $this;
    }

    /**
     * @return EventDispatcherInterface
     */
    private function createEventDispatcher(): EventDispatcherInterface
    {
        if (empty($this->listeners)) {
            return new EventDispatcher(new ListenerProvider());
        }

        $listenerProvider = new ListenerProvider();
        foreach ($this->listeners as $listener) {
            $listenerProvider->addListeners($listener);
        }

        return new EventDispatcher($listenerProvider);
    }

    /**
     * @param EventListenerInterface $listener
     * @return $this
     */
    public function addListener(EventListenerInterface $listener): self
    {
        if (in_array($listener, $this->listeners, true)) {
            return $this;
        }
        $this->listeners[] = $listener;
        return $this;
    }


    //=========== Repositories  ===========
    /**
     * @param RepositoryInterface $repository
     * @return $this
     */
    public function withCustomRepository(RepositoryInterface $repository): self
    {
        $this->repository = $repository;
        return $this;
    }

    /**
     * @param Redis $redis
     * @param int $ttl
     * @param string $prefix
     * @return $this
     */
    public function withRedisRepository(Redis $redis, int $ttl = 0, string $prefix = 'linkloom'): self
    {
        $this->repository = new RedisRepository($redis, $ttl, $prefix);
        return $this;
    }

    /**
     * @throws RepositoryDataException
     */
    public function withFileRepository(string $file, int $maxItems = 1000): self
    {
        $this->repository = new FileRepository($file, $maxItems);
        return $this;
    }

    //=========== Validators  ===========
    /**
     * @param UrlValidatorInterface $validator
     * @return $this
     */
    public function withCustomValidator(UrlValidatorInterface $validator): self
    {
        $this->validator = $validator;
        return $this;
    }

    /**
     * @return $this
     */
    public function withBaseValidator(): self
    {
        $this->validator = new BaseValidator();
        return $this;
    }

    //=========== Code Generators  ===========
    /**
     * @param CodeGeneratorInterface $codeGenerator
     * @return $this
     */
    public function withCustomCodeGenerator(CodeGeneratorInterface $codeGenerator): self
    {
        $this->codeGenerator = $codeGenerator;
        return $this;
    }

    public function withBaseCodeGenerator(int $length = 8): self
    {
        $this->codeGenerator = new BaseCodeGenerator($length);
        return $this;
    }

    //=========== Logger  ===========
    public function withLogger(string $path, Level $level = Level::Debug): self
    {
        $dir = \dirname($path);
        if (!is_dir($dir)) {
            if (!@mkdir($dir, 0777, true) && !is_dir($dir)) {
                throw new \RuntimeException("Cannot create log directory: {$dir}");
            }
        }

        $logger = new Logger('general', [new StreamHandler($path, $level)]);

        return $this->withLoggerInstance($logger);
    }

    public function withLoggerInstance(LoggerInterface $logger): self
    {
        $this->removeLoggerListener();
        $this->listeners[] = new LoggerListener($logger);

        return $this;
    }

    private function removeLoggerListener(): void
    {
        $this->listeners = array_filter($this->listeners, function($listener) {
            return !($listener instanceof LoggerListener);
        });
    }

    //=========== Build  ===========
    /**
     * @throws ShorterBuildException
     */
    public function build(): UrlShortener
    {
        // Установка значений по умолчанию
        $repository = $this->repository ?? $this->createDefaultRepository();
        $validator = $this->validator ?? new BaseValidator();
        $codeGenerator = $this->codeGenerator ?? new BaseCodeGenerator(8);
        $eventDispatcher = $this->eventDispatcher ?? $this->createEventDispatcher();

        return new UrlShortener(
            $repository,
            $validator,
            $codeGenerator,
            $eventDispatcher
        );
    }

    /**
     * @throws ShorterBuildException
     */
    private function createDefaultRepository(): RepositoryInterface
    {
        throw new ShorterBuildException(
            'Repository must be configured. Use withRedisRepository(), withFileRepository() or withCustomRepository()'
        );
    }

}