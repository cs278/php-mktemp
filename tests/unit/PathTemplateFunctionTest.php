<?php

namespace Cs278\Mktemp\Tests;

use PHPUnit\Framework\TestCase;

use function Cs278\Mktemp\pathTemplate;

final class PathTemplateFunctionTest extends TestCase
{
    /** @dataProvider dataValid */
    public function testValid($expectedPrefix, $expectedPlaceholderLength, $expectedSuffix, $input)
    {
        $output = pathTemplate($input);

        $regex = sprintf(
            '{^%s[0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ]{%u}%s$}',
            preg_quote($expectedPrefix),
            $expectedPlaceholderLength,
            preg_quote($expectedSuffix)
        );

        $this->assertRegExp($regex, $output);
    }

    public static function dataValid()
    {
        return [
            ['', 3, '', 'XXX'],
            ['', 31, '', 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX'],
            ['', 3, '.tmp', 'XXX.tmp'],
            ['', 31, '.tmp', 'XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX.tmp'],
            ['foo_', 3, '.tmp', 'foo_XXX.tmp'],
            ['foo_', 31, '.tmp', 'foo_XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX.tmp'],
            ['foo_', 3, '', 'foo_XXX'],
            ['foo_', 31, '', 'foo_XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX'],
            ['XXX_', 3, '', 'XXX_XXX'],
        ];
    }

    /** @dataProvider dataInvalid */
    public function testInvalid($input)
    {
        $this->expectException(\InvalidArgumentException::class);

        pathTemplate($input);
    }

    public static function dataInvalid()
    {
        return [
            [''],
            ['xxx'],
            ['X.X.X'],
            ['XX.XX'],
            ['XXxXX'],
        ];
    }
}
