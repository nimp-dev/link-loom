<?php

namespace Nimp\LinkLoom\entities;

/** todo add data property */
class UrlCodePair
{
    protected string $url;
    protected string $code;

    /**
     * @param string $url
     * @param string $code
     */
    public function __construct(string $url, string $code)
    {
        $this->url = $url;
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

}