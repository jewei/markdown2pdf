<?php

namespace Jewei\Markdown2pdf;

use Jewei\Markdown2pdf\Generator;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;

class Command extends SymfonyCommand
{
    /**
     * Configure the console command.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('generate:file')
            ->setDescription('Convert MD to PDF')
            ->addArgument(
                'input_file',
                InputArgument::OPTIONAL,
                'Who do you want to greet?',
                'example.md'
            )
            ->addArgument(
                'output_file',
                InputArgument::OPTIONAL,
                'Who do you want to greet?',
                'example.pdf'
            )
            ->addOption(
                'css',
                null,
                InputOption::VALUE_OPTIONAL,
                'Determine which CSS file to load.',
                'example.css'
            )
        ;
    }

    /**
     * Execute the console command.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface  $input
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     * @return mixed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $logger = new Logger('Markdown2pdf');
        $logger->pushHandler(new StreamHandler(__DIR__.'/../generator.log', Logger::DEBUG));

        $filesystem = new Filesystem();

        $generator = new Generator($filesystem, $logger);
        $generator->setMarkdownFile($input->getArgument('input_file'));
        $generator->setPDFFile($input->getArgument('output_file'));
        $generator->setCSSFile($input->getOption('css'));

        if (!$generator->convert()) {
            $output->writeln(sprintf('<error>Error: %s</error>', $generator->getError()));
        } else {
            $output->writeln(sprintf('Created the PDF file <info>%s</info>.', $input->getArgument('output_file')));
        }
    }
}
