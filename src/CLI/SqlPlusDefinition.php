<?php

declare(strict_types=1);

namespace Oracle\CLI;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

class SqlPlusDefinition extends OracleDefinition
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
            'C' => 'Sets the compatibility of affected commands to the specified version. The version has form "x.y[.z]'
                 . '". For example, --C=10.2.0',
            'ED' => 'Specifies the value for Session Edition',
            'F' => 'This option improves performance in general. It changes the default values settings. See SQL*Plus U'
                 . 'ser\'s Guide for the detailed settings.',
            'L' => 'Attempts to log on just once, instead of reprompting on error.',
            'M' => 'Sets automatic HTML or CSV markup of output. The options have the form: {HTML html_options|CSV csv_'
                 . 'options} See SQL*Plus User\'s Guide for detailed HTML and CSV options.',
            'NOLOG' => 'Starts SQL*Plus without connecting to a database',
            'NOLOGINTIME' => 'Don\'t display Last Successful Login Time.',
            'R' => 'Sets restricted mode to disable SQL*Plus commands that interact with the file system. The level can'
                 . ' be 1, 2 or 3. The most restrictive is --R=3 which disables all user commands interacting with the '
                 . 'file system.',
            'S' => 'Sets silent mode which suppresses the display of the SQL*Plus banner, prompts, and echoing of comma'
                 . 'nds.',
            'SCRIPT' => 'Runs the specified SQL*Plus script from a web server (URL) or the local file system (filename.'
                      . 'ext)',
            'PARAMETERS' => 'Specified parameters that will be assigned to substitution variables in the script'
        ];

        parent::__construct('sqlplus', $workDir, $env, $descriptions);
    }


    /**
     * @param Command $command
     * @return Command
     */
    public function configure(Command $command): Command
    {
        $desc = $this->getDescriptions();
        return parent::configure($command)
            ->addOption('C', null, InputOption::VALUE_REQUIRED, $desc['C'])
            ->addOption('ED', null, InputOption::VALUE_REQUIRED, $desc['ED'])
            ->addOption('F', null, InputOption::VALUE_NONE, $desc['F'])
            ->addOption('L', null, InputOption::VALUE_NONE, $desc['L'])
            ->addOption('M', null, InputOption::VALUE_REQUIRED, $desc['M'])
            ->addOption('NOLOG', null, InputOption::VALUE_NONE, $desc['NOLOG'])
            ->addOption('NOLOGINTIME', null, InputOption::VALUE_NONE, $desc['NOLOGINTIME'])
            ->addOption('R', null, InputOption::VALUE_REQUIRED, $desc['R'])
            ->addOption('S', null, InputOption::VALUE_NONE, $desc['S'])
            ->addArgument('SCRIPT', InputArgument::REQUIRED, $desc['SCRIPT'])
            ->addArgument('PARAMETERS', InputArgument::IS_ARRAY, $desc['PARAMETERS']);
    }


    /**
     * @param InputInterface $input
     * @return string[]
     */
    public function getArgs(InputInterface $input): array
    {
        $opts = $input->getOptions();
        $args = $input->getArguments();

        $compatibility = is_string($opts['C']) ? $opts['C'] : null;
        $edition = is_string($opts['ED']) ? $opts['ED'] : null;
        $fast = ($opts['F'] ?? null) === true;
        $logOnce = ($opts['L'] ?? null) === true;
        $markupOptions = is_string($opts['M']) ? $opts['M'] : null;
        $noLog = ($opts['NOLOG'] ?? null) === true;
        $noLoginTime = ($opts['NOLOGINTIME'] ?? null) === true;
        $restrictedMode = is_string($opts['R']) ? $opts['R'] : null;
        $silent = ($opts['S'] ?? null) === true;

        $script = is_string($args['SCRIPT']) ? '@' . $args['SCRIPT'] : null;
        $parameters = array_map(
            strval(...),
            array_filter(is_array($args['PARAMETERS']) ? $args['PARAMETERS'] : [], is_scalar(...))
        );

        return array_filter(
            [
                ...parent::getArgs($input),
                is_string($compatibility) ? "-C={$compatibility}" : null,
                is_string($edition) ? (str_starts_with($edition, "EDITION=") ? "" : "EDITION=") . $edition : null,
                $fast === true ? "-F" : null,
                $logOnce === true ? "-L" : null,
                is_string($markupOptions) ? "-M={$markupOptions}" : null,
                $noLog === true ? "-NOLOG" : null,
                $noLoginTime === true ? "-NOLOGINTIME" : null,
                is_string($restrictedMode) ? "-R={$restrictedMode}" : null,
                $silent === true ? "-S" : null,
                $script,
                ...$parameters
            ],
            is_string(...)
        );
    }
}
