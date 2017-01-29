<?php

namespace Phug\Test;

use Phug\Lexer;
use Phug\Parser;
use Phug\Parser\Node\DocumentNode;

/**
 * @coversDefaultClass Phug\Parser
 */
class ParserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Parser
     */
    private $parser;

    public function setUp()
    {
        $this->parser = new Parser();
    }

    /**
     * @covers ::<public>
     */
    public function testParseAssignment()
    {
        self::assertInstanceOf(DocumentNode::class, $this->parser->parse('&some-assignment'));
        self::assertInstanceOf(Lexer::class, $this->parser->getLexer());
    }
    /**
     * @covers                   ::<public>
     * @expectedException        \Phug\ParserException
     * @expectedExceptionMessage Passed lexer class ErrorException is not a valid Phug\Lexer
     */
    public function testWrongLexerClassNameOption()
    {
        new Parser([
            'lexer_class_name' => \ErrorException::class,
        ]);
    }

    /**
     * @covers                   ::<public>
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage Passed token handler needs to implement Phug\Parser\TokenHandlerInterface
     */
    public function testWrongTokenHandler()
    {
        $this->parser->setTokenHandler('error', \ErrorException::class);
    }

    /**
     * @covers                   ::<public>
     * @covers                   ::dumpNode
     * @expectedException        \InvalidArgumentException
     * @expectedExceptionMessage state_class_name needs to be a valid Phug\Parser\State sub class
     */
    public function testWrongStateClassNameOption()
    {
        $parser = new Parser([
            'state_class_name' => \ErrorException::class,
        ]);
        $parser->parse('');
    }

    /**
     * @covers ::<public>
     * @covers ::dumpNode
     */
    public function testDump()
    {
        self::assertSame(
            "[Phug\\Parser\\Node\\DocumentNode]\n".
            "  [Phug\\Parser\\Node\\ElementNode]\n".
            "    [Phug\\Parser\\Node\\ElementNode]\n".
            "      [Phug\\Parser\\Node\\ElementNode]\n".
            "        [Phug\\Parser\\Node\\TextNode]\n".
            "  [Phug\\Parser\\Node\\ElementNode]",
            $this->parser->dump("div\n  section: p Hello\nfooter")
        );
    }
}
