<?php

namespace Phug\Test;

use Phug\Parser;
use Phug\ParserModule;

/**
 * @coversDefaultClass Phug\ParserModule
 */
class ParserModuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::<public>
     */
    public function testModule()
    {
        $copy = null;
        $module = new ParserModule();
        $module->onPlug(function ($_parser) use (&$copy) {
            $copy = $_parser;
        });
        $parser = new Parser([
            'modules' => [$module],
        ]);
        self::assertSame($parser, $copy);
    }
}
