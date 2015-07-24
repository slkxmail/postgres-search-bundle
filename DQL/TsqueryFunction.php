<?php
namespace Intaro\PostgresSearchBundle\DQL;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;

/**
 * TsqueryFunction ::= "TSQUERY" "(" StringPrimary "," StringPrimary "," StringPrimary ")"
 */
class TsqueryFunction extends FunctionNode
{
    public $fieldName = null;
    public $queryString = null;
    public $regconfig = null;

    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->fieldName = $parser->StringPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->queryString = $parser->StringPrimary();

        if ($parser->getLexer()->lookahead['type'] == Lexer::T_COMMA) {
            $parser->match(Lexer::T_COMMA);
            $this->regconfig = $parser->StringPrimary();
        }

        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        if (!is_null($this->regconfig)) {
            $result = $this->fieldName->dispatch($sqlWalker)
                . ' @@ to_tsquery('
                . $this->regconfig->dispatch($sqlWalker) . ', '
                . $this->queryString->dispatch($sqlWalker) . ')';
        } else {
            $result = $this->fieldName->dispatch($sqlWalker)
                . ' @@ to_tsquery(' . $this->queryString->dispatch($sqlWalker) . ')';
        }

        return $result;
    }
}
