<?php

namespace Phug\Parser\TokenHandler;

use Phug\Lexer\Token\ExpansionToken;
use Phug\Lexer\Token\TagToken;
use Phug\Parser\Node\ElementNode;
use Phug\Parser\State;
use Phug\Parser\TokenHandlerInterface;
use Phug\Lexer\TokenInterface;

class ExpansionTokenHandler implements TokenHandlerInterface
{

    public function handleToken(TokenInterface $token, State $state)
    {

        if (!($token instanceof ExpansionToken))
            throw new \RuntimeException(
                "You can only pass expansion tokens to this token handler"
            );

        if (!$state->getCurrentNode())
            $state->throwException(
                "Expansion needs an element to work on",
                $token
            );

        if (!$state->currentNodeIs([ElementNode::class]) && !$token->hasSpace()) {

            if (!$state->expectNext([TagToken::class])) {
                $state->throwException(
                    sprintf(
                        "Expected tag name or expansion after double colon, "
                        ."%s received",
                        basename(get_class($state->getToken()), 'Token')
                    ),
                    $token
                );
            }

            /** @var TagToken $token */
            $token = $state->getToken();
            /** @var ElementNode $current */
            $current = $state->getCurrentNode();
            $current->setName($current->getName().':'.$token->getName());

            return;
        }

        //Make sure to keep the expansion saved
        if ($state->getOuterNode())
            $state->getCurrentNode()->setOuterNode($state->getOuterNode());

        $state->setOuterNode($state->getCurrentNode());
        $state->setCurrentNode(null);
    }
}