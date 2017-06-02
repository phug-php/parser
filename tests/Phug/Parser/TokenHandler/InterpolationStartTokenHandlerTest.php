<?php

namespace Phug\Test\Parser\TokenHandler;

use Phug\Lexer;
use Phug\Lexer\Token\AttributeToken;
use Phug\Parser\State;
use Phug\Parser\TokenHandler\InterpolationStartTokenHandler;
use Phug\Test\AbstractParserTest;

/**
 * @coversDefaultClass Phug\Parser\TokenHandler\InterpolationStartTokenHandler
 */
class InterpolationStartTokenHandlerTest extends AbstractParserTest
{
    /**
     * @covers ::<public>
     * @covers \Phug\Parser\TokenHandler\InterpolationEndTokenHandler::<public>
     */
    public function testHandleToken()
    {
        $template = "p\n  |#{\$var} foo\n  | bar";
        $this->assertNodes($template, [
            '[DocumentNode]',
            '  [ElementNode]',
            '    [ExpressionNode]',
            '    [TextNode]',
            '    [TextNode]',
        ]);
        $document = $this->parser->parse($template);
        self::assertSame('$var', $document->getChildAt(0)->getChildAt(0)->getValue());

        $template = "p.\n  foo\n  #{'hi'}";
        $this->assertNodes($template, [
            '[DocumentNode]',
            '  [ElementNode]',
            '    [TextNode]',
            '    [ExpressionNode]',
            '    [TextNode]',
        ]);
        $document = $this->parser->parse($template);
        self::assertSame("'hi'", $document->getChildAt(0)->getChildAt(1)->getValue());
    }

    /**
     * @covers                   ::<public>
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage You can only pass interpolation start tokens to this token handler
     */
    public function testHandleTokenTokenException()
    {
        $lexer = new Lexer();
        $state = new State($lexer->lex('div'));
        $handler = new InterpolationStartTokenHandler();
        $handler->handleToken(new AttributeToken(), $state);
    }
}
