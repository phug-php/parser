<?php

namespace Phug\Parser\TokenHandler;

use Phug\Lexer\Token\AttributeToken;
use Phug\Parser\Node\AttributeNode;
use Phug\Parser\Node\ElementNode;
use Phug\Parser\Node\MixinCallNode;
use Phug\Parser\State;
use Phug\Parser\TokenHandlerInterface;
use Phug\Lexer\TokenInterface;

class AttributeTokenHandler implements TokenHandlerInterface
{

    public function handleToken(TokenInterface $token, State $state)
    {

        if (!($token instanceof AttributeToken)) {
            throw new \RuntimeException(
                "You can only pass attribute tokens to this token handler"
            );
        }

        if (!$state->getCurrentNode()) {
            $state->setCurrentNode($state->createNode(ElementNode::class, $token));
        }

        /** @var AttributeNode $node */
        $node = $state->createNode(AttributeNode::class, $token);
        $name = $node->getName();
        $value = $node->getValue();
        $node->setName($name);
        $node->setValue($value);
        $node->setIsEscaped($token->isEscaped());
        $node->setIsChecked($token->isChecked());

        //Mixin calls take the first expression set as the name as the value
        if ($state->currentNodeIs([MixinCallNode::class]) && ($value === '' || $value === null)) {
            $node->setValue($name);
            $node->setName(null);
        }

        /** @var ElementNode|MixinCallNode $current */
        $current = $state->getCurrentNode();
        $current->getAttributes()->attach($node);
    }
}
