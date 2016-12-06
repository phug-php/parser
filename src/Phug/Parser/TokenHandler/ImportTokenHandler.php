<?php

namespace Phug\Parser\TokenHandler;

use Phug\Lexer\Token\ImportToken;
use Phug\Lexer\Token\VariableToken;
use Phug\Parser\Node\ImportNode;
use Phug\Parser\Node\VariableNode;
use Phug\Parser\State;
use Phug\Parser\TokenHandlerInterface;
use Phug\Lexer\TokenInterface;

class ImportTokenHandler implements TokenHandlerInterface
{

    public function handleToken(TokenInterface $token, State $state)
    {

        if (!($token instanceof ImportToken)) {
            throw new \RuntimeException(
                "You can only pass import tokens to this token handler"
            );
        }

        if ($token->getName() === 'extends' && $state->getDocumentNode()->hasChildren()) {
            $state->throwException(
                "extends should be the very first statement in a document",
                $token
            );
        }

        /** @var ImportNode $node */
        $node = $state->createNode(ImportNode::class, $token);
        $node->setName($token->getName());
        $node->setPath($token->getPath());
        $node->setFilter($token->getFilter());
        $state->setCurrentNode($node);
    }
}
