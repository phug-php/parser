<?php

namespace Phug\Test\Parser\TokenHandler;

use Phug\Lexer;
use Phug\Lexer\Token\AttributeToken;
use Phug\Parser\State;
use Phug\Parser\TokenHandler\TagInterpolationStartTokenHandler;
use Phug\Test\AbstractParserTest;

/**
 * @coversDefaultClass Phug\Parser\TokenHandler\TagInterpolationStartTokenHandler
 */
class TagInterpolationStartTokenHandlerTest extends AbstractParserTest
{
    /**
     * @covers ::<public>
     * @covers \Phug\Parser\TokenHandler\TagInterpolationEndTokenHandler::<public>
     */
    public function testHandleToken()
    {
        $template = "p\n  |#[.i i] foo\n  | bar";
        $this->assertNodes($template, [
            '[DocumentNode]',
            '  [ElementNode]',
            '    [ElementNode]',
            '      [TextNode]',
            '    [TextNode]',
            '    [TextNode]',
        ]);
        $document = $this->parser->parse($template);
        self::assertSame('i', $document->getChildAt(0)->getChildAt(0)->getChildAt(0)->getValue());

        $template = "p.\n  foo\n  #[a]";
        $this->assertNodes($template, [
            '[DocumentNode]',
            '  [ElementNode]',
            '    [TextNode]',
            '    [ElementNode]',
            '    [TextNode]',
        ]);
        $document = $this->parser->parse($template);
        self::assertSame('a', $document->getChildAt(0)->getChildAt(1)->getName());
    }

    /**
     * @covers                   ::<public>
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage You can only pass tag interpolation start tokens to this token handler
     */
    public function testHandleTokenTokenException()
    {
        $lexer = new Lexer();
        $state = new State($lexer->lex('div'));
        $handler = new TagInterpolationStartTokenHandler();
        $handler->handleToken(new AttributeToken(), $state);
    }
}
