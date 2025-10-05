<?php

namespace Nimp\LinkLoom\Tests;

use Nimp\LinkLoom\implementation\BaseCodeGenerator;
use PHPUnit\Framework\TestCase;

class BaseCodeGeneratorTest extends TestCase
{
    /**
     * Checking the length of the generated string
     * @dataProvider lengthProvider
     */
    public function testGenerateReturnsStringOfCorrectLength(int $length): void
    {
        $generator = new BaseCodeGenerator($length);
        $url = 'https://example.com';

        $result = $generator->generate($url);

        $this->assertIsString($result);
        $this->assertEquals($length, strlen($result));
    }

    /**
     * @return int[][]
     */
    public static function lengthProvider(): array
    {
        return [
            [4],
            [8],
            [12],
            [16],
            [32],
        ];
    }

    /**
     * Checking the format of the generated code
     * @dataProvider getUrls
     */
    public function testGeneratedCodesAreConsistentInFormat(string $url): void
    {
        $generator = new BaseCodeGenerator(8);

        $code = $generator->generate($url);
        $this->assertMatchesRegularExpression('/^[a-f0-9]{8}$/', $code);
    }

    /**
     * @return string[][]
     */
    public static function getUrls(): array
    {
        return [
            ['https://example.com'],
            ['http://google.com'],
            ['https://github.com'],
            ['https://stackoverflow.com'],
        ];
    }

    /**
     * Checking the difference in generated code after a time interval
     * @return void
     */
    public function testGenerateReturnsDifferentCodesForSameUrl(): void
    {
        $generator = new BaseCodeGenerator(8);
        $url = 'https://example.com';

        $code1 = $generator->generate($url);
        sleep(1); // Небольшая задержка чтобы time() изменился
        $code2 = $generator->generate($url);

        $this->assertNotEquals($code1, $code2);
    }

    /**
     * Checking the difference in generated code for different urls
     * @return void
     */
    public function testGenerateReturnsDifferentCodesForDifferentUrls(): void
    {
        $generator = new BaseCodeGenerator(8);

        $code1 = $generator->generate('https://example.com');
        $code2 = $generator->generate('https://google.com');

        $this->assertNotEquals($code1, $code2);
    }

    /**
     * Checking the format of the generated code
     * @return void
     */
    public function testGenerateReturnsHexCharacters(): void
    {
        $generator = new BaseCodeGenerator(8);
        $url = 'https://example.com';

        $code = $generator->generate($url);

        $this->assertMatchesRegularExpression('/^[a-f0-9]+$/', $code);
    }

    /**
     * Checking the format of the generated code
     * @return void
     */
    public function testGenerateWithEmptyUrl(): void
    {
        $generator = new BaseCodeGenerator(8);

        $code = $generator->generate('');

        $this->assertIsString($code);
        $this->assertEquals(8, strlen($code));
        $this->assertMatchesRegularExpression('/^[a-f0-9]+$/', $code);
    }

}