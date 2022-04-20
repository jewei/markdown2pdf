<?php

namespace Jewei\Markdown2pdf;

use Dompdf\Dompdf;
use Exception;
use Jewei\Markdown2pdf\Generator;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
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
                'primer.css'
            );
    }

    /**
     * Execute the console command.
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $generator = new Generator(
            new Dompdf(),
            new GithubFlavoredMarkdownConverter([
                'html_input' => 'strip',
                'allow_unsafe_links' => false,
            ]),
            new Filesystem()
        );

        $generator
            ->setMarkdown($input->getArgument('input_file'))
            ->setPdf($input->getArgument('output_file'))
            ->setCss($input->getOption('css'))
            ->setStub(dirname(__DIR__) . '/src/html.stub');

        try {
            $generator->convert();
            $output->writeln(sprintf('<info>Created the PDF file %s</info>.', $input->getArgument('output_file')));
        } catch (Exception $e) {
            $output->writeln(sprintf('<error>Error: %s</error>', $e->getMessage()));
            return SymfonyCommand::FAILURE;
        }

        return SymfonyCommand::SUCCESS;
    }
}
