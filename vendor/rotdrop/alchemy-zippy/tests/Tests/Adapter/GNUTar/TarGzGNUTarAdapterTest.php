<?php

declare(strict_types=1);

namespace Alchemy\Zippy\Tests\Adapter\GNUTar;

final class TarGzGNUTarAdapterTest extends GNUTarAdapterWithOptionsTestBase
{
    protected function getOptions()
    {
        return '--gzip';
    }

    protected static function getAdapterClassName()
    {
        return 'Alchemy\\Zippy\\Adapter\\GNUTar\\TarGzGNUTarAdapter';
    }
}
