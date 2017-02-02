<?php

namespace Phug\Test\Parser\TokenHandler;

use Phug\Lexer;
use Phug\Lexer\Token\TagToken;
use Phug\Parser\State;
use Phug\Parser\TokenHandler\MixinCallTokenHandler;
use Phug\Test\AbstractParserTest;

/**
 * @coversDefaultClass Phug\Parser\TokenHandler\MixinCallTokenHandler
 */
class MixinCallTokenHandlerTest extends AbstractParserTest
{
    /**
     * @covers ::<public>
     */
    public function testhandleSingleLine()
    {
        $this->assertNodes('+foo(1, 2)', [
            '[DocumentNode]',
            '  [MixinCallNode]',
        ]);
        $mixin = $this->parser->parse('+foo(1, 2)')->getChildren()[0];
        self::assertSame('foo', $mixin->getName());
    }

    /**
     * @covers                   ::<public>
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage You can only pass mixin call tokens to this token handler
     */
    public function testHandleTokenTokenException()
    {
        $lexer = new Lexer();
        $state = new State($lexer->lex(''));
        $handler = new MixinCallTokenHandler();
        $handler->handleToken(new TagToken(), $state);
    }
}
