<?php

namespace Phug\Parser\TokenHandler;

use Phug\Lexer\Token\TagInterpolationStartToken;
use Phug\Lexer\TokenInterface;
use Phug\Parser\Node\TextNode;
use Phug\Parser\State;
use Phug\Parser\TokenHandlerInterface;

class TagInterpolationStartTokenHandler implements TokenHandlerInterface
{
    public function handleToken(TokenInterface $token, State $state)
    {
        if (!($token instanceof TagInterpolationStartToken)) {
            throw new \RuntimeException(
                'You can only pass tag interpolation start tokens to this token handler'
            );
        }

        $state->store();
        $state->startInterpolation(!($state->getLastNode() instanceof TextNode));
    }
}
