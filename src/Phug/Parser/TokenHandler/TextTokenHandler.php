<?php

namespace Phug\Parser\TokenHandler;

use Phug\Lexer\Token\TagToken;
use Phug\Lexer\Token\TextToken;
use Phug\Parser\Node\ElementNode;
use Phug\Parser\Node\TextNode;
use Phug\Parser\State;
use Phug\Parser\TokenHandlerInterface;
use Phug\Lexer\TokenInterface;

class TextTokenHandler implements TokenHandlerInterface
{

    public function handleToken(TokenInterface $token, State $state)
    {

        if (!($token instanceof TextToken))
            throw new \RuntimeException(
                "You can only pass text tokens to this token handler"
            );

        /** @var TextNode $node */
        $node = $state->createNode(TextNode::class, $token);
        $node->setValue($token->getValue());
        $node->setLevel($token->getLevel());
        $node->setIsEscaped($token->isEscaped());

        if ($state->getCurrentNode()) {

            $state->getCurrentNode()->appendChild($node);
        } else
            $state->setCurrentNode($node);
    }
}