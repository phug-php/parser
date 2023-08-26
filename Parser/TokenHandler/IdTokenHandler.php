<?php

namespace Phug\Parser\TokenHandler;

use Phug\Lexer\Token\IdToken;
use Phug\Parser\Node\AttributeNode;
use Phug\Parser\Node\ElementNode;
use Phug\Parser\Node\MixinCallNode;
use Phug\Parser\State;

class IdTokenHandler extends AbstractTokenHandler
{
    const TOKEN_TYPE = IdToken::class;

    public function handleIdToken(IdToken $token, State $state)
    {
        $this->onlyOnElement($token, $state);

        /** @var AttributeNode $attr */
        $attr = $state->createNode(AttributeNode::class, $token);
        $attr->setName('id');
        $attr->setValue(var_export($token->getName(), true));
        $attr->unescape()->uncheck();

        /** @var ElementNode|MixinCallNode $current */
        $current = $state->getCurrentNode();
        $current->getAttributes()->attach($attr);
    }
}
