<?php

namespace Core\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class InstallCommand extends ContainerAwareCommand
{
    protected $pathToBeCreated = array(
        'images',
        'uploads'
    );

    protected function configure()
    {
        $this->setName('app:install')
             ->setDescription('App installer.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Installing Application.</info>');
        $output->writeln('');

        $this->setupStep($input, $output);

        $output->writeln('<info>Application has been successfully installed.</info>');
    }

    protected function setupStep(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('<info>Setting up database.</info>');

        $this->runCommand('doctrine:database:create', $input, $output);
        $this->runCommand('doctrine:schema:create', $input, $output);
        $this->runCommand('assets:install', $input, $output);
        $this->runCommand('assetic:dump', $input, $output);

        $output->writeln('');
        $output->writeln('<info>Administration setup.</info>');

        $this->runCommand('app:populate:user', $input, $output);
        $output->writeln('');

        $output->writeln('');

        return $this;
    }

    private function runCommand($command, InputInterface $input, OutputInterface $output)
    {
        $this->getApplication()
             ->find($command)
             ->run($input, $output);

        return $this;
    }

    protected function createCoreDirectories()
    {
        $fs = new Filesystem();

        foreach ($this->pathToBeCreated as $path) {
            if (!$fs->exists($path)) {
                $fs->mkdir($path);
                $fs->chmod($path, '0777');
            }
        }
    }
}
