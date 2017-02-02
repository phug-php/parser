<?php

namespace Phug\Test\Parser\TokenHandler;

use Phug\Lexer;
use Phug\Lexer\Token\TagToken;
use Phug\Parser\State;
use Phug\Parser\TokenHandler\ImportTokenHandler;
use Phug\Test\AbstractParserTest;

/**
 * @coversDefaultClass Phug\Parser\TokenHandler\ImportTokenHandler
 */
class ImportTokenHandlerTest extends AbstractParserTest
{
    /**
     * @covers ::<public>
     */
    public function testhandleToken()
    {
        $this->assertNodes("extends layout\ninclude header", [
            '[DocumentNode]',
            '  [ImportNode]',
            '  [ImportNode]',
        ]);
    }

    /**
     * @covers                   ::<public>
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage You can only pass import tokens to this token handler
     */
    public function testHandleTokenTokenException()
    {
        $lexer = new Lexer();
        $state = new State($lexer->lex(''));
        $handler = new ImportTokenHandler();
        $handler->handleToken(new TagToken(), $state);
    }

    /**
     * @covers                   ::<public>
     * @expectedException        \Phug\ParserException
     * @expectedExceptionMessage extends should be the very first statement in a document
     */
    public function testHandleTokenElementException()
    {
        $this->parser->parse("div\nextends foo");
    }
}
