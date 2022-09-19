<?php

declare(strict_types=1);

use App\GithubJsonOutputFormatter;
use PHP_CodeSniffer\Standards\Generic\Sniffs\Files\LineLengthSniff;
use PHP_CodeSniffer\Standards\Generic\Sniffs\NamingConventions\UpperCaseConstantNameSniff;
use PHP_CodeSniffer\Standards\PSR12\Sniffs\Functions\ReturnTypeDeclarationSniff;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return static function (ECSConfig $ecsConfig): void {

    $ecsConfig->paths([
        __DIR__ . '/src',
    ]);
    $ecsConfig->cacheDirectory('.ecs');
    $ecsConfig->cacheNamespace('TestProject');
    $ecsConfig->services()->set('github', GithubJsonOutputFormatter::class);

    $sniffers = [
        //UpperCaseConstantNameSniff::class,
        ReturnTypeDeclarationSniff::class,
    ];
    $ecsConfig->rules($sniffers);
    $configured_sniffers = [
        LineLengthSniff::class => ['lineLimit' => 120, 'absoluteLineLimit' => 0],
    ];
    foreach ($configured_sniffers as $sniffer => $cfg) {
        $ecsConfig->ruleWithConfiguration($sniffer, $cfg);
    }
    $ecsConfig->reportSniffClassWarnings([...$sniffers, ...array_keys($configured_sniffers)]);
};
