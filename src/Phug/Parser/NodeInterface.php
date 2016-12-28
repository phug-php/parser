<?php

namespace Phug\Parser;

use Phug\Ast\NodeInterface as AstNodeInterface;

interface NodeInterface extends AstNodeInterface
{
    public function getLine();

    public function getOffset();

    public function getLevel();

    public function getOuterNode();

    public function setOuterNode(NodeInterface $node);
}
