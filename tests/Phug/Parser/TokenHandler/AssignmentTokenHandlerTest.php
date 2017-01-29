<?php

namespace Phug\Test\Parser\TokenHandler;

use Phug\Lexer;
use Phug\Lexer\Token\AssignmentToken;
use Phug\Lexer\Token\AttributeToken;
use Phug\Lexer\Token\AttributeEndToken;
use Phug\Lexer\Token\AttributeStartToken;
use Phug\Lexer\Token\TagToken;
use Phug\Parser\Node\DocumentNode;
use Phug\Parser\State;
use Phug\Parser\TokenHandler\AssignmentTokenHandler;
use Phug\Parser\TokenHandler\AttributeTokenHandler;
use Phug\Parser\TokenHandler\AttributeEndTokenHandler;
use Phug\Parser\TokenHandler\AttributeStartTokenHandler;

/**
 * @coversDefaultClass Phug\Parser\TokenHandler\AssignmentTokenHandler 
 */
class AssignmentTokenHandlerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::<public>
     */
    public function testHandleToken()
    {
        $lexer = new Lexer();
        $state = new State($lexer->lex('&attributes($a)'), [
            'token_handlers'   => [
                AssignmentToken::class     => AssignmentTokenHandler::class,
                AttributeStartToken::class => AttributeStartTokenHandler::class,
                AttributeEndToken::class   => AttributeEndTokenHandler::class,
                AttributeToken::class      => AttributeTokenHandler::class,
            ],
        ]);

        $state->handleToken();
        $assignments = [];
        foreach ($state->getCurrentNode()->getAssignments() as $assignment) {
            $assignments[] = $assignment;
        }

        self::assertSame(1, count($assignments));
        self::assertSame('attributes', $assignments[0]->getName());
    }

    /**
     * @covers ::<public>
     */
    public function testHandleTokenWithNothingNext()
    {
        $lexer = new Lexer();
        $state = new State($lexer->lex('&attributes'), [
            'token_handlers'   => [
                AssignmentToken::class     => AssignmentTokenHandler::class,
                AttributeStartToken::class => AttributeStartTokenHandler::class,
                AttributeEndToken::class   => AttributeEndTokenHandler::class,
                AttributeToken::class      => AttributeTokenHandler::class,
            ],
        ]);

        $state->handleToken();
        $assignments = [];
        foreach ($state->getCurrentNode()->getAssignments() as $assignment) {
            $assignments[] = $assignment;
        }

        self::assertSame(1, count($assignments));
        self::assertSame('attributes', $assignments[0]->getName());
    }

    /**
     * @covers                   ::<public>
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage You can only pass Assignment tokens to this token handler
     */
    public function testHandleTokenTokenException()
    {
        $lexer = new Lexer();
        $state = new State($lexer->lex('div'));
        $handler = new AssignmentTokenHandler();
        $handler->handleToken(new AttributeToken(), $state);
    }

    /**
     * @covers                   ::<public>
     * @expectedException        \Phug\ParserException
     * @expectedExceptionMessage Failed to parse: Assignments can only happen on elements and mixinCalls
     */
    public function testHandleTokenElementTagsException()
    {
        $lexer = new Lexer();
        $state = new State($lexer->lex('div'), [
            'token_handlers'   => [
                AssignmentToken::class     => AssignmentTokenHandler::class,
                AttributeStartToken::class => AttributeStartTokenHandler::class,
                AttributeEndToken::class   => AttributeEndTokenHandler::class,
                AttributeToken::class      => AttributeTokenHandler::class,
            ],
        ]);

        $assignment = new AssignmentToken();
        $assignment->setName('foo');
        $state->setCurrentNode(new DocumentNode());
        $state->handleToken($assignment);
    }

    /**
     * @covers                   ::<public>
     * @expectedException        \Phug\ParserException
     * @expectedExceptionMessage Failed to parse: The element already has a tag name
     */
    public function skip_testHandleTokenTagNameException()
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
