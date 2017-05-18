<?php

namespace Phug\Test\Parser\TokenHandler;

use Phug\Lexer;
use Phug\Lexer\Token\TagToken;
use Phug\Parser\State;
use Phug\Parser\TokenHandler\WhileTokenHandler;
use Phug\Test\AbstractParserTest;

/**
 * @coversDefaultClass Phug\Parser\TokenHandler\WhileTokenHandler
 */
class WhileTokenHandlerTest extends AbstractParserTest
{
    /**
     * @covers ::<public>
     */
    public function testHandleToken()
    {
        $this->assertNodes("while \$i < 3\n  p=\$i", [
            '[DocumentNode]',
            '  [WhileNode]',
            '    [ElementNode]',
            '      [ExpressionNode]',
        ]);
    }

    /**
     * @covers                   ::<public>
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage You can only pass while tokens to this token handler
     */
    public function testHandleTokenTokenException()
    {
        $lexer = new Lexer();
        $state = new State($lexer->lex(''));
        $handler = new WhileTokenHandler();
        $handler->handleToken(new TagToken(), $state);
    }
}
