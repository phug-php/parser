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
    public function testhandleSingleLine()
    {
        $this->assertNodes('- do_something();', [
            '[DocumentNode]',
            '  [CodeNode]',
            '    [TextNode]',
        ]);
        $this->assertNodes("code- do_something();\n  | foo\n| bar", [
            '[DocumentNode]',
            '  [ElementNode]',
            '    [CodeNode]',
            '      [TextNode]',
            '    [TextNode]',
            '  [TextNode]',
        ]);
    }
    /**
     * @covers ::<public>
     */
    public function testhandleBlock()
    {
        $this->assertNodes("-\n  foo();\n  bar();", [
            '[DocumentNode]',
            '  [CodeNode]',
            '    [TextNode]',
            '    [TextNode]',
        ]);
    }

    /**
     * @covers ::<public>
     */
    public function testHandleCodeValue()
    {
        $document = $this->parser->parse('- do_something();');
        $element = $document->getChildren()[0];

        self::assertInstanceOf(CodeNode::class, $element);

        $text = $element->getChildren()[0];

        self::assertSame('do_something();', $text->getValue());
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
}
