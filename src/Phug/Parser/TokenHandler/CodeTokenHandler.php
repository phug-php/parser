<?php

namespace Phug\Parser\TokenHandler;

use Phug\Lexer\Token\CodeToken;
use Phug\Parser\Node\ExpressionNode;
use Phug\Parser\State;
use Phug\Parser\TokenHandlerInterface;
use Phug\Lexer\TokenInterface;

class CodeTokenHandler implements TokenHandlerInterface
{

    public function handleToken(TokenInterface $token, State $state)
    {

        if (!($token instanceof CodeToken)) {
            throw new \RuntimeException(
                "You can only pass code tokens to this token handler"
            );
        }

        /** @var ExpressionNode $node */
        $node = $state->createNode(ExpressionNode::class, $token);
        $node->setIsEscaped($token->isEscaped());
        $node->setIsChecked($token->isChecked());
        $node->setValue($token->getValue());

        if ($state->getCurrentNode()) {
            $state->getCurrentNode()->appendChild($node);
        } else {
            $state->setCurrentNode($node);
        }
    }
}
