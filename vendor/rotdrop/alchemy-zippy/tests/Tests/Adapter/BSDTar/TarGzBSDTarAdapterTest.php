<?php

declare(strict_types=1);

namespace Alchemy\Zippy\Tests\Adapter\BSDTar;

final class TarGzBSDTarAdapterTest extends BSDTarAdapterWithOptionsTestBase
{
    protected function getOptions()
    {
        return '--gzip';
    }

    protected static function getAdapterClassName()
    {
        return 'Alchemy\\Zippy\\Adapter\\BSDTar\\TarGzBSDTarAdapter';
    }
}
