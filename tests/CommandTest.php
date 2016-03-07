<?php

namespace Jewei\Markdown2pdf\Test;

use Jewei\Markdown2pdf\Command;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CommandTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $application = new Application();
        $application->add(new Command());

        $command = $application->find('generate:file');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array('command' => $command->getName()));

        $this->assertEquals("Created the PDF file example.pdf.\n", $commandTester->getDisplay());
    }

    public function testInputArgumentsAndOptions()
    {
        $application = new Application();
        $application->add(new Command());

        $command = $application->find('generate:file');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(),
            'input_file' => 'example.md',
            'output_file' => 'example.pdf',
            '--css' => 'example.css',
        ));

        $this->assertEquals('example.md', $commandTester->getInput()->getArgument('input_file'));
        $this->assertEquals('example.pdf', $commandTester->getInput()->getArgument('output_file'));
        $this->assertEquals('example.css', $commandTester->getInput()->getOption('css'));
    }

    public function testPDFGeneration()
    {
        //...
    }
}
