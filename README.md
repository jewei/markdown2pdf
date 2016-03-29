## Installation
Convert Markdown to PDF file.

## Usage

    composer install
    chmod +x bin/console

## Example usage

    bin/console generate:file example.md example.pdf

    // Set custom CSS.
    bin/console generate:file example.md example.pdf --css=example.css --style=traditional

## Usage

    Usage:
      command [options] [arguments]

    Options:
      -h, --help            Display this help message
      -q, --quiet           Do not output any message
      -V, --version         Display this application version
          --ansi            Force ANSI output
          --no-ansi         Disable ANSI output
      -n, --no-interaction  Do not ask any interactive question
      -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

    Available commands:
      help           Displays help for a command
      list           Lists commands
     generate
      generate:file  Convert MD to PDF
