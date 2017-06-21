<?php

namespace Phug\Test\Parser\TokenHandler;

use Phug\Formatter\Element\AttributeElement;
use Phug\Lexer;
use Phug\Lexer\Token\TagToken;
use Phug\Parser\Node\DocumentNode;
use Phug\Parser\Node\FilterNode;
use Phug\Parser\State;
use Phug\Parser\TokenHandler\FilterTokenHandler;
use Phug\Test\AbstractParserTest;

/**
 * @coversDefaultClass Phug\Parser\TokenHandler\FilterTokenHandler
 */
class FilterTokenHandlerTest extends AbstractParserTest
{
    /**
     * @covers ::<public>
     */
    public function testHandleToken()
    {
        $this->assertNodes(':foo', [
            '[DocumentNode]',
            '  [FilterNode]',
        ]);
        $template = ':foo(baz="bar") Bla';
        $this->assertNodes($template, [
            '[DocumentNode]',
            '  [FilterNode]',
            '    [TextNode]',
        ]);
        $document = $this->parser->parse($template);
        /** @var FilterNode $filter */
        $filter = $document->getChildAt(0);
        /** @var AttributeElement $attribute */
        $attribute = null;
        foreach ($filter->getAttributes() as $item) {
            $attribute = $item;
        }
        self::assertSame('"bar"', $attribute->getValue());
        self::assertSame('baz', $attribute->getName());

        $template = 'include:coffee(foo=1 bar biz=9) file.coffee';
        $this->assertNodes($template, [
            '[DocumentNode]',
            '  [ImportNode]',
            '  [FilterNode]',
        ]);
        $document = $this->parser->parse($template);
        /** @var FilterNode $filter1 */
        $filter1 = $document->getChildAt(0)->getFilter();
        /** @var FilterNode $filter2 */
        $filter2 = $document->getChildAt(1);
        $attribute = null;
        foreach ($filter1->getAttributes() as $item) {
            if ($item->getName() === 'biz') {
                $attribute = $item->getValue();
            }
        }
        self::assertSame('9', $attribute);
        self::assertSame($filter1, $filter2);
    }

    /**
     * @covers                   ::<public>
     * @expectedException        \RuntimeException
     * @expectedExceptionMessage You can only pass filter tokens to this token handler
     */
    public function testHandleTokenTokenException()
    {
        $lexer = new Lexer();
        $state = new State($lexer->lex(''));
        $handler = new FilterTokenHandler();
        $handler->handleToken(new TagToken(), $state);
    }
}
