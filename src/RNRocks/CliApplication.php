<?php

/*
 * This file is part of the Rhein Neckar Rocks Crawler project.
 *
 * (c) Rhein Neckar Rocks
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RNRocks;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;

class CliApplication extends Application
{
    /**
     * @var string
     */
    private $singleCommandName;

    /**
     * Creates a new {@link \RNRocks\CliApplication}.
     *
     * @param Command $command
     */
    public function __construct(Command $command)
    {
        parent::__construct('rheinneckarrocks Crawler');

        // Add the given command as single (publicly accessible) command.
        $this->add($command);
        $this->singleCommandName = $command->getName();

        // Override the Application's definition so that it does not
        // require a command name as first argument.
        $this->getDefinition()->setArguments();
    }

    /**
     * {@inheritDoc}
     */
    protected function getCommandName(InputInterface $input)
    {
        if ($input->hasOption('help')) {
            $command = new HelpCommand();
            return $command->getName();
        }

        return $this->singleCommandName;
    }
}
