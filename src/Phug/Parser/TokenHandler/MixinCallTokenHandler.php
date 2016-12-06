<?php

namespace Phug\Parser\TokenHandler;

use Phug\Lexer\Token\MixinCallToken;
use Phug\Parser\Node\MixinCallNode;
use Phug\Parser\State;
use Phug\Parser\TokenHandlerInterface;
use Phug\Lexer\TokenInterface;

class MixinCallTokenHandler implements TokenHandlerInterface
{

    public function handleToken(TokenInterface $token, State $state)
    {

        if (!($token instanceof MixinCallToken)) {
            throw new \RuntimeException(
                "You can only pass mixin call tokens to this token handler"
            );
        }

        /** @var MixinCallNode $node */
        $node = $state->createNode(MixinCallNode::class, $token);
        $node->setName($token->getName());
        $state->setCurrentNode($node);
    }
}
