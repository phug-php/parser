<?php

namespace Phug\Parser\TokenHandler;

use Phug\Lexer\Token\NewLineToken;
use Phug\Parser\State;
use Phug\Parser\TokenHandlerInterface;
use Phug\Lexer\TokenInterface;

class NewLineTokenHandler implements TokenHandlerInterface
{

    public function handleToken(TokenInterface $token, State $state)
    {

        if (!($token instanceof NewLineToken))
            throw new \RuntimeException(
                "You can only pass newline tokens to this token handler"
            );

        $state->store();
    }
}