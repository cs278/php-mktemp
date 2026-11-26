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
            ['foo-', 3, '-XX_XX_X_XX_X_XX_XX_X_X_X.tmp', 'foo-XXX-XX_XX_X_XX_X_XX_XX_X_X_X.tmp'],
            ['foo-', 3, '-XX_XX_X_XX_X_XX_XX_X_X_X', 'foo-XXX-XX_XX_X_XX_X_XX_XX_X_X_X'],
            'internal space' => ['my temp_', 6, '', 'my temp_XXXXXX'],
            'valid UTF-8 multibyte' => ['tëmp_日本語_', 6, '', 'tëmp_日本語_XXXXXX'],
            'dots and dashes' => ['app-cache.v1_', 6, '', 'app-cache.v1_XXXXXX']
        ];
    }

    /**
     * @medium
     */
    public function testSlow()
    {
        $result = pathTemplate('XXX'.\str_repeat('aXX', 1024 * 1024 * 100));

        $this->assertEquals(1024 * 1024 * 100 * 3 + 3, strlen($result));
        $this->assertRegExp('{^[0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ]{3}a}', $result);
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
            ['.'],
            ['..'],
            ['xxx'],
            ['X.X.X'],
            ['XX.XX'],
            ['XXxXX'],
            [' XXXXXXX '],
            [' XXXXXXX'],
            ['XXXXXXX '],
            ['../XXXXXX'],
            ['XXXXXX/..'],
            ['XXXXXX/../XXXXXX'],
            ["\0XXXXXX"],
            ["tmp\0.XXXXXX"],
            ["tmp\n.XXXXXX"],

            // Leading & trailing whitespace
            'leading space' => [' tmp_XXXXXX'],
            'trailing space' => ['tmp_XXXXXX '],
            'leading and trailing spaces' => [' tmp_XXXXXX '],
            'leading tab' => ["\ttmp_XXXXXX"],
            'trailing tab' => ["tmp_XXXXXX\t"],
            'leading newline' => ["\ntmp_XXXXXX"],
            'trailing newline' => ["tmp_XXXXXX\n"],
            'trailing CRLF' => ["tmp_XXXXXX\r\n"],

            // Embedded ASCII control characters (C0: 0x00–0x1F and DEL: 0x7F)
            'null byte' => ["tmp\0_XXXXXX"],
            'embedded LF' => ["tmp\n.XXXXXX"],
            'embedded CR' => ["tmp\r.XXXXXX"],
            'embedded tab' => ["tmp\t_XXXXXX"],
            'vertical tab' => ["tmp\v_XXXXXX"],
            'form feed' => ["tmp\f_XXXXXX"],
            'bell character' => ["tmp\x07_XXXXXX"],
            'backspace character' => ["tmp\x08_XXXXXX"],
            'ANSI escape sequence' => ["tmp_\x1B[31mred\x1B[0m_XXXXXX"],
            'DEL character' => ["tmp\x7F_XXXXXX"],
        ];
    }
}
