<?php

namespace Phug;

use Phug\Lexer\TokenInterface;
use Phug\Parser\Dumper\Html;
use Phug\Parser\Dumper\Text;
use Phug\Parser\DumperInterface;
use Phug\Parser\Node;
use Phug\Lexer\Token\AssignmentToken;
use Phug\Lexer\Token\AttributeEndToken;
use Phug\Lexer\Token\AttributeStartToken;
use Phug\Lexer\Token\AttributeToken;
use Phug\Lexer\Token\BlockToken;
use Phug\Lexer\Token\CaseToken;
use Phug\Lexer\Token\ClassToken;
use Phug\Lexer\Token\CodeToken;
use Phug\Lexer\Token\CommentToken;
use Phug\Lexer\Token\ConditionalToken;
use Phug\Lexer\Token\DoToken;
use Phug\Lexer\Token\DoctypeToken;
use Phug\Lexer\Token\EachToken;
use Phug\Lexer\Token\ExpansionToken;
use Phug\Lexer\Token\ExpressionToken;
use Phug\Lexer\Token\FilterToken;
use Phug\Lexer\Token\ForToken;
use Phug\Lexer\Token\IdToken;
use Phug\Lexer\Token\ImportToken;
use Phug\Lexer\Token\IndentToken;
use Phug\Lexer\Token\MixinCallToken;
use Phug\Lexer\Token\MixinToken;
use Phug\Lexer\Token\NewLineToken;
use Phug\Lexer\Token\OutdentToken;
use Phug\Lexer\Token\TagToken;
use Phug\Lexer\Token\TextToken;
use Phug\Lexer\Token\VariableToken;
use Phug\Lexer\Token\WhenToken;
use Phug\Lexer\Token\WhileToken;
use Phug\Parser\Node\AssignmentNode;
use Phug\Parser\Node\AttributeNode;
use Phug\Parser\Node\BlockNode;
use Phug\Parser\Node\CaseNode;
use Phug\Parser\Node\CodeNode;
use Phug\Parser\Node\CommentNode;
use Phug\Parser\Node\ConditionalNode;
use Phug\Parser\Node\DoctypeNode;
use Phug\Parser\Node\DoNode;
use Phug\Parser\Node\EachNode;
use Phug\Parser\Node\ElementNode;
use Phug\Parser\Node\ExpressionNode;
use Phug\Parser\Node\FilterNode;
use Phug\Parser\Node\ForNode;
use Phug\Parser\Node\ImportNode;
use Phug\Parser\Node\MixinCallNode;
use Phug\Parser\Node\MixinNode;
use Phug\Parser\Node\TextNode;
use Phug\Parser\Node\VariableNode;
use Phug\Parser\Node\WhenNode;
use Phug\Parser\Node\WhileNode;
use Phug\Parser\NodeInterface;
use Phug\Parser\State;
use Phug\Parser\TokenHandler\AssignmentTokenHandler;
use Phug\Parser\TokenHandlerInterface;
use Phug\Util\OptionInterface;
use Phug\Util\Partial\OptionTrait;

/**
 * Takes tokens from the Lexer and creates an AST out of it.
 *
 * This class takes generated tokens from the Lexer sequentially
 * and produces an Abstract Syntax Tree (AST) out of it
 *
 * The AST is an object-tree containing Node-instances
 * with parent/child relations
 *
 * This AST is passed to the compiler to generate PHTML out of it
 *
 * Usage example:
 * <code>
 *
 *     use Phug\Parser;
 *
 *     $parser = new Parser();
 *     var_dump($parser->parse($pugInput));
 *
 * </code>
 */
class Parser implements OptionInterface
{
    use OptionTrait;

    /**
     * The lexer used in this parser instance.
     *
     * @var Lexer
     */
    private $lexer;

    /**
     * @var State
     */
    private $state;

    /**
     * @var callable[]
     */
    private $tokenHandlers;

    /**
     * Creates a new parser instance.
     *
     * The parser will run the provided input through the lexer
     * and generate an AST out of it.
     *
     * The AST will be an object-tree consisting of Phug\Parser\Node instances
     *
     * You can take the AST and either compile it with the Compiler or handle it yourself
     *
     * @param array|null $options the options array
     * @throws ParserException
     */
    public function __construct(array $options = null)
    {

        $this->options = array_replace_recursive([
            'lexer_class_name' => Lexer::class,
            'lexer_options' => [],
            'state_class_name' => State::class,
            'token_handlers' => [
                AssignmentToken::class => AssignmentTokenHandler::class,
                AttributeEndToken::class => [$this, 'handleAttributeEnd'],
                AttributeStartToken::class => [$this, 'handleAttributeStart'],
                AttributeToken::class => [$this, 'handleAttribute'],
                BlockToken::class => [$this, 'handleBlock'],
                CaseToken::class => [$this, 'handleCase'],
                ClassToken::class => [$this, 'handleClass'],
                CodeToken::class => [$this, 'handleCode'],
                CommentToken::class => [$this, 'handleComment'],
                ConditionalToken::class => [$this, 'handleConditional'],
                DoToken::class => [$this, 'handleDo'],
                DoctypeToken::class => [$this, 'handleDoctype'],
                EachToken::class => [$this, 'handleEach'],
                ExpansionToken::class => [$this, 'handleExpansion'],
                ExpressionToken::class => [$this, 'handleExpression'],
                FilterToken::class => [$this, 'handleFilter'],
                ForToken::class => [$this, 'handleFor'],
                IdToken::class => [$this, 'handleId'],
                ImportToken::class => [$this, 'handleImport'],
                IndentToken::class => [$this, 'handleIndent'],
                MixinCallToken::class => [$this, 'handleMixinCall'],
                MixinToken::class => [$this, 'handleMixin'],
                NewLineToken::class => [$this, 'handleNewLine'],
                OutdentToken::class => [$this, 'handleOutdent'],
                TagToken::class => [$this, 'handleTag'],
                TextToken::class => [$this, 'handleText'],
                VariableToken::class => [$this, 'handleVariable'],
                WhenToken::class => [$this, 'handleWhen'],
                WhileToken::class => [$this, 'handleWhile']
            ]
        ], $options ?: []);

        $lexerClassName = $this->options['lexer_class_name'];
        if (!is_a($lexerClassName, Lexer::class))
            throw new ParserException(
                "Passed lexer class $lexerClassName is ".
                "not a valid ".Lexer::class
            );

        $this->lexer = new $lexerClassName($this->options['lexer_options']);
        $this->state = null;
        $this->tokenHandlers = [];

        foreach ($this->options['token_handlers'] as $className => $handler)
            $this->setTokenHandler($className, $handler);
    }

    /**
     * Returns the currently used Lexer instance.
     *
     * @return Lexer
     */
    public function getLexer()
    {

        return $this->lexer;
    }

    public function setTokenHandler($className, $handler)
    {

        if (!is_subclass_of($handler, TokenHandlerInterface::class))
            throw new \InvalidArgumentException(
                "Passed token handler needs to implement ".TokenHandlerInterface::class
            );

        $this->tokenHandlers[$className] = $handler;

        return $this;
    }

    /**
     * Parses the provided input-string to an AST.
     *
     * The Abstract Syntax Tree (AST) will be an object-tree consisting
     * of \Phug\Parser\Node instances.
     *
     * You can either let the compiler compile it or compile it yourself
     *
     * The root-node will always be of type 'document',
     * from there on it can contain several kinds of nodes
     *
     * @param string $input the input jade string that is to be parsed
     *
     * @return Node the root-node of the parsed AST
     */
    public function parse($input)
    {

        $stateClassName = $this->options['state_class_name'];
        if (!is_a($stateClassName, State::class, true))
            throw new \InvalidArgumentException(
                'state_class_name needs to be a valid '.State::class.' sub class'
            );

        $this->state = new $stateClassName(
            $this->lexer->lex($input)
        );

        //While we have tokens, handle current token, then go to next token
        //rinse and repeat
        while ($this->state->hasTokens()) {

            $this->state->handleToken();
            $this->state->nextToken();
        }

        $document = $this->state->getDocumentNode();

        //Some work after parsing needed
        //Resolve expansions/outer nodes
        $expandingNodes = $document->find(function(NodeInterface $node) {

            return $node->getOuterNode() !== null;
        });

        foreach ($expandingNodes as $expandingNode) {

            $current = $expandingNode;
            while ($outerNode = $expandingNode->getOuterNode()) {

                /** @var NodeInterface $expandedNode */
                $expandedNode = $outerNode;
                $current->setOuterNode(null);
                $current->prepend($expandedNode);
                $current->remove();
                $expandedNode->appendChild($current);
                $current = $expandedNode;
            }
        }

        $this->state = null;

        //Return the final document node with all its awesome child nodes
        return $document;
    }

    /**
     * Parses an <attribute>-token into an attribute-node.
     *
     * That node is appended to the $_current element.
     *
     * If no $_current element exists, a new one is created
     *
     * Attributes in elements and mixins always need a valid name
     *
     * @param AttributeToken $token the <attribute>-token
     * @param State $state the parser state
     *
     * @throws ParserException
     */
    protected function handleAttribute(AttributeToken $token, State $state)
    {

        if (!$state->getCurrentNode())
            $state->setCurrentNode($state->createNode(ElementNode::class, $token));

        /** @var AttributeNode $node */
        $node = $state->createNode(AttributeNode::class, $token);
        $name = $node->getName();
        $value = $node->getValue();
        $node->setName($name);
        $node->setValue($value);
        $node->setIsEscaped($token->isEscaped());
        $node->setIsChecked($token->isChecked());

        if ($state->currentNodeIs([MixinCallNode::class]) && ($value === '' || $value === null)) {

            $node->setValue($name);
            $node->setName(null);
        }

        /** @var ElementNode|MixinCallNode $current */
        $current = $state->getCurrentNode();
        $current->getAttributes()->appendChild($node);
    }

    /**
     * Handles an <attributeStart>-token.
     *
     * Attributes can only start on elements, assignments, imports, mixins and mixinCalls
     *
     * After that, all following <attribute>-tokens are handled.
     * After that, an <attributeEnd>-token is expected
     * (When I think about it, the Lexer kind of does that already)
     *
     * @param AttributeStartToken $token the <attributeStart>-token
     * @param State $state the parser state
     *
     * @throws ParserException
     */
    protected function handleAttributeStart(AttributeStartToken $token, State $state)
    {

        if (!$state->getCurrentNode())
            $state->setCurrentNode($state->createNode(ElementNode::class, $token));

        if (!$state->currentNodeIs([
            ElementNode::class, AssignmentNode::class,
            ImportNode::class, VariableNode::class,
            MixinNode::class, MixinCallNode::class
        ]))
            $state->throwException(
                "Attributes can only be placed on element, assignment, "
                ."import, variable, mixin and mixinCall",
                $token
            );

        foreach ($state->lookUpNext([AttributeToken::class]) as $subToken) {

            $this->handle($subToken);
        }

        if (!$state->expect([AttributeEndToken::class]))
            $state->throwException(
                "Attribute list not closed",
                $token
            );
    }

    /**
     * Handles an <attributeEnd>-token.
     *
     * It does nothing (right now?)
     *
     * @param AttributeEndToken $token the <attributeEnd>-token
     * @param State $state the parser state
     */
    protected function handleAttributeEnd(AttributeEndToken $token, State $state)
    {
        //Nothing to do here.
    }

    /**
     * Handles a <block>-token and parses it into a block-node.
     *
     * Blocks outside a mixin always need a name! (That's what $_inMixin is for)
     *
     * @param BlockToken $token the <block>-token
     * @param State $state the parser state
     *
     * @throws ParserException
     */
    protected function handleBlock(BlockToken $token, State $state)
    {

        /** @var BlockNode $node */
        $node = $state->createNode(BlockNode::class, $token);
        $node->setName($token->getName());
        $node->setMode($token->getMode());
        $state->setCurrentNode($node);
    }

    /**
     * Handles a <class>-token and parses it into an element.
     *
     * If there's no $_current-node, a new one is created
     *
     * It will be converted to a regular <attribute>-node on the element
     * (There is no class-node)
     *
     * Classes can only exist on elements and mixinCalls
     *
     * @param ClassToken $token the <class>-token
     * @param State $state the parser state
     *
     * @throws ParserException
     */
    protected function handleClass(ClassToken $token, State $state)
    {

        if (!$state->getCurrentNode())
            $state->setCurrentNode($state->createNode(ElementNode::class, $token));

        if (!$state->currentNodeIs([ElementNode::class, MixinCallNode::class]))
            $state->throwException(
                "Classes can only be used on elements and mixin calls",
                $token
            );

        //We actually create a fake class attribute
        /** @var AttributeNode $attr */
        $attr = $state->createNode(AttributeNode::class, $token);
        $attr->setName('class');
        $attr->setValue($token->getName());
        $attr->unescape()->uncheck();

        /** @var ElementNode|MixinCallNode $current */
        $current = $state->getCurrentNode();
        $current->getAttributes()->appendChild($attr);
    }

    /**
     * Handles a <comment>-token and parses it into a comment-node.
     *
     * The comment node is set as the $_current element
     *
     * @param CommentToken $token the <comment>-token
     * @param State $state the parser state
     */
    protected function handleComment(CommentToken $token, State $state)
    {

        /** @var CommentNode $node */
        $node = $state->createNode(CommentNode::class, $token);
        $node->setIsVisible($token->isVisible());
        $state->setCurrentNode($node);
    }

    /**
     * Handles a <case>-token and parses it into a case-node.
     *
     * @param CaseToken $token the <case>-token
     * @param State $state the parser state
     */
    protected function handleCase(CaseToken $token, State $state)
    {

        /** @var CaseNode $node */
        $node = $state->createNode(CaseNode::class, $token);
        $node->setSubject($token->getSubject());
        $state->setCurrentNode($node);
    }

    /**
     * Handles a <conditional>-token and parses it into a conditional-node.
     *
     * @param ConditionalToken $token the <conditional>-token
     * @param State $state the parser state
     */
    protected function handleConditional(ConditionalToken $token, State $state)
    {

        /** @var ConditionalNode $node */
        $node = $state->createNode(ConditionalNode::class, $token);
        $node->setSubject($token->getSubject());
        $node->setName($token->getName());
        $state->setCurrentNode($node);
    }

    /**
     * Handles a <do>-token and parses it into a do-node.
     *
     * @param DoToken $token the <do>-token
     * @param State $state the parser state
     */
    protected function handleDo(DoToken $token, State $state)
    {

        $node = $state->createNode(DoNode::class, $token);
        $state->setCurrentNode($node);
    }

    /**
     * Handles a <doctype>-token and parses it into a doctype-node.
     *
     * @param DoctypeToken $token the <doctype>-token
     * @param State $state the parser state
     */
    protected function handleDoctype(DoctypeToken $token, State $state)
    {

        /** @var DoctypeNode $node */
        $node = $state->createNode(DoctypeNode::class, $token);
        $node->setName($token->getName());
        $state->setCurrentNode($node);
    }

    /**
     * Handles an <each>-token and parses it into an each-node.
     *
     * @param EachToken $token the <each>-token
     * @param State $state the parser state
     */
    protected function handleEach(EachToken $token, State $state)
    {

        /** @var EachNode $node */
        $node = $state->createNode(EachNode::class, $token);
        $node->setSubject($token->getSubject());
        $node->setItem($token->getItem());
        $node->setKey($token->getKey());
        $state->setCurrentNode($node);
    }

    /**
     * Handles an <expression>-token into an expression-node.
     *
     * If there's a $_current-element, the expression gets appended
     * to the $_current-element. If not, the expression itself
     * becomes the $_current element
     *
     * @param ExpressionToken $token the <expression>-token
     * @param State $state the parser state
     *
     * @throws ParserException
     */
    protected function handleExpression(ExpressionToken $token, State $state)
    {

        /** @var ExpressionNode $node */
        $node = $state->createNode(ExpressionNode::class, $token);
        $node->setIsEscaped($token->isEscaped());
        $node->setIsChecked($token->isChecked());
        $node->setValue($token->getValue());

        if ($state->getCurrentNode())
            $state->getCurrentNode()->appendChild($node);
        else
            $state->setCurrentNode($node);
    }

    /**
     * Handles an <code>-token into an code-node.
     *
     * @param CodeToken $token the <code>-token
     * @param State $state the parser state
     *
     * @throws ParserException
     */
    protected function handleCode(CodeToken $token, State $state)
    {

        /** @var CodeNode $node */
        $node = $state->createNode(CodeNode::class, $token);
        $node->setValue($token->getValue());
        $node->setIsBlock($token->isBlock());
        $state->setCurrentNode($node);
    }

    /**
     * Handles a <filter>-token and parses it into a filter-node.
     *
     * @param FilterToken $token the <filter>-token
     * @param State $state the parser state
     */
    protected function handleFilter(FilterToken $token, State $state)
    {

        /** @var FilterNode $node */
        $node = $state->createNode(FilterNode::class, $token);
        $node->setName($token->getName());
        $state->setCurrentNode($node);
    }

    /**
     * Handles an <id>-token and parses it into an element.
     *
     * If no $_current element exists, a new one is created
     *
     * IDs can only exist on elements an mixin calls
     *
     * They will get converted to attribute-nodes and appended to the current element
     *
     * @param IdToken $token the <id>-token
     * @param State $state the parser state
     *
     * @throws ParserException
     */
    protected function handleId(IdToken $token, State $state)
    {

        if (!$state->getCurrentNode())
            $state->setCurrentNode($state->createNode(ElementNode::class));

        if (!$state->currentNodeIs([ElementNode::class, MixinCallNode::class]))
            $state->throwException(
                'IDs can only be used on elements and mixin calls',
                $token
            );

        /** @var AttributeNode $attr */
        $attr = $state->createNode(AttributeNode::class, $token);
        $attr->setName('id');
        $attr->setValue($token->getName());
        $attr->unescape()->uncheck();

        /** @var ElementNode|MixinCallNode $current */
        $current = $state->getCurrentNode();
        $current->getAttributes()->appendChild($attr);
    }

    /**
     * Handles a <variable>-token and parses it into a variable assignment.
     *
     * @param VariableToken $token the <variable>-token
     * @param State $state the parser state
     *
     * @throws ParserException
     */
    protected function handleVariable(VariableToken $token, State $state)
    {

        /** @var VariableNode $node */
        $node = $state->createNode(VariableNode::class);
        $node->setName($token->getName());
        $state->setCurrentNode($node);
    }

    /**
     * Handles an <import>-token and parses it into an import-node.
     *
     * Notice that "extends" and "include" are basically the same thing.
     * The only difference is that "extends" can only exist at the very
     * beginning of a jade-block
     *
     * Only "include" can have filters, though.
     * This gets checked in the Compiler, not here
     *
     * @param ImportToken $token the <import>-token
     * @param State $state the parser state
     *
     * @throws ParserException
     */
    protected function handleImport(ImportToken $token, State $state)
    {

        if ($token->getName() === 'extends' && $state->getDocumentNode()->hasChildren())
            $state->throwException(
                "extends should be the very first statement in a document",
                $token
            );

        /** @var ImportNode $node */
        $node = $state->createNode(ImportNode::class, $token);
        $node->setName($token->getName());
        $node->setPath($token->getPath());
        $node->setFilter($token->getFilter());
        $state->setCurrentNode($node);
    }

    /**
     * Handles an <indent>-token.
     *
     * The $_level will be increased by 1 for each <indent>
     *
     * If there's no $_last element (which is set on a newLine), we do nothing
     * (because there's nothing to indent into)
     *
     * The $_last node is set as the $_currentParent node and acts as a parent-node
     * for further created nodes (They get appended in handleNewLine)
     *
     * import-nodes can't be indented into, because they can't have children (poor imports :'( )
     *
     * The opposite of this is, obviously, handleOutdent with <outdent>-tokens
     *
     * @param IndentToken $token the <indent>-token
     * @param State $state the parser state
     *
     * @throws ParserException
     */
    protected function handleIndent(IndentToken $token, State $state)
    {

        $state->enter();
    }

    /**
     * Handles a <tag>-token and parses it into a tag-node.
     *
     * If no $_current element exists, a new one is created
     * A tag can only exist once on an element
     * Only elements can have tags
     *
     * @param TagToken $token the <tag>-token
     * @param State $state the parser state
     *
     * @throws ParserException
     */
    protected function handleTag(TagToken $token, State $state)
    {

        if (!$state->getCurrentNode())
            $state->setCurrentNode($state->createNode(ElementNode::class, $token));

        if (!$state->currentNodeIs([ElementNode::class]))
            $state->throwException(
                'Tags can only be used on elements',
                $token
            );

        /** @var ElementNode $current */
        $current = $state->getCurrentNode();

        if ($current->getName())
            $state->throwException(
                'The element already has a tag name',
                $token
            );

        $current->setName($token->getName());
    }

    /**
     * Handles a <mixin>-token and parses it into a mixin-node.
     *
     * Mixins can't be inside other mixins.
     * We use $_inMixin and $_mixinLevel for that
     * $_mixinLevel gets reset in handleOutdent
     *
     * @param MixinToken $token the <mixin>-token
     * @param State $state the parser state
     *
     * @throws ParserException
     */
    protected function handleMixin(MixinToken $token, State $state)
    {

        /** @var MixinNode $node */
        $node = $state->createNode(MixinNode::class, $token);
        $node->setName($token->getName());
        $state->setCurrentNode($node);
    }

    /**
     * Handles a <mixinCall>-token and parses it into a mixinCall-node.
     *
     * @param MixinCallToken $token the <mixinCall>-token
     * @param State $state the parser state
     */
    protected function handleMixinCall(MixinCallToken $token, State $state)
    {

        /** @var MixinCallNode $node */
        $node = $state->createNode(MixinCallNode::class, $token);
        $node->setName($token->getName());
        $state->setCurrentNode($node);
    }

    /**
     * Handles a <newLine>-token.
     *
     * If there's no $_current element, it does nothing
     * If there is one it:
     *
     * 1. Checks if we have an $_expansion. If we do, append it to $_current and reset $_expansion
     * 2. Appends the $_current element to the $_currentParent
     * 3. Set's the $_last element to the $_current element
     * 4. Resets $_current to null
     *
     * @param NewLineToken $token the <newLine>-token or null
     * @param State $state the parser state
     */
    protected function handleNewLine(NewLineToken $token, State $state)
    {

        $state->store();
    }

    /**
     * Handles an <outdent>-token.
     *
     * Decreases the current $_level by 1
     *
     * Sets the $_currentParent to the ->parent of $_currentParent
     * (Walking up the tree by 1)
     *
     * If we're in a mixin and we're at or below our mixin-level again,
     * we're not in a mixin anymore
     *
     * @param OutdentToken $token the <outdent>-token
     * @param State $state the parser state
     */
    protected function handleOutdent(OutdentToken $token, State $state)
    {

        $state->leave();
    }

    /**
     * Handles an <expansion>-token.
     *
     * If there's no current element, we don't expand anything and throw an ParserException
     *
     * If there's no space behind the : and the next token is a <tag>-token,
     * we don't treat this as an expansion, but rather as a tag-extension
     * (a:b === <a:b></a:b>, a: b === <a><b></b></a>)
     * This is important for XML and XML-namespaces
     *
     * Notice that, right now, any element that can also land in $_current can be expanded
     * (so almost all elements there are)
     * It just makes no sense for some elements ("extends", "include")
     *
     * $_current is reset after the expansion so that we can collect the expanding element
     * and handle it on a newLine or in an indent
     *
     * @param ExpansionToken $token the <expansion>-token
     * @param State $state the parser state
     *
     * @throws ParserException
     */
    protected function handleExpansion(ExpansionToken $token, State $state)
    {

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


    /**
     * Handles a <text>-token and parses it into a text-node.
     *
     * If there's a $_current element, we append it to that element,
     * if not, it becomes the $_current element
     *
     * @param TextToken $token the <text>-token
     * @param State $state the parser state
     */
    protected function handleText(TextToken $token, State $state)
    {

        /** @var TextNode $node */
        $node = $state->createNode(TextNode::class, $token);
        $node->setValue($token->getValue());
        $node->setLevel($token->getLevel());
        $node->setIsEscaped($token->isEscaped());

        if ($state->getCurrentNode()) {

            $state->getCurrentNode()->appendChild($node);
        } else
            $state->setCurrentNode($node);
    }

    /**
     * Handles a <when>-token and parses it into a when-node.
     *
     * @param WhenToken $token the <when>-token
     * @param State $state the parser state
     */
    protected function handleWhen(WhenToken $token, State $state)
    {

        /** @var WhenNode $node */
        $node = $state->createNode(WhenNode::class, $token);
        $node->setSubject($token->getSubject());
        $node->setName($token->getName());
        $state->setCurrentNode($node);
    }

    /**
     * Handles a <while>-token and parses it into a while-node.
     *
     * @param WhileToken $token the <while>-token
     * @param State $state the parser state
     */
    protected function handleWhile(WhileToken $token, State $state)
    {

        /** @var WhileNode $node */
        $node = $state->createNode(WhileNode::class, $token);
        $node->setSubject($token->getSubject());
        $state->setCurrentNode($node);
    }


    /**
     * Handles a <for>-token and parses it into a for-node.
     *
     * @param ForToken $token the <while>-token
     * @param State $state the parser state
     */
    protected function handleFor(ForToken $token, State $state)
    {

        /** @var ForNode $node */
        $node = $state->createNode(ForNode::class, $token);
        $node->setSubject($token->getSubject());
        $state->setCurrentNode($node);
    }

    protected function dumpNode(NodeInterface $node, $level = null)
    {

        $level = $level ?: 0;
        $text = '';
        switch (get_class($node)) {
            default:

                $text = $this->getNodeName($node);

                if ($outerNode = $node->getOuterNode())
                    $text .= ' outer='.get_class($outerNode);

                break;
        }

        $text = str_repeat('  ', $level)."[$text]";

        if (count($node) > 0) {

            foreach ($node as $child)
                $text .= "\n".$this->dumpNode($child, $level + 1);
        }

        return $text;
    }
}