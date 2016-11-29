<?php

namespace Phug\Parser\TokenHandler;

use Phug\Lexer\Token\TagToken;
use Phug\Parser\Node\ElementNode;
use Phug\Parser\State;
use Phug\Parser\TokenHandlerInterface;
use Phug\Lexer\TokenInterface;

class TagTokenHandler implements TokenHandlerInterface
{

    public function handleToken(TokenInterface $token, State $state)
    {

        if (!($token instanceof TagToken))
            throw new \RuntimeException(
                "You can only pass tag tokens to this token handler"
            );

        if (!$state->getCurrentNode())
            $state->setCurrentNode($state->createNode(ElementNode::class, $token));

        if (!$state->currentNodeIs([ElementNode::class]))
            $state->throwException(
                'Tags can only be used on elements',
                $token
            );

        /** @var ElementNode $current */
        $current = $state->getCurrentNode();

        if ($current->getName())
            $state->throwException(
                'The element already has a tag name',
                $token
            );

        $current->setName($token->getName());
    }
}