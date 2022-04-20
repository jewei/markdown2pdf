<?php

namespace Jewei\Markdown2pdf;

use Dompdf\Dompdf;
use Dompdf\Options;
use League\CommonMark\ConverterInterface;
use Symfony\Component\Filesystem\Filesystem;

class Generator
{
    /**
     * The pdf converter.
     *
     * @var \Dompdf\Dompdf
     */
    private $pdf;

    /**
     * The markdown converter.
     *
     * @var \League\CommonMark\ConverterInterface
     */
    private $converter;

    /**
     * The filesystem.
     *
     * @var \Symfony\Component\Filesystem\Filesystem
     */
    private $filesystem;

    /**
     * The markdown file.
     *
     * @var  string
     */
    private $markdown;

    /**
     * The pdf file.
     *
     * @var  string
     */
    private $ouput;

    /**
     * The css file.
     *
     * @var  string
     */
    private $css;

    /**
     * Create a new generator instance.
     *
     * @return void
     */
    public function __construct(Dompdf $pdf, ConverterInterface $converter, Filesystem $filesystem)
    {
        $this->pdf = $pdf;
        $this->converter = $converter;
        $this->filesystem = $filesystem;
    }

    /**
     * Run the generator to convert .md to .pdf.
     *
     * @return void
     * @throws \Jewei\Markdown2pdf\Exception
     */
    public function convert(): void
    {
        /**
         * Get the user data ready.
         */
        if (!$markdown = file_get_contents($this->markdown)) {
            throw new Exception(sprintf('File empty or cannot read: %s', $this->markdown));
        }

        if (!$css = file_get_contents($this->css)) {
            throw new Exception(sprintf('File empty or cannot read: %s', $this->css));
        }

        if (!$stub = file_get_contents($this->stub)) {
            throw new Exception(sprintf('File empty or cannot read: %s', $this->stub));
        }

        /**
         * Covert Markdown to HTML.
         */
        $content = $this->converter->convert($markdown);

        $html = str_replace(
            ['{{ css }}', '{{ content }}'],
            [$css, $content],
            $stub
        );

        /**
         * Convert HTML to PDF.
         */
        $this->pdf->setOptions(new Options([
            'logOutputFile' => false,
            'isRemoteEnabled' => true,
            'isFontSubsettingEnabled' => false,
        ]));
        $this->pdf->loadHtml($html);
        $this->pdf->render();

        $this->filesystem->dumpFile($this->ouput, $this->pdf->output());
    }

    /**
     * Set the markdown file.
     *
     * @param  string  $file
     * @return $this
     */
    public function setMarkdown(string $file)
    {
        $this->markdown = $file;

        return $this;
    }

    /**
     * Set the PDF file.
     *
     * @param  string  $file
     * @return $this
     */
    public function setPdf(string $file)
    {
        $this->ouput = $file;

        return $this;
    }

    /**
     * Set the CSS file.
     *
     * @param  string  $file
     * @return $this
     */
    public function setCss(string $file)
    {
        $this->css = $file;

        return $this;
    }

    /**
     * Set the stub file.
     *
     * @param  string  $file
     * @return $this
     */
    public function setStub(string $file)
    {
        $this->stub = $file;

        return $this;
    }
}
