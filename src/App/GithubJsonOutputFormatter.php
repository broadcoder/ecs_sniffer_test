<?php

declare(strict_types=1);

namespace App;

use ECSPrefix202209\Nette\Utils\Json;
use Symplify\EasyCodingStandard\Console\Output\ExitCodeResolver;
use Symplify\EasyCodingStandard\Console\Style\EasyCodingStandardStyle;
use Symplify\EasyCodingStandard\Contract\Console\Output\OutputFormatterInterface;
use Symplify\EasyCodingStandard\ValueObject\Configuration;
use Symplify\EasyCodingStandard\ValueObject\Error\ErrorAndDiffResult;
use Symplify\EasyCodingStandard\ValueObject\Error\SystemError;

final class GithubJsonOutputFormatter implements OutputFormatterInterface
{
    public const NAME = 'github';

    private EasyCodingStandardStyle $easyCodingStandardStyle;
    private ExitCodeResolver $exitCodeResolver;

    public function __construct(EasyCodingStandardStyle $easyCodingStandardStyle, ExitCodeResolver $exitCodeResolver)
    {
        $this->easyCodingStandardStyle = $easyCodingStandardStyle;
        $this->exitCodeResolver = $exitCodeResolver;
    }

    public function report(ErrorAndDiffResult $errorAndDiffResult, Configuration $configuration): int
    {
        $this->easyCodingStandardStyle->writeln($this->createJsonContent($errorAndDiffResult));
        return $this->exitCodeResolver->resolve($errorAndDiffResult, $configuration);
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function createJsonContent(ErrorAndDiffResult $errorAndDiffResult): string
    {
        $errors = [];
        foreach ($errorAndDiffResult->getErrors() as $error) {
            $parts = explode('\\', $error->getCheckerClass());
            $errors = [...$errors, [
                'description' => $error->getMessage() . ' reported by ' . array_pop($parts),
                'fingerprint' => md5($error->getRelativeFilePath() . $error->getLine()),
                'severity' => 'major',
                'location' => [
                    'path' => $error->getRelativeFilePath(),
                    'lines' => ['begin' => $error->getLine()]
                ],
            ]];
        }
        foreach ($errorAndDiffResult->getSystemErrors() as $error) {
            if ($error instanceof SystemError) {
                $error = $error->jsonSerialize();
                $errors = [...$errors, [
                    'description' => $error['message'],
                    'fingerprint' => md5($error['relative_file_path'] . $error['line']),
                    'severity' => 'critical',
                    'location' => [
                        'path' => $error['relative_file_path'],
                        'lines' => ['begin' =>  $error['line']]
                    ],
                ]];
            } else {
                $errors = [...$errors, [
                    'description' => $error,
                    'fingerprint' => md5($error),
                    'severity' => 'critical',
                    'location' => [
                        'path' => 'Error',
                        'lines' => 0
                    ],
                ]];
            }

        }
        return Json::encode($errors, Json::PRETTY);
    }
}
