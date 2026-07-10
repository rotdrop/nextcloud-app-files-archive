<?php

declare(strict_types=1);

namespace Alchemy\Zippy\Tests\Adapter\VersionProbe;

final class BSDTarVersionProbeTest extends AbstractTarVersionProbeTestBase
{
    public static function getProbeClassName()
    {
        return 'Alchemy\Zippy\Adapter\VersionProbe\BSDTarVersionProbe';
    }

    public static function getCorrespondingVersionOutput()
    {
        return self::getBSDTarVersionOutput();
    }

    public static function getNonCorrespondingVersionOutput()
    {
        return self::getGNUTarVersionOutput();
    }
}
