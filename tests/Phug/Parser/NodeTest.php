<?php

namespace Phug\Test\Parser;

use Phug\Parser\Node\ElementNode;
use Phug\Test\AbstractParserTest;

/**
 * @coversDefaultClass Phug\Parser\Node
 */
class NodeTest extends AbstractParserTest
{
    /**
     * @covers ::<public>
     */
    public function testGettersAndSetters()
    {
        $document = $this->parser->parse("div\n  p Hello");

        $children = $document->getChildren();

        self::assertSame(1, count($children));

        $div = $children[0];

        self::assertInstanceOf(ElementNode::class, $div);
        self::assertSame(1, $div->getLine());
        self::assertSame(1, $div->getOffset());
        self::assertSame(0, $div->getLevel());
        self::assertSame(null, $div->getOuterNode());

        $p = $div->getChildren()[0];

        self::assertInstanceOf(ElementNode::class, $p);
        self::assertSame(2, $p->getLine());
        self::assertSame(3, $p->getOffset());
        self::assertSame(2, $p->getLevel());
        self::assertSame(null, $p->getOuterNode());

        $p->setOuterNode($div);

        self::assertSame($div, $p->getOuterNode());
    }
}
