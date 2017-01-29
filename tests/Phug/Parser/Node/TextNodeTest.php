<?php

namespace Phug\Test\Parser\Node;

use Phug\Parser\Node\TextNode;
use Phug\Test\AbstractParserTest;

/**
 * @coversDefaultClass Phug\Parser\Node\TextNode
 */
class TextNodeTest extends AbstractParserTest
{
    /**
     * @covers ::setLevel
     * @covers ::getLevel
     */
    public function testLevel()
    {
        $text = new TextNode();

        self::assertSame(null, $text->getLevel());

        $text->setLevel(2);

        self::assertSame(2, $text->getLevel());
    }
}
