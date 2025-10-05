<?php

use Nimp\LinkLoom\implementation\UrlValidator;
use PHPUnit\Framework\TestCase;

class UrlValidatorTest extends TestCase
{
    private UrlValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new UrlValidator();
    }

    /**
     * @dataProvider validUrlsProvider
     */
    public function testValidateReturnsTrueForValidUrls(string $url): void
    {
        $result = $this->validator->validate($url);

        $this->assertTrue($result);
        $this->assertEquals('msg error', $this->validator->getMessageError());
    }

    /**
     * @return array<string, array<string>>
     */
    public static function validUrlsProvider(): array
    {
        return [
            'HTTPS URL' => ['https://example.com'],
            'HTTP URL' => ['http://example.com'],
            'URL with path' => ['https://example.com/path/to/resource'],
            'URL with query parameters' => ['https://example.com?param1=value1&param2=value2'],
            'URL with fragment' => ['https://example.com#section'],
            'URL with port' => ['https://example.com:8080'],
            'URL with authentication' => ['https://user:pass@example.com'],
            'Localhost' => ['http://localhost'],
            'IPv4 address' => ['http://192.168.1.1'],
            'URL with subdomain' => ['https://sub.example.com'],
            'URL with trailing slash' => ['https://example.com/'],
        ];
    }

    /**
     * @dataProvider invalidUrlsProvider
     */
    public function testValidateReturnsFalseForInvalidUrls(string $url, string $expectedMessage): void
    {
        $result = $this->validator->validate($url);

        $this->assertFalse($result);
        $this->assertEquals($expectedMessage, $this->validator->getMessageError());
    }

    /**
     * @return array<string, array<string, string>>
     */
    public static function invalidUrlsProvider(): array
    {
        return [
            'Empty string' => ['', 'invalid url'],
            'String without protocol' => ['example.com', 'invalid url'],
//            'Invalid protocol' => ['ftp://example.com', 'invalid url'],
            'Missing host' => ['https://', 'invalid url'],
            'Just spaces' => ['   ', 'invalid url'],
            'Special characters only' => ['!@#$%^&*()', 'invalid url'],
            'JavaScript protocol' => ['javascript:alert("xss")', 'invalid url'],
            'Data URI' => ['data:text/html,<script>alert("xss")</script>', 'invalid url'],
//            'Missing TLD' => ['http://localhost:3000', 'invalid url'], // localhost is actually valid, but keeping for example
        ];
    }

}