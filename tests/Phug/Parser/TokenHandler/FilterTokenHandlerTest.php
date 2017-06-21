<?php

namespace Phug\Test\Parser\TokenHandler;

use Phug\Formatter\Element\AttributeElement;
use Phug\Lexer;
use Phug\Lexer\Token\TagToken;
use Phug\Parser\Node\DocumentNode;
use Phug\Parser\Node\FilterNode;
use Phug\Parser\State;
use Phug\Parser\TokenHandler\FilterTokenHandler;
use Phug\Test\AbstractParserTest;

/**
 * @coversDefaultClass Phug\Parser\TokenHandler\FilterTokenHandler
 */
class FilterTokenHandlerTest extends AbstractParserTest
{
    /**
     * @covers ::<public>
     */
    public function testHandleToken()
    {
        $this->assertNodes(':foo', [
            '[DocumentNode]',
            '  [FilterNode]',
        ]);
        $template = ':foo(baz="bar") Bla';
        $this->assertNodes($template, [
            '[DocumentNode]',
            '  [FilterNode]',
            '    [TextNode]',
        ]);
        $document = $this->parser->parse($template);
        /** @var FilterNode $filter */
        $filter = $document->getChildAt(0);
        /** @var AttributeElement $attribute */
        $attribute = null;
        foreach ($filter->getAttributes() as $item) {
            $attribute = $item;
        }
        self::assertSame('"bar"', $attribute->getValue());
        self::assertSame('baz', $attribute->getName());
    }

    /**
     * @covers                   ::<public>
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage You can only pass filter tokens to this token handler
     */
    public function testHandleTokenTokenException()
    {
        $lexer = new Lexer();
        $state = new State($lexer->lex(''));
        $handler = new FilterTokenHandler();
        $handler->handleToken(new TagToken(), $state);
    }
}
