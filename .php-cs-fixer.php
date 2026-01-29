<?php

return (new PhpCsFixer\Config())
    ->setRules([
        '@Laravel' => true,
    ])
    ->setFinder(
        PhpCsFixer\Finder::create()
            ->in(__DIR__)
            ->exclude(['vendor', 'storage'])
    );
