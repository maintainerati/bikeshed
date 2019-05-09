<?php

declare(strict_types=1);

namespace Maintainerati\Bikeshed\Doctrine\DQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\SimpleArithmeticExpression;
use Doctrine\ORM\Query\Lexer;
use Doctrine\ORM\Query\SqlWalker;

final class Random extends FunctionNode
{
    /** @var SimpleArithmeticExpression */
    private $expression;

    public function getSql(SqlWalker $sqlWalker)
    {
        if ($this->expression) {
            return 'RANDOM(' . $this->expression->dispatch($sqlWalker) . ')';
        }

        return 'RANDOM()';
    }

    public function parse(\Doctrine\ORM\Query\Parser $parser): void
    {
        $lexer = $parser->getLexer();
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);

        if ($lexer->lookahead['type'] !== Lexer::T_CLOSE_PARENTHESIS) {
            $this->expression = $parser->SimpleArithmeticExpression();
        }

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }
}
