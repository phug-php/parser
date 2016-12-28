<?php

namespace Phug\Parser\TokenHandler;

use Phug\Lexer\Token\FilterToken;
use Phug\Lexer\TokenInterface;
use Phug\Parser\Node\FilterNode;
use Phug\Parser\State;
use Phug\Parser\TokenHandlerInterface;

class FilterTokenHandler implements TokenHandlerInterface
{
    public function handleToken(TokenInterface $token, State $state)
    {
        if (!($token instanceof FilterToken)) {
            throw new \RuntimeException(
                'You can only pass filter tokens to this token handler'
            );
        }

        /** @var FilterNode $node */
        $node = $state->createNode(FilterNode::class, $token);
        $node->setName($token->getName());
        $state->setCurrentNode($node);
    }
}
