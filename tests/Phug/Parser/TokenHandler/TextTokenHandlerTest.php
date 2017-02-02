<?php

namespace Phug\Test\Parser\TokenHandler;

use Phug\Lexer;
use Phug\Lexer\Token\TagToken;
use Phug\Parser\State;
use Phug\Parser\TokenHandler\TextTokenHandler;
use Phug\Test\AbstractParserTest;

/**
 * @coversDefaultClass Phug\Parser\TokenHandler\TextTokenHandler
 */
class TextTokenHandlerTest extends AbstractParserTest
{
    /**
     * @covers ::<public>
     */
    public function testhandleToken()
    {
        $this->assertNodes("p foo", [
            '[DocumentNode]',
            '  [ElementNode]',
            '    [TextNode]',
        ]);
        $this->assertNodes("p\n  | foo", [
            '[DocumentNode]',
            '  [ElementNode]',
            '    [TextNode]',
        ]);
    }

    /**
     * @covers                   ::<public>
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage You can only pass text tokens to this token handler
     */
    public function testHandleTokenTokenException()
    {
        $lexer = new Lexer();
        $state = new State($lexer->lex(''));
        $handler = new TextTokenHandler();
        $handler->handleToken(new TagToken(), $state);
    }
}
