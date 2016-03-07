<?php

namespace Jewei\Markdown2pdf;

use Dompdf\Css\Stylesheet;
use Dompdf\Dompdf;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;

class Generator
{
    /**
     * The filesystem instance.
     *
     * @var \Filesystem
     */
    private $filesystem;

    /**
     * The logger interface implementation.
     *
     * @var \LoggerInterface
     */
    private $logger;

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
    private $pdf;

    /**
     * The css file.
     *
     * @var  string
     */
    private $css;

    /**
     * The markdown flavor.
     *
     * @var  string
     */
    private $flavor;

    /**
     * The error message.
     *
     * @var  string
     */
    private $error;

    /**
     * Create a new generator instance.
     *
     * @return void
     */
    public function __construct(Filesystem $filesystem, LoggerInterface $logger)
    {
        $this->filesystem = $filesystem;
        $this->logger = $logger;
    }

    /**
     * Run the generator to convert .md to .pdf.
     *
     * @param  string  $source
     * @param  string  $destination
     * @param  string  $style
     * @return mixed
     */
    public function convert()
    {
        if (!$markdown = file_get_contents($this->markdown)) {
            $this->setError(sprintf('File empty or cannot read: %s', $this->markdown));
            return false;
        }

        $this->logger->debug(sprintf('Mardown: %s', $this->markdown));

        if (!$css = file_get_contents($this->css)) {
            $this->setError(sprintf('File empty or cannot read: %s', $this->css));
            return false;
        }

        $this->logger->debug(sprintf('CSS: %s', $this->css));

        switch ($this->flavor) {
            case 'extra':
                $parser = new \cebe\markdown\MarkdownExtra();
                break;

            case 'github':
                $parser = new \cebe\markdown\GithubMarkdown();
                break;

            case 'traditional':
            default:
                $parser = new \cebe\markdown\Markdown();
                break;
        }

        $markdown = $parser->parse($markdown);

        $html = <<<EOD
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
    "http://www.w3.org/TR/html4/loose.dtd">
<html>
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="$this->css" media="screen" rel="stylesheet" type="text/css" />
  </head>
  <body>
    <article>
        $markdown
    </article>
  </body>
</html>
EOD;

        $this->logger->debug(sprintf('Raw HTML: %s', $html));

        // Instantiate and use the dompdf class
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->render();

        $this->filesystem->dumpFile($this->pdf, $dompdf->output());
        $this->logger->info(sprintf('PDF Created: %s', $this->pdf));

        return true;
    }

    /**
     * Set the markdown file.
     *
     * @param  string  $file
     */
    public function setMarkdownFile($file)
    {
        $this->markdown =  $file;
    }

    /**
     * Set the PDF file.
     *
     * @param  string  $file
     */
    public function setPDFFile($file)
    {
        $this->pdf =  $file;
    }

    /**
     * Set the CSS file.
     *
     * @param  string  $file
     */
    public function setCSSFile($file)
    {
        $this->css =  $file;
    }

    /**
     * Set the Markdown flavor.
     *
     * @param  string  $flavor
     */
    public function setFlavor($flavor)
    {
        $this->flavor =  $flavor;
    }

    /**
     * Set the error message.
     *
     * @param  string
     * @return void
     */
    public function setError($error)
    {
        $this->error = $error;
        $this->logger->error($this->error);
    }

    /**
     * Get the error message.
     *
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }
}
