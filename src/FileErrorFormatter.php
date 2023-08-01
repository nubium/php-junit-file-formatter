<?php
declare(strict_types=1);

namespace Nubium\JUnitFormatter;

use PHPStan\Command\AnalysisResult;
use PHPStan\Command\ErrorFormatter\ErrorFormatter;
use PHPStan\Command\ErrorFormatter\JunitErrorFormatter;
use PHPStan\Command\ErrorFormatter\TableErrorFormatter;
use PHPStan\Command\Output;
use Symfony\Component\Console\Output\StreamOutput;

class FileErrorFormatter implements ErrorFormatter
{
    public function __construct(
        private string $outputFilePath,
        private readonly TableErrorFormatter $tableErrorFormatter,
        private readonly JunitErrorFormatter $junitErrorFormatter
    )
    {}

    public function formatErrors(AnalysisResult $analysisResult, Output $output): int
    {
        // write human-readable output to console
        $this->tableErrorFormatter->formatErrors($analysisResult, $output);

        // write junit to file
        $fileStream = new StreamOutput(fopen($this->outputFilePath, 'w'));
        $fileOutput = new FileOutput($fileStream, $output->getStyle());
        $this->junitErrorFormatter->formatErrors($analysisResult, $fileOutput);

        return $analysisResult->hasErrors() ? 1 : 0;
    }
}