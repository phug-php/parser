<?php

namespace Phug\Test\Parser\TokenHandler;

use Phug\Lexer;
use Phug\Lexer\Token\AttributeEndToken;
use Phug\Lexer\Token\AttributeToken;
use Phug\Parser\State;
use Phug\Parser\TokenHandler\AttributeEndTokenHandler;

/**
 * @coversDefaultClass Phug\Parser\TokenHandler\AttributeEndTokenHandler
 */
class AttributeEndTokenHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::<public>
     */
    public function testHandleToken()
    {
        $lexer = new Lexer();
        $state = new State($lexer->lex('div'));
        $handler = new AttributeEndTokenHandler();
        $handler->handleToken(new AttributeEndToken(), $state);

        self::assertSame(null, $state->getCurrentNode());
    }

    /**
     * @covers                   ::<public>
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage You can only pass attribute end tokens to this token handler
     */
    public function testHandleTokenTokenException()
    {
        $lexer = new Lexer();
        $state = new State($lexer->lex('div'));
        $handler = new AttributeEndTokenHandler();
        $handler->handleToken(new AttributeToken(), $state);
    }
}
