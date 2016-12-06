<?php

namespace Phug\Parser\TokenHandler;

use Phug\Lexer\Token\IndentToken;
use Phug\Parser\State;
use Phug\Parser\TokenHandlerInterface;
use Phug\Lexer\TokenInterface;

class IndentTokenHandler implements TokenHandlerInterface
{

    public function handleToken(TokenInterface $token, State $state)
    {

        if (!($token instanceof IndentToken)) {
            throw new \RuntimeException(
                "You can only pass indent tokens to this token handler"
            );
        }

        $state->enter();
    }
}
