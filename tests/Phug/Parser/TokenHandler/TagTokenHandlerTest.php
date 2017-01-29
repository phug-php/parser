<?php

namespace Phug\Test\Parser\TokenHandler;

use Phug\Lexer;
use Phug\Lexer\Token\TagToken;
use Phug\Parser\State;
use Phug\Parser\TokenHandler\TagTokenHandler;

/**
 * @coversDefaultClass Phug\Parser\TokenHandler\TagTokenHandler
 */
class TagTokenHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::<public>
     */
    public function testHandleToken()
    {
        $lexer = new Lexer();
        $state = new State($lexer->lex('div'), [
            'token_handlers'   => [
                TagToken::class => TagTokenHandler::class,
            ],
        ]);

        $tag = new TagToken();
        $tag->setName('foo');
        $state->handleToken($tag);

        self::assertSame('foo', $state->getCurrentNode()->getName());
    }

    /**
     * @covers                   ::<public>
     * @expectedException        \Phug\ParserException
     * @expectedExceptionMessage Failed to parse: The element already has a tag name
     */
    public function testHandleTokenException()
    {
        $lexer = new Lexer();
        $state = new State($lexer->lex('div'), [
            'token_handlers'   => [
                TagToken::class => TagTokenHandler::class,
            ],
        ]);

        $tag = new TagToken();
        $tag->setName('foo');
        $state->handleToken($tag);

        $tag = new TagToken();
        $tag->setName('foo');
        $state->handleToken($tag);
    }
}
