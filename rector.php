<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        //__DIR__ . '/config',
        //__DIR__ . '/public',
        __DIR__ . '/src',
    ]);

    // register a single rule
    //$rectorConfig->rule(InlineConstructorDefaultToPropertyRector::class);

    $rectorConfig->sets([
        Rector\Set\ValueObject\LevelSetList::UP_TO_PHP_82,        // ✅
//        Rector\Symfony\Set\SymfonySetList::SYMFONY_54,              // ⚠️ TODO
//        Rector\Symfony\Set\SymfonySetList::SYMFONY_62,              // ⚠️ TODO
//        Rector\Symfony\Set\SymfonySetList::SYMFONY_64,              // ⚠️ TODO
//        Rector\Symfony\Set\SymfonySetList::SYMFONY_CODE_QUALITY,  // ⚠️ TODO
//        Rector\Symfony\Set\SymfonySetList::ANNOTATIONS_TO_ATTRIBUTES,   // ⚠️ TODO
//        \Rector\Doctrine\Set\DoctrineSetList::ANNOTATIONS_TO_ATTRIBUTES,    // ⚠️ TODO
    ]);

    // Règles ignorées pour l'instant (non bloquantes) ou ignorées tout court (inutiles)
    $rectorConfig->skip([
//        \Rector\Php81\Rector\FuncCall\NullToStrictStringFuncCallArgRector::class,
//        \Rector\Php80\Rector\Class_\ClassPropertyAssignToConstructorPromotionRector::class,
//        \Rector\Php81\Rector\Property\ReadOnlyPropertyRector::class,
//        \Rector\Php81\Rector\ClassConst\FinalizePublicClassConstantRector::class,
//        \Rector\Php80\Rector\FunctionLike\MixedTypeRector::class,

        \Rector\Php80\Rector\Class_\StringableForToStringRector::class,
        \Rector\Php73\Rector\FuncCall\JsonThrowOnErrorRector::class,
        \Rector\Php71\Rector\FuncCall\CountOnNullRector::class,
        \Rector\TypeDeclaration\Rector\Property\TypedPropertyFromAssignsRector::class,
    ]);
};





