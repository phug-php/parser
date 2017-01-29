<?php

namespace Phug\Test;

use Phug\Parser;

abstract class AbstractParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Parser
     */
    protected $parser;

    public function setUp()
    {
        $this->parser = new Parser();
    }

    public function assertNodes($expression, $expected)
    {
        if (is_array($expected)) {
            $expected = implode("\n", $expected);
        }

        $dump = str_replace('Phug\\Parser\\Node\\', '', $this->parser->dump($expression));

        self::assertSame($expected, $dump);
    }
}
