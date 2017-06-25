<?php

namespace Phug\Parser\TokenHandler;

use Phug\Lexer\Token\InterpolationEndToken;
use Phug\Lexer\Token\NewLineToken;
use Phug\Lexer\Token\TagInterpolationEndToken;
use Phug\Lexer\TokenInterface;
use Phug\Parser\Node\ExpressionNode;
use Phug\Parser\Node\TextNode;
use Phug\Parser\State;
use Phug\Parser\TokenHandlerInterface;

class NewLineTokenHandler implements TokenHandlerInterface
{
    public function handleToken(TokenInterface $token, State $state)
    {
        if (!($token instanceof NewLineToken)) {
            throw new \RuntimeException(
                'You can only pass newline tokens to this token handler'
            );
        }

        $node = $state->getLastNode();
        $linkedInterpolation = $node && $state->getInterpolationStack()->offsetExists($node)
            ? $state->getInterpolationStack()->offsetGet($node)
            : null;
        $state->recordLastNodeBeforeNewLine();
        if ($state->lastNodeIs([
            ExpressionNode::class,
            TextNode::class,
        ]) ||
            $linkedInterpolation instanceof TagInterpolationEndToken ||
            $linkedInterpolation instanceof InterpolationEndToken
        ) {
            /** @var TextNode $node */
            $node = $state->createNode(TextNode::class);
            $node->setValue("\n");
            $state->append($node);
        }
        $state->store();
    }
}
