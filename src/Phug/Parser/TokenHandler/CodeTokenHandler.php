<?php

namespace Phug\Parser\TokenHandler;

use Phug\Lexer\Token\CodeToken;
use Phug\Lexer\Token\TextToken;
use Phug\Lexer\TokenInterface;
use Phug\Parser\Node\CodeNode;
use Phug\Parser\State;
use Phug\Parser\TokenHandlerInterface;
use Phug\ParserException;

class CodeTokenHandler implements TokenHandlerInterface
{
    public function handleToken(TokenInterface $token, State $state)
    {
        if (!($token instanceof CodeToken)) {
            throw new \RuntimeException(
                'You can only pass code tokens to this token handler'
            );
        }

        /** @var CodeNode $node */
        $node = $state->createNode(CodeNode::class, $token);

        if ($state->getCurrentNode()) {
            $token = $state->expectNext([TextToken::class]);
            if (!$token) {
                throw new ParserException(
                    'Unexpected token `blockcode` expected `text`, `interpolated-code` or `code`'
                );
            }
            $node->setValue($token->getValue());
        }

        $state->append($node);
    }
}
