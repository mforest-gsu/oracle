<?php

declare(strict_types=1);

namespace Oracle\CLI;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Process\ProcessCommand;

#[AsCommand('oracle:sqlldr')]
class SqlLoaderCommand extends ProcessCommand
{
    /**
     * @param SqlPlusDefinition $processDefinition
     * @param string|null $name
     */
    public function __construct(
        SqlPlusDefinition $processDefinition,
        string|null $name = null
    ) {
        parent::__construct($processDefinition, $name);
    }
}
