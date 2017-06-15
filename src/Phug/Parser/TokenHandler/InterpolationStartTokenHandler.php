<?php

namespace Phug\Parser\TokenHandler;

use Phug\Lexer\Token\InterpolationStartToken;
use Phug\Lexer\TokenInterface;
use Phug\Parser\Node\TextNode;
use Phug\Parser\State;
use Phug\Parser\TokenHandlerInterface;

class InterpolationStartTokenHandler implements TokenHandlerInterface
{
    public function handleToken(TokenInterface $token, State $state)
    {
        if (!($token instanceof InterpolationStartToken)) {
            throw new \RuntimeException(
                'You can only pass interpolation start tokens to this token handler'
            );
        }

        $state->store();
        if (!($state->getLastNode() instanceof TextNode)) {
            $state->enter();
        }
    }
}
