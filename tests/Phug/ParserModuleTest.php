<?php

namespace Phug\Test;

use Phug\AbstractParserModule;
use Phug\Parser;
use Phug\Parser\Event\NodeEvent;
use Phug\Parser\Node\ElementNode;
use Phug\Parser\Node\TextNode;
use Phug\ParserEvent;

//@codingStandardsIgnoreStart
class ParserTestModule extends AbstractParserModule
{
    public function getEventListeners()
    {
        return [
            ParserEvent::DOCUMENT => function (NodeEvent $e) {
                $e->getNode()->prependChild(new TextNode());
            },
        ];
    }
}

class StateEnterLeaveStoreTestModule extends AbstractParserModule
{
    public function getEventListeners()
    {
        return [
            ParserEvent::STATE_ENTER => function (NodeEvent $e) {
                $node = $e->getNode();
                if ($node instanceof ElementNode && $node->getName() === 'div') {
                    $node->prependChild(new TextNode());
                }
            },
            ParserEvent::STATE_LEAVE => function (NodeEvent $e) {
                $node = $e->getNode();
                if ($node instanceof ElementNode && $node->getName() === 'div') {
                    $node->appendChild(new TextNode());
                }
            },
            ParserEvent::STATE_STORE => function (NodeEvent $e) {
                $node = $e->getNode();
                if ($node instanceof ElementNode && $node->getName() === 'div') {
                    $node->append(new TextNode());
                }
            },
        ];
    }
}

/**
 * @coversDefaultClass Phug\AbstractParserModule
 */
class ParserModuleTest extends AbstractParserTest
{
    /**
     * @covers ::<public>
     */
    public function testTokenEvent()
    {
        self::assertNodes('p Test', [
            '[DocumentNode]',
            '  [ElementNode]',
            '    [TextNode]',
        ]);

        $parser = new Parser(['modules' => [ParserTestModule::class]]);

        self::assertNodes('p Test', [
            '[DocumentNode]',
            '  [TextNode]',
            '  [ElementNode]',
            '    [TextNode]',
        ], $parser);
    }

    /**
     * @covers ::<public>
     */
    public function testStateEnterLeaveStoreEvents()
    {
        self::assertNodes("div\n\tp= \$test\na", [
            '[DocumentNode]',
            '  [ElementNode]',
            '    [ElementNode]',
            '      [ExpressionNode]',
            '  [ElementNode]',
        ]);

        $parser = new Parser(['modules' => [StateEnterLeaveStoreTestModule::class]]);

        self::assertNodes("div\n\tp= \$test\na", [
            '[DocumentNode]',
            '  [ElementNode]',
            '    [TextNode]',
            '    [ElementNode]',
            '      [ExpressionNode]',
            '    [TextNode]',
            '  [TextNode]',
            '  [ElementNode]',
        ], $parser);
    }
}
//@codingStandardsIgnoreEnd
