<?php

declare(strict_types=1);

namespace Oracle\CLI;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class SqlLoaderDefinition extends OracleDefinition
{
    /**
     * @param string $workDir
     * @param array<string,string> $env
     */
    public function __construct(
        string $workDir = '',
        array $env = [],
    ) {
        $descriptions = [
            'BAD' => 'Bad file name',
            'CONTROL' => 'Control file name',
            'DATA' => 'Data file name',
            'DIRECT' => 'Use direct path',
            'DISCARD' => 'Discard file name',
            'ERRORS' => 'Number of errors to allow',
            'LOAD' => 'Number of logical records to load',
            'LOG' => 'Log file name',
            'MULTITHREADING' => 'Use multithreading in direct path',
            'PARALLEL' => 'Do parallel load',
            'PARFILE' => 'Parameter file: name of file that contains parameter specifications',
            'SILENT' => 'Suppress messages during run (header,feedback,errors,discards,partitions)',
            'SKIP' => 'Number of logical records to skip',
        ];

        parent::__construct('sqlldr', $workDir, $env, $descriptions);
    }


    /**
     * @param Command $command
     * @return Command
     */
    public function configure(Command $command): Command
    {
        $desc = $this->getDescriptions();
        return parent::configure($command)
            ->addOption('BAD', null, InputOption::VALUE_REQUIRED, $desc['BAD'])
            ->addOption('CONTROL', null, InputOption::VALUE_REQUIRED, $desc['CONTROL'])
            ->addOption('DATA', null, InputOption::VALUE_REQUIRED, $desc['DATA'])
            ->addOption('DIRECT', null, InputOption::VALUE_NONE, $desc['DIRECT'])
            ->addOption('ERRORS', null, InputOption::VALUE_REQUIRED, $desc['ERRORS'])
            ->addOption('LOAD', null, InputOption::VALUE_REQUIRED, $desc['LOAD'])
            ->addOption('LOG', null, InputOption::VALUE_REQUIRED, $desc['LOG'])
            ->addOption('MULTITHREADING', null, InputOption::VALUE_NONE, $desc['MULTITHREADING'])
            ->addOption('PARALLEL', null, InputOption::VALUE_NONE, $desc['PARALLEL'])
            ->addArgument('PARFILE', InputArgument::OPTIONAL, $desc['PARFILE'])
            ->addOption('SILENT', null, InputOption::VALUE_REQUIRED, $desc['SILENT'])
            ->addOption('SKIP', null, InputOption::VALUE_REQUIRED, $desc['SKIP']);
    }


    /**
     * @param InputInterface $input
     * @return string[]
     */
    public function getArgs(InputInterface $input): array
    {
        $opts = $input->getOptions();
        $args = $input->getArguments();

        $values = array_filter(
            [
                'bad' => is_string($opts['BAD']) ? $opts['BAD'] : null,
                'control' => is_string($opts['CONTROL']) ? $opts['CONTROL'] : null,
                'data' => is_string($opts['DATA']) ? $opts['DATA'] : null,
                'direct' => ($opts['DIRECT'] ?? null) === true ? 'true' : null,
                'errors' => is_numeric($opts['ERRORS']) ? intval($opts['ERRORS']) : null,
                'load' => is_numeric($opts['LOAD']) ? intval($opts['LOAD']) : null,
                'log' => is_string($opts['LOG']) ? $opts['LOG'] : null,
                'multithreading' => ($opts['MULTITHREADING'] ?? null) === true ? 'true' : null,
                'parallel' => ($opts['PARALLEL'] ?? null) === true ? 'true' : null,
                'parfile' => is_string($args['PARFILE']) ? $args['PARFILE'] : null,
                'silent' => is_string($opts['SILENT']) ? $opts['SILENT'] : null,
                'skip' => is_numeric($opts['SKIP']) ? intval($opts['SKIP']) : null,
                'userid' => parent::getArgs($input)[0] ?? null,
            ],
            is_scalar(...)
        );

        return array_map(
            fn ($v, $k) => $k . '=' . strval($v),
            array_values($values),
            array_keys($values)
        );
    }
}
