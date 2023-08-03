<?php
declare(strict_types=1);

namespace Nubium\JUnitFormatter\Tests;

use Nubium\JUnitFormatter\FileErrorFormatter;
use Nubium\JUnitFormatter\FileOutput;
use PHPStan\Command\AnalysisResult;
use PHPStan\Command\ErrorFormatter\JunitErrorFormatter;
use PHPStan\Command\ErrorFormatter\TableErrorFormatter;
use PHPStan\Command\Output;
use PHPUnit\Framework\TestCase;

class FileErrorFormatterTest extends TestCase
{
    public function testFormat(): void
    {
        $file = $this->createTempFile();
        $result = $this->createMock(AnalysisResult::class);
        $output = $this->createMock(Output::class);

        $tableFormatter = $this->createMock(TableErrorFormatter::class);
        $tableFormatter->expects($this->once())
            ->method('formatErrors')
            ->with($result, $output);

        $junitFormatter = $this->createMock(JunitErrorFormatter::class);
        $junitFormatter->expects($this->once())
            ->method('formatErrors')
            ->with($result, $this->isInstanceOf(FileOutput::class));

        try {
            $fileFormatter = new FileErrorFormatter(
                $file,
                $tableFormatter,
                $junitFormatter
            );

            $fileFormatter->formatErrors($result, $output);
        } finally {
            unlink($file);
        }
    }

    private function createTempFile(): string
    {
        /** @var non-empty-string|false $file */
        $file = tempnam(sys_get_temp_dir(), 'app-logger');
        if ($file === false) {
            $this->fail(error_get_last()['message'] ?? 'can not create temp file');
        }

        return $file;
    }
}