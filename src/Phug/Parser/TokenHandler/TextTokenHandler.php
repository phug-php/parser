<?php

namespace Phug\Parser\TokenHandler;

use Phug\Lexer\Token\TextToken;
use Phug\Lexer\TokenInterface;
use Phug\Parser\Node\TextNode;
use Phug\Parser\State;
use Phug\Parser\TokenHandlerInterface;

class TextTokenHandler implements TokenHandlerInterface
{
    public function handleToken(TokenInterface $token, State $state)
    {
        if (!($token instanceof TextToken)) {
            throw new \RuntimeException(
                'You can only pass text tokens to this token handler'
            );
        }

        /** @var TextNode $node */
        $node = $state->createNode(TextNode::class, $token);
        $value = $token->getValue();
        if (substr($value, 0, 1) === ' ') {
            $value = substr($value, 1);
        }
        $node->setValue($value);
        $node->setLevel($token->getLevel());
        $node->setIsEscaped($token->isEscaped());
        $node->setIndent($token->getIndent());

        $state->append($node);
    }
}
