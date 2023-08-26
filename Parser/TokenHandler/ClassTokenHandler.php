<?php

namespace Phug\Parser\TokenHandler;

use Phug\Lexer\Token\ClassToken;
use Phug\Parser\Node\AttributeNode;
use Phug\Parser\Node\ElementNode;
use Phug\Parser\Node\MixinCallNode;
use Phug\Parser\State;

class ClassTokenHandler extends AbstractTokenHandler
{
    const TOKEN_TYPE = ClassToken::class;

    public function handleClassToken(ClassToken $token, State $state)
    {
        $this->createElementNodeIfMissing($token, $state);
        $this->assertCurrentNodeIs($token, $state, [ElementNode::class, MixinCallNode::class]);

        //We actually create a fake class attribute
        /** @var AttributeNode $attr */
        $attr = $state->createNode(AttributeNode::class, $token);
        $attr->setName('class');
        $attr->setValue(var_export($token->getName(), true));
        $attr->unescape()->uncheck();

        /** @var ElementNode|MixinCallNode $current */
        $current = $state->getCurrentNode();
        $current->getAttributes()->attach($attr);
    }
}
