<?php


namespace Mlym\CodeGeneration\Unity;


use EasySwoole\ORM\Utility\Schema\Table;

class Unity
{
    static function getNamespacePath($rootPath, $namespace)
    {
        $composerJson = json_decode(file_get_contents($rootPath . '/composer.json'), true);
        if (isset($composerJson['autoload']['psr-4']["{$namespace}\\"])) {
            return $composerJson['autoload']['psr-4']["{$namespace}\\"];
        }
        if (isset($composerJson['autoload-dev']['psr-4']["{$namespace}\\"])) {
            return $composerJson['autoload-dev']['psr-4']["{$namespace}\\"];
        }
        return "$namespace/";
    }


    static function convertDbTypeToDocType($fieldType)
    {
        if (in_array($fieldType, ['tinyint', 'smallint', 'mediumint', 'int', 'bigint'])) {
            $newFieldType = 'int';
        } elseif (in_array($fieldType, ['float', 'double', 'real', 'decimal', 'numeric'])) {
            $newFieldType = 'float';
        } elseif (in_array($fieldType, ['char', 'varchar', 'text'])) {
            $newFieldType = 'string';
        } else {
            $newFieldType = 'mixed';
        }
        return $newFieldType;
    }

    static function chunkTableColumn(Table $table, callable $callback)
    {
        foreach ($table->getColumns() as $column) {
            $columnName = $column->getColumnName();
            $result = $callback($column, $columnName);
            if ($result === true) {
                break;
            }
        }
    }

    static function getModelName($modelClass)
    {
        $modelNameArr = (explode('\\', $modelClass));
        $modelName = end($modelNameArr);
        return $modelName;
    }
}
