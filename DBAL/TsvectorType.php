<?php
namespace Intaro\PostgresSearchBundle\DBAL;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class TsvectorType extends Type
{
    public function getName()
    {
        return 'tsvector';
    }

    public function canRequireSQLConversion()
    {
        return true;
    }

    public function getSqlDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return "TSVECTOR";
    }

    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        return $value;
    }

    public function convertToDatabaseValueSQL($sqlExpr, AbstractPlatform $platform)
    {
        return sprintf('to_tsvector(%s)', $sqlExpr);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        $result = '';

        if (is_array($value)) {
            foreach ($value as $item) {
                if (is_array($item)) {
                    $item = implode(' ', $item);
                }
                $result .= $item . ' ';
            }
        }
        $result = trim($result);

        return $result;
    }
}
