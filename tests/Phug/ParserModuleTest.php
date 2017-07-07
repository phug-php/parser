<?php

namespace Phug\Test;

use Phug\AbstractParserModule;
use Phug\Parser;
use Phug\Parser\Event\NodeEvent;
use Phug\Parser\Node\TextNode;
use Phug\ParserEvent;

//@codingStandardsIgnoreStart
class ParserTestModule extends AbstractParserModule
{
    public function getEventListeners()
    {
        return [
            ParserEvent::DOCUMENT => function (NodeEvent $e) {

                $node = new TextNode();
                $node->setValue('Listener was here!');
                $e->getNode()->prependChild($node);
            },
        ];
    }
}


/**
 * @coversDefaultClass Phug\AbstractParserModule
 */
class ParserModuleTest extends AbstractParserTest
{
    /**
     * @covers ::<public>
     */
    public function testTokenEvent()
    {
        self::assertNodes('p Test', [
            '[DocumentNode]',
            '  [ElementNode]',
            '    [TextNode]',
        ]);

        $parser = new Parser(['modules' => [ParserTestModule::class]]);

        self::assertNodes('p Test', [
            '[DocumentNode]',
            '  [TextNode]',
            '  [ElementNode]',
            '    [TextNode]',
        ], $parser);
    }
}
//@codingStandardsIgnoreEnd
