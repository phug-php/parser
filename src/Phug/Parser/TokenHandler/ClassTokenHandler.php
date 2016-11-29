<?php

namespace Phug\Parser\TokenHandler;

use Phug\Lexer\Token\ClassToken;
use Phug\Parser\Node\AttributeNode;
use Phug\Parser\Node\ElementNode;
use Phug\Parser\Node\MixinCallNode;
use Phug\Parser\State;
use Phug\Parser\TokenHandlerInterface;
use Phug\Lexer\TokenInterface;

class ClassTokenHandler implements TokenHandlerInterface
{

    public function handleToken(TokenInterface $token, State $state)
    {

        if (!($token instanceof ClassToken))
            throw new \RuntimeException(
                "You can only pass class tokens to this token handler"
            );

        if (!$state->getCurrentNode())
            $state->setCurrentNode($state->createNode(ElementNode::class, $token));

        if (!$state->currentNodeIs([ElementNode::class, MixinCallNode::class]))
            $state->throwException(
                "Classes can only be used on elements and mixin calls",
                $token
            );

        //We actually create a fake class attribute
        /** @var AttributeNode $attr */
        $attr = $state->createNode(AttributeNode::class, $token);
        $attr->setName('class');
        $attr->setValue($token->getName());
        $attr->unescape()->uncheck();

        /** @var ElementNode|MixinCallNode $current */
        $current = $state->getCurrentNode();
        $current->getAttributes()->attach($attr);
    }
}