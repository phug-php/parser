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
        $previousNode = $state->getLastNode();
        if ($previousNode instanceof TextNode && $previousNode === $state->getLastNodeBeforeNewLine()) {
            $value = "\n".$value;
        }
        $node->setValue($value);
        $node->setLevel($token->getLevel());
        $node->setIsEscaped($token->isEscaped());
        $node->setIndent($token->getIndent());

        $state->append($node);
    }
}
