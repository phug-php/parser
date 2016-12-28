<?php

namespace Phug\Test;

use Phug\Parser;
use Phug\Parser\Node\DocumentNode;

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

    public function testParseAssignment()
    {
        self::assertInstanceOf(DocumentNode::class, $this->parser->parse('&some-assignment'));
    }
}
