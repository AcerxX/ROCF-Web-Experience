<?php

namespace App\ComposerScript;

class Hooks
{
    public static function checkHooks($event): bool
    {
        $io = $event->getIO();
        $gitHook = @file_get_contents(__DIR__ . '/../../.git/hooks/pre-commit');
        $docHook = @file_get_contents(__DIR__ . '/../../docs/hooks/pre-commit');

        if ($gitHook !== $docHook) {
            exec('rm -rf ' . __DIR__ . '/../../.git/hooks');
            exec('ln -s ' . __DIR__ . '/../../docs/hooks/ ' . __DIR__ . '/../../.git/hooks');
            exec('chmod a+x ' . __DIR__ . '/../../docs/hooks/pre-commit');
            $io->write('<warning>Git hooks were automatically configured by Users Engine application.</warning>');
        }

        $io->write('Everything looks good! Have a wonderful day!');
        return true;
    }

    public static function configurePHPCS($event): bool
    {
        $io = $event->getIO();

        exec('./vendor/bin/phpcs --config-set default_standard PSR2');

        $io->write('PHPCS has been set to use PSR2 standard!');
        return true;
    }
}
