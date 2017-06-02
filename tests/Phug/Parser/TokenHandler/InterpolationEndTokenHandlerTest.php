<?php

namespace Phug\Test\Parser\TokenHandler;

use Phug\Lexer;
use Phug\Lexer\Token\AttributeToken;
use Phug\Parser\State;
use Phug\Parser\TokenHandler\InterpolationEndTokenHandler;

/**
 * @coversDefaultClass Phug\Parser\TokenHandler\InterpolationEndTokenHandler
 */
class InterpolationEndTokenHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers                   ::<public>
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage You can only pass interpolation end tokens to this token handler
     */
    public function testHandleTokenTokenException()
    {
        $lexer = new Lexer();
        $state = new State($lexer->lex('div'));
        $handler = new InterpolationEndTokenHandler();
        $handler->handleToken(new AttributeToken(), $state);
    }
}
