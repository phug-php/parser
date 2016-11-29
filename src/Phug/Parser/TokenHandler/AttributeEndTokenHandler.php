<?php

namespace Phug\Parser\TokenHandler;

use Phug\Lexer\Token\AttributeEndToken;
use Phug\Lexer\Token\AttributeStartToken;
use Phug\Lexer\Token\AttributeToken;
use Phug\Parser\Node\AssignmentNode;
use Phug\Parser\Node\ElementNode;
use Phug\Parser\Node\ImportNode;
use Phug\Parser\Node\MixinCallNode;
use Phug\Parser\Node\MixinNode;
use Phug\Parser\Node\VariableNode;
use Phug\Parser\State;
use Phug\Parser\TokenHandlerInterface;
use Phug\Lexer\TokenInterface;

class AttributeEndTokenHandler implements TokenHandlerInterface
{

    public function handleToken(TokenInterface $token, State $state)
    {

        if (!($token instanceof AttributeEndToken))
            throw new \RuntimeException(
                "You can only pass attribute end tokens to this token handler"
            );
    }
}