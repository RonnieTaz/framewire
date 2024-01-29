<?php

namespace Framewire\Foundation\Console\Commands;

use Framewire\Stubs\Database\Model;
use League\Container\ContainerAwareInterface;
use League\Container\ContainerAwareTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateModelCommand extends Command implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    protected function configure(): void
    {
        $this->setName('generate:model')
            ->setDescription('Generates new model class.')
            ->setHelp('This command allows you to create a new model.')
            ->addArgument('model', InputArgument::REQUIRED, 'The class name of the model.')
            ->addArgument('namespace', InputArgument::OPTIONAL, 'The namespace for the model class.');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln([
            'Generating model ...',
            '====================',
            ''
        ]);

        $stub = new Model();

        $name = ucwords($input->getArgument('model'));
        $namespace = $input?->getArgument('namespace') ?? 'App\Models';

        if (!file_put_contents(
            dirname(__DIR__, 4) . "/app/Models/$name.php",
            $stub($input->getArgument('model'), $input->getArgument('namespace'))
        )) {
            return Command::FAILURE;
        }

        $output->writeln([
            "Model {$input->getArgument('model')} generated under $namespace namespace"
        ]);

        return Command::SUCCESS;
    }
}
