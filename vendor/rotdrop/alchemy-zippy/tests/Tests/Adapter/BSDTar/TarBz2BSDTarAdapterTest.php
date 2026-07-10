<?php

declare(strict_types=1);

namespace Alchemy\Zippy\Tests\Adapter\BSDTar;

final class TarBz2BSDTarAdapterTest extends BSDTarAdapterWithOptionsTestBase
{
    protected function getOptions()
    {
        return '--bzip2';
    }

    protected static function getAdapterClassName()
    {
        return 'Alchemy\\Zippy\\Adapter\\BSDTar\\TarBz2BSDTarAdapter';
    }
}
