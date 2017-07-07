<?php

namespace Phug\Test\Parser\TokenHandler;

use Phug\Lexer;
use Phug\Lexer\Token\AttributeEndToken;
use Phug\Lexer\Token\AttributeStartToken;
use Phug\Lexer\Token\AttributeToken;
use Phug\Lexer\Token\TagToken;
use Phug\Parser;
use Phug\Parser\Node\AttributeNode;
use Phug\Parser\Node\DocumentNode;
use Phug\Parser\Node\ElementNode;
use Phug\Parser\State;
use Phug\Parser\TokenHandler\AttributeEndTokenHandler;
use Phug\Parser\TokenHandler\AttributeStartTokenHandler;
use Phug\Parser\TokenHandler\AttributeTokenHandler;
use Phug\Test\AbstractParserTest;

/**
 * @coversDefaultClass Phug\Parser\TokenHandler\AttributeTokenHandler
 */
class AttributeTokenHandlerTest extends AbstractParserTest
{
    /**
     * @covers ::<public>
     */
    public function testNoCurrentNode()
    {
        $lexer = new Lexer();
        $state = new State(new Parser(), $lexer->lex('(a)'), [
            'token_handlers'   => [
                AttributeStartToken::class => AttributeStartTokenHandler::class,
                AttributeEndToken::class   => AttributeEndTokenHandler::class,
                AttributeToken::class      => AttributeTokenHandler::class,
            ],
        ]);
        $handler = new AttributeStartTokenHandler();
        $handler->handleToken(new AttributeStartToken(), $state);
        $state->setCurrentNode(null);
        $handler = new AttributeTokenHandler();
        $handler->handleToken(new AttributeToken(), $state);

        self::assertInstanceof(ElementNode::class, $state->getCurrentNode());
    }

    /**
     * @covers                   ::<public>
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage You can only pass attribute tokens to this token handler
     */
    public function testHandleTokenTokenException()
    {
        $lexer = new Lexer();
        $state = new State(new Parser(), $lexer->lex('div'));
        $handler = new AttributeTokenHandler();
        $handler->handleToken(new TagToken(), $state);
    }

    /**
     * @covers Phug\Parser\TokenHandler\AttributeEndTokenHandler::<public>
     * @covers Phug\Parser\TokenHandler\AttributeStartTokenHandler::<public>
     * @covers ::<public>
     */
    public function testHandleTokenFull()
    {
        $this->assertNodes('+a(a b)', [
            '[DocumentNode]',
            '  [MixinCallNode]',
        ]);
        $document = $this->parser->parse('+a(a b)');
        $attributes = [];
        $storage = $document->getChildren()[0]->getAttributes();
        foreach ($storage as $attribute) {
            self::assertInstanceOf(AttributeNode::class, $attribute);
            $attributes[] = $attribute->getValue();
        }
        self::assertSame(['a', 'b'], $attributes);
    }
}
