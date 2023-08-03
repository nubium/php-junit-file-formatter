<?php
declare(strict_types=1);

namespace Nubium\JUnitFormatter;

use PHPStan\Command\AnalysisResult;
use PHPStan\Command\ErrorFormatter\ErrorFormatter;
use PHPStan\Command\ErrorFormatter\JunitErrorFormatter;
use PHPStan\Command\ErrorFormatter\TableErrorFormatter;
use PHPStan\Command\Output;
use PHPStan\ShouldNotHappenException;
use Symfony\Component\Console\Output\StreamOutput;

class FileErrorFormatter implements ErrorFormatter
{
    public function __construct(
        private string $outputFilePath,
        private readonly TableErrorFormatter $tableErrorFormatter,
        private readonly JunitErrorFormatter $junitErrorFormatter
    )
    {}

    /**
     * @throws ShouldNotHappenException
     */
    public function formatErrors(AnalysisResult $analysisResult, Output $output): int
    {
        // write human-readable output to console
        $this->tableErrorFormatter->formatErrors($analysisResult, $output);

        // write junit to file
        $resource = fopen($this->outputFilePath, 'w');

        if ($resource === false) {
            throw new ShouldNotHappenException('Cannot open file for writing: ' . $this->outputFilePath);
        }

        $fileStream = new StreamOutput($resource);
        $fileOutput = new FileOutput($fileStream, $output->getStyle());

        // @phpstan-ignore-next-line
        $this->junitErrorFormatter->formatErrors($analysisResult, $fileOutput);

        return $analysisResult->hasErrors() ? 1 : 0;
    }
}