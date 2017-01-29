<?php

namespace Phug\Test\Parser;

use Phug\Lexer;
use Phug\Lexer\Token\AttributeToken;
use Phug\Lexer\Token\NewLineToken;
use Phug\Lexer\Token\TagToken;
use Phug\Parser\Node\DocumentNode;
use Phug\Parser\Node\ElementNode;
use Phug\Parser\State;
use Phug\Parser\TokenHandler\TagTokenHandler;

/**
 * @coversDefaultClass Phug\Parser\State
 */
class StateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::<public>
     */
    public function testGettersAndSetters()
    {
        $lexer = new Lexer();
        $state = new State($lexer->lex('div'));

        $state->setLevel(3);

        self::assertSame(3, $state->getLevel());

        $state->setTokens([1, 2]);

        self::assertSame([1, 2], $state->getTokens());

        self::assertInstanceOf(DocumentNode::class, $state->getDocumentNode());

        $element = new ElementNode();

        $state->setParentNode($element);

        self::assertSame($element, $state->getParentNode());

        $element = new ElementNode();

        $state->setCurrentNode($element);

        self::assertSame($element, $state->getCurrentNode());

        $element = new ElementNode();

        $state->setLastNode($element);

        self::assertSame($element, $state->getLastNode());

        $element = new ElementNode();

        $state->setOuterNode($element);

        self::assertSame($element, $state->getOuterNode());
    }

    /**
     * @covers ::<public>
     */
    public function testTokensCrawler()
    {
        $lexer = new Lexer();
        $state = new State($lexer->lex("div\np"));

        self::assertTrue($state->hasTokens());
        self::assertSame($state, $state->nextToken());
        self::assertSame('p', $state->nextToken()->getToken()->getName());
        self::assertFalse($state->nextToken()->hasTokens());
    }

    /**
     * @covers ::handleToken
     * @covers ::getNamedHandler
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
     * @covers ::handleToken
     * @covers ::getNamedHandler
     */
    public function testHandleTokenTwice()
    {
        $lexer = new Lexer();
        $state = new State($lexer->lex('div Hello'), [
            'token_handlers'   => [
                TagToken::class => TagTokenHandler::class,
            ],
        ]);

        $tag = new TagToken();
        $tag->setName('foo');
        $state->handleToken($tag);

        self::assertSame('foo', $state->getCurrentNode()->getName());

        // Should works twice and use the chaced named handler.
        $bar = new ElementNode();
        $state->setCurrentNode($bar);
        $tag = new TagToken();
        $tag->setName('bar');
        $state->handleToken($tag);

        self::assertSame('bar', $bar->getName());
    }

    /**
     * @covers                   ::handleToken
     * @expectedException        \Phug\ParserException
     * @expectedExceptionMessage Failed to parse: Unexpected token
     * @expectedExceptionMessage `Phug\Lexer\Token\TagToken`,
     * @expectedExceptionMessage no token handler registered
     */
    public function testHandleTokenException()
    {
        $lexer = new Lexer();
        $state = new State($lexer->lex('div'));

        $tag = new TagToken();
        $state->handleToken($tag);
    }

    /**
     * @covers ::handleToken
     */
    public function testHandleTokenWithAnInstance()
    {
        $lexer = new Lexer();
        $handler = new TagTokenHandler();
        $state = new State($lexer->lex('div'), [
            'token_handlers'   => [
                TagToken::class => $handler,
            ],
        ]);

        $tag = new TagToken();
        $tag->setName('foo');
        $state->handleToken($tag);

        self::assertSame('foo', $state->getCurrentNode()->getName());
    }

    /**
     * @covers ::lookUp
     */
    public function testLookUp()
    {
        $tokens = [];
        $lexer = new Lexer();
        $handler = new TagTokenHandler();
        $state = new State($lexer->lex('div'));

        foreach ($state->lookUp([TagToken::class]) as $token) {
            $tokens[] = $token;
        }

        self::assertSame(1, count($tokens));
        self::assertInstanceOf(TagToken::class, $tokens[0]);

        $tokens = [];
        $lexer = new Lexer();
        $handler = new TagTokenHandler();
        $state = new State($lexer->lex('div'));

        foreach ($state->lookUp([AttributeToken::class]) as $token) {
            $tokens[] = $token;
        }

        self::assertSame(0, count($tokens));
    }

    /**
     * @covers ::lookUpNext
     */
    public function testLookUpNext()
    {
        $tokens = [];
        $lexer = new Lexer();
        $handler = new TagTokenHandler();
        $state = new State($lexer->lex("\ndiv\np"));
        $types = [TagToken::class, NewLineToken::class];

        foreach ($state->lookUpNext($types) as $token) {
            $tokens[] = $token;
        }

        self::assertSame(3, count($tokens));
        self::assertInstanceOf(TagToken::class, $tokens[0]);
        self::assertSame('div', $tokens[0]->getName());
        self::assertInstanceOf(NewLineToken::class, $tokens[1]);
        self::assertInstanceOf(TagToken::class, $tokens[2]);
        self::assertSame('p', $tokens[2]->getName());
    }

    /**
     * @covers ::expect
     * @covers ::expectNext
     */
    public function testExpect()
    {
        $tokens = [];
        $lexer = new Lexer();
        $handler = new TagTokenHandler();
        $state = new State($lexer->lex("\ndiv\n+p"));
        $types = [TagToken::class, NewLineToken::class];

        self::assertInstanceOf(TagToken::class, $state->expectNext($types));
        self::assertInstanceOf(NewLineToken::class, $state->expectNext($types));
        self::assertSame(null, $state->expectNext($types));
    }
}
