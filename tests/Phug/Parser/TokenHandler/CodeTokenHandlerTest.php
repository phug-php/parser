<?php

namespace Phug\Test\Parser\TokenHandler;

use Phug\Lexer;
use Phug\Lexer\Token\TagToken;
use Phug\Parser\Node\CodeNode;
use Phug\Parser\State;
use Phug\Parser\TokenHandler\CodeTokenHandler;
use Phug\Test\AbstractParserTest;

/**
 * @coversDefaultClass Phug\Parser\TokenHandler\CodeTokenHandler
 */
class CodeTokenHandlerTest extends AbstractParserTest
{
    /**
     * @covers ::<public>
     */
    public function testHandleSingleLine()
    {
        $this->assertNodes('- do_something();', [
            '[DocumentNode]',
            '  [CodeNode]',
            '    [TextNode]',
        ]);
        $this->assertNodes("code- do_something()\n  | foo\n| bar", [
            '[DocumentNode]',
            '  [ElementNode]',
            '    [CodeNode]',
            '    [TextNode]',
            '  [TextNode]',
        ]);
        $documentNodes = $this->parser->parse("code- do_something()\n  | foo\n| bar")->getChildren();
        self::assertSame('code', $documentNodes[0]->getName());
        self::assertSame('bar', $documentNodes[1]->getValue());
        $elementNodes = $documentNodes[0]->getChildren();
        self::assertSame('do_something()', $elementNodes[0]->getValue());
        self::assertSame('foo', $elementNodes[1]->getValue());
    }

    /**
     * @group i
     * @covers ::<public>
     */
    public function testHandleBlock()
    {
        $this->assertNodes("-\n  foo();\n  bar();", [
            '[DocumentNode]',
            '  [CodeNode]',
            '    [TextNode]',
        ]);
        $documentNodes = $this->parser->parse("-\n  foo();\n  bar();")->getChildren();
        self::assertSame("foo();\nbar();", $documentNodes[0]->getChildren()[0]->getValue());
    }

    /**
     * @covers ::<public>
     */
    public function testHandleCodeValue()
    {
        $document = $this->parser->parse('- do_something()');
        $element = $document->getChildren()[0];

        self::assertInstanceOf(CodeNode::class, $element);

        $text = $element->getChildren()[0];

        self::assertSame('do_something()', $text->getValue());
    }

    /**
     * @covers                   ::<public>
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage You can only pass code tokens to this token handler
     */
    public function testHandleTokenTokenException()
    {
        $lexer = new Lexer();
        $state = new State($lexer->lex('div- do_something()'));
        $handler = new CodeTokenHandler();
        $handler->handleToken(new TagToken(), $state);
    }

    /**
     * @covers                   ::<public>
     * @expectedException        \Phug\ParserException
     * @expectedExceptionMessage Unexpected token `blockcode` expected `text`, `interpolated-code` or `code`
     */
    public function testHandleTokenUnexpectedBlock()
    {
        $this->parser->parse('div-');
    }
}
