<?php

declare(strict_types=1);

namespace Alchemy\Zippy\Tests\Adapter\GNUTar;

final class TarBz2GNUTarAdapterTest extends GNUTarAdapterWithOptionsTestBase
{
    protected function getOptions()
    {
        return '--bzip2';
    }

    protected static function getAdapterClassName()
    {
        return 'Alchemy\\Zippy\\Adapter\\GNUTar\\TarBz2GNUTarAdapter';
    }
}
