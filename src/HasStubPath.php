<?php

namespace Marv311\BlueprintNovaAddon;

trait HasStubPath
{
    /**
     * Returns the stub path for this package.
     *
     * @return string
     */
    protected function stubPath(): string
    {
        return dirname(__DIR__).'/stubs';
    }
}
