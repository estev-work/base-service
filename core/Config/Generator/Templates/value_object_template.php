<?php
return <<<PHP
<?php

declare(strict_types=1);

namespace {namespace};
{uses}

/**
 * @generated by yaml file {yamlFileName}.yaml
 */
class {className}
{
    {properties}

    public function __construct(array \$data)
    {
        {constructorAssignments}    
    }

    {getters}
}
PHP;
