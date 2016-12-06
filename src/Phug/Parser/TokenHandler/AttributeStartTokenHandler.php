<?php

namespace Phug\Parser\TokenHandler;

use Phug\Lexer\Token\AttributeEndToken;
use Phug\Lexer\Token\AttributeStartToken;
use Phug\Lexer\Token\AttributeToken;
use Phug\Parser\Node\AssignmentNode;
use Phug\Parser\Node\ElementNode;
use Phug\Parser\Node\ImportNode;
use Phug\Parser\Node\MixinCallNode;
use Phug\Parser\Node\MixinNode;
use Phug\Parser\Node\VariableNode;
use Phug\Parser\State;
use Phug\Parser\TokenHandlerInterface;
use Phug\Lexer\TokenInterface;

class AttributeStartTokenHandler implements TokenHandlerInterface
{

    public function handleToken(TokenInterface $token, State $state)
    {

        if (!($token instanceof AttributeStartToken)) {
            throw new \RuntimeException(
                "You can only pass attribute start tokens to this token handler"
            );
        }

        if (!$state->getCurrentNode()) {
            $state->setCurrentNode($state->createNode(ElementNode::class, $token));
        }

        if (!$state->currentNodeIs([
            ElementNode::class, AssignmentNode::class,
            ImportNode::class, VariableNode::class,
            MixinNode::class, MixinCallNode::class
        ])) {
            $state->throwException(
                "Attributes can only be placed on element, assignment, "
                ."import, variable, mixin and mixinCall",
                $token
            );
        }

        foreach ($state->lookUpNext([AttributeToken::class]) as $subToken) {
            $state->handleToken($subToken);
        }

        if (!$state->expect([AttributeEndToken::class])) {
            $state->throwException(
                "Attribute list not closed",
                $token
            );
        }
    }
}
