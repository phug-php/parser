<?php

namespace Phug\Test;

use Phug\Parser;

class ParserTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Parser
     */
    private $parser;

    public function setUp()
    {


        $this->parser = new Parser;
    }

    public function testParseAssignment()
    {

        var_dump($this->parser->parse('&some-assignment'));
    }
}