<?php
declare(strict_types=1);

namespace Nubium\JUnitFormatter;

use PHPStan\Command\Output;
use PHPStan\Command\OutputStyle;
use Symfony\Component\Console\Output\OutputInterface;

class FileOutput implements Output
{
    public function __construct(
        private readonly OutputInterface $streamOutput,
        private readonly OutputStyle $style
    )
    {}

    public function writeFormatted(string $message): void
    {
        $this->streamOutput->write($message, \false, OutputInterface::OUTPUT_NORMAL);
    }

    public function writeLineFormatted(string $message): void
    {
        $this->streamOutput->writeln($message, OutputInterface::OUTPUT_NORMAL);
    }

    public function writeRaw(string $message): void
    {
        $this->streamOutput->write($message, \false, OutputInterface::OUTPUT_RAW);
    }

    public function getStyle(): OutputStyle
    {
        return $this->style;
    }

    public function isVerbose(): bool
    {
        return $this->streamOutput->isVerbose();
    }

    public function isDebug(): bool
    {
        return $this->streamOutput->isDebug();
    }
}
