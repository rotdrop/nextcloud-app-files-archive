<?php

declare(strict_types=1);

namespace Alchemy\Zippy\Tests\Adapter\VersionProbe;

final class GNUTarVersionProbeTest extends AbstractTarVersionProbeTestBase
{
    public static function getProbeClassName()
    {
        return 'Alchemy\Zippy\Adapter\VersionProbe\GNUTarVersionProbe';
    }

    public static function getCorrespondingVersionOutput()
    {
        return self::getGNUTarVersionOutput();
    }

    public static function getNonCorrespondingVersionOutput()
    {
        return self::getBSDTarVersionOutput();
    }
}
