<?php

namespace Nails\MFA\Driver\Authentication\Tests;

use Nails\Auth\Resource\User;
use Nails\MFA\Driver\Authentication\Authenticator;
use PHPUnit\Framework\TestCase;
use Sonata\GoogleAuthenticator\GoogleAuthenticator;

final class AuthenticatorTotpTest extends TestCase
{
    public function testSetupUsesA160BitSecret(): void
    {
        $oSetup = (new Authenticator())->setupStart(new User());

        //  A 160-bit secret is represented by 32 Base32 characters
        self::assertMatchesRegularExpression('/^[A-Z2-7]{32}$/', $oSetup->secret);
    }

    public function testMatchingPeriodAcceptsCurrentCode(): void
    {
        $sSecret = (new GoogleAuthenticator())->generateSecret();
        $iNow    = 1_700_000_000;
        $iSlice  = (int) floor($iNow / 30);
        $sCode   = (new GoogleAuthenticator())->getCode($sSecret, $iSlice);

        self::assertSame(
            $iSlice,
            Authenticator::matchingPeriod($sSecret, $sCode, 1, $iNow)
        );
    }

    public function testMatchingPeriodRejectsWrongCode(): void
    {
        $sSecret = (new GoogleAuthenticator())->generateSecret();

        self::assertNull(
            Authenticator::matchingPeriod($sSecret, '000000', 1, 1_700_000_000)
        );
    }

    public function testMatchingPeriodIgnoresFormatting(): void
    {
        $sSecret = (new GoogleAuthenticator())->generateSecret();
        $iNow    = 1_700_000_030;
        $iSlice  = (int) floor($iNow / 30);
        $sCode   = (new GoogleAuthenticator())->getCode($sSecret, $iSlice);

        self::assertSame(
            $iSlice,
            Authenticator::matchingPeriod($sSecret, substr($sCode, 0, 3) . ' ' . substr($sCode, 3), 1, $iNow)
        );
    }
}
