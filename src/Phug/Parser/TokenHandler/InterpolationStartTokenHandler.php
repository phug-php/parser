<?php

namespace Phug\Parser\TokenHandler;

use Phug\Lexer\Token\InterpolationStartToken;
use Phug\Lexer\TokenInterface;
use Phug\Parser\Node\CodeNode;
use Phug\Parser\Node\ExpressionNode;
use Phug\Parser\Node\TextNode;
use Phug\Parser\State;
use Phug\Parser\TokenHandlerInterface;

class InterpolationStartTokenHandler implements TokenHandlerInterface
{
    public function handleToken(TokenInterface $token, State $state)
    {
        if (!($token instanceof InterpolationStartToken)) {
            throw new \RuntimeException(
                'You can only pass interpolation start tokens to this token handler'
            );
        }

        $node = $state->getCurrentNode();
        if ($state->currentNodeIs([
            TextNode::class,
            CodeNode::class,
            ExpressionNode::class,
        ])) {
            $node = $node->getParent();
        }
        $state->getInterpolationStack()->attach($token->getEnd(), (object) [
            'currentNode' => $node,
            'parentNode'  => $state->getParentNode(),
        ]);
        $state->store();
    }
}
