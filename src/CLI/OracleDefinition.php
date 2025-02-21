<?php

declare(strict_types=1);

namespace Oracle\CLI;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\ProcessDefinition;

abstract class OracleDefinition extends ProcessDefinition
{
    /**
     * @param string $command
     * @param string $workDir
     * @param array<string,string> $env
     * @param array<string,string> $descriptions
     */
    public function __construct(
        string $command = '',
        string $workDir = '',
        array $env = [],
        private array $descriptions = []
    ) {
        $this->descriptions = $descriptions + [
            'USER' => 'Specifies the database account username',
            'PASS' => 'Specifies the database account password',
            'CONN' => 'Specifies the database connect identifier',
            'SYS' => 'Database administration privileges'
        ];

        parent::__construct($command, $workDir, $env);
    }


    /**
     * @return array<string,string>
     */
    public function getDescriptions(): array
    {
        return $this->descriptions;
    }


    /**
     * @param Command $command
     * @return Command
     */
    public function configure(Command $command): Command
    {
        $desc = $this->getDescriptions();
        return $command
            ->addOption('USER', null, InputOption::VALUE_REQUIRED, $desc['USER'])
            ->addOption('PASS', null, InputOption::VALUE_REQUIRED, $desc['PASS'])
            ->addOption('CONN', null, InputOption::VALUE_REQUIRED, $desc['CONN'])
            ->addOption('SYS', null, InputOption::VALUE_REQUIRED, $desc['SYS']);
    }


    /**
     * @param InputInterface $input
     * @return string[]
     */
    public function getArgs(InputInterface $input): array
    {
        $opts = $input->getOptions();
        $connectIdentifier = is_string($opts['CONN']) ? $opts['CONN'] : null;
        $asSys = is_string($opts['SYS']) ? $opts['SYS'] : null;

        $logon = sprintf(
            "%s/%s%s",
            is_string($opts['USER']) ? $opts['USER'] : "",
            is_string($opts['PASS']) ? $opts['PASS'] : "",
            is_string($connectIdentifier)
                ? (str_starts_with($connectIdentifier, "@") ? "" : "@") . $connectIdentifier
                : ""
        );
        $asSys = is_string($asSys) ? " " . (str_starts_with($asSys, "AS ") ? "" : "AS ") : '';

        return [$logon . $asSys];
    }
}
