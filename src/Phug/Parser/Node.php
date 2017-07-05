<?php

namespace Phug\Parser;

use Phug\Ast\Node as AstNode;
use Phug\Lexer\TokenInterface;

/**
 * Represents a node in the AST the parser generates.
 *
 * A node has children and always tries to reference its parents
 *
 * It also has some utility methods to work with those nodes
 */
class Node extends AstNode implements NodeInterface
{
    private $file;
    private $line;
    private $offset;
    private $level;
    private $outerNode;
    private $token;

    /**
     * Creates a new, detached node without children or a parent.
     *
     * It can be appended to any node after that
     *
     * @param int|null        $line     the line at which we found this node
     * @param int|null        $offset   the offset in a line we found this node at
     * @param int|null        $level    the level of indentation this node is at
     * @param NodeInterface   $parent   the parent of this node
     * @param NodeInterface[] $children the children of this node
     * @param TokenInterface  $token    the token that created the node
     */
    public function __construct(
        $line = null,
        $offset = null,
        $level = null,
        NodeInterface $parent = null,
        array $children = null,
        TokenInterface $token = null,
        $file = null
    ) {
        parent::__construct($parent, $children);

        $this->line = $line ?: 0;
        $this->offset = $offset ?: 0;
        $this->level = $level ?: 0;
        $this->outerNode = null;
        $this->token = $token;
        $this->file = $file;
    }

    /**
     * @return int
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @return int
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @return int
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * @return NodeInterface
     */
    public function getOuterNode()
    {
        return $this->outerNode;
    }

    /**
     * @return TokenInterface
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return string|null
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param NodeInterface $node
     *
     * @return $this
     */
    public function setOuterNode(NodeInterface $node = null)
    {
        $this->outerNode = $node;

        return $this;
    }
}
