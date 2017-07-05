<?php

namespace Phug\Test\Parser;

use Phug\Lexer\Token\TagToken;
use Phug\Lexer\Token\TextToken;
use Phug\Parser\Node\ElementNode;
use Phug\Parser\Node\TextNode;
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
        $document = $this->parser->parse("div\n  p Hello", 'source.pug');

        $children = $document->getChildren();

        self::assertSame(1, count($children));

        /** @var ElementNode $div */
        $div = $children[0];

        self::assertInstanceOf(ElementNode::class, $div);
        self::assertSame(1, $div->getLine());
        self::assertSame(1, $div->getOffset());
        self::assertSame(0, $div->getLevel());
        self::assertSame(null, $div->getOuterNode());
        self::assertInstanceOf(TagToken::class, $div->getToken());
        self::assertSame('source.pug', $div->getFile());

        /** @var ElementNode $p */
        $p = $div->getChildren()[0];

        self::assertInstanceOf(ElementNode::class, $p);
        self::assertSame(2, $p->getLine());
        self::assertSame(3, $p->getOffset());
        self::assertSame(2, $p->getLevel());
        self::assertSame(null, $p->getOuterNode());
        self::assertInstanceOf(TagToken::class, $p->getToken());
        self::assertSame('source.pug', $p->getFile());

        /** @var TextNode $text */
        $text = $div->getChildren()[0]->getChildren()[0];

        self::assertInstanceOf(TextNode::class, $text);
        self::assertSame(2, $text->getLine());
        self::assertSame(4, $text->getOffset());
        self::assertSame(2, $text->getLevel());
        self::assertSame(null, $text->getOuterNode());
        self::assertInstanceOf(TextToken::class, $text->getToken());
        self::assertSame('source.pug', $text->getFile());

        $p->setOuterNode($div);

        self::assertSame($div, $p->getOuterNode());
    }
}
