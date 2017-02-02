<?php

namespace Phug\Test\Parser\TokenHandler;

use Phug\Lexer;
use Phug\Lexer\Token\TagToken;
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
    public function testhandleToken()
    {
        $this->assertNodes(':foo', [
            '[DocumentNode]',
            '  [FilterNode]',
        ]);
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
