<?php

namespace Phug\Test\Parser\TokenHandler;

use Phug\Lexer;
use Phug\Lexer\Token\AutoCloseToken;
use Phug\Parser\Node\TextNode;
use Phug\Parser\State;
use Phug\Parser\TokenHandler\AutoCloseTokenHandler;
use Phug\Test\AbstractParserTest;

/**
 * @coversDefaultClass Phug\Parser\TokenHandler\AutoCloseTokenHandler
 */
class AutoCloseTokenHandlerTest extends AbstractParserTest
{
    /**
     * @covers ::<public>
     */
    public function testHandleToken()
    {
        $this->assertNodes('tag', [
            '[DocumentNode]',
            '  [ElementNode]',
        ]);
        $this->assertNodes('tag/', [
            '[DocumentNode]',
            '  [ElementNode]',
        ]);

        $document = $this->parser->parse('tag');
        $element = $document->getChildren()[0];

        self::assertFalse($element->isAutoClosed());

        $document = $this->parser->parse('tag/');
        $element = $document->getChildren()[0];

        self::assertTrue($element->isAutoClosed());
    }

    /**
     * @covers                   ::<public>
     * @expectedException        \Phug\ParserException
     * @expectedExceptionMessage Auto-close operators can only be used on elements
     */
    public function testHandleClassOnWrongNode()
    {
        $lexer = new Lexer();
        $state = new State($lexer->lex('| foo'));
        $state->setCurrentNode(new TextNode());
        $handler = new AutoCloseTokenHandler();
        $handler->handleToken(new AutoCloseToken(), $state);
    }
}
