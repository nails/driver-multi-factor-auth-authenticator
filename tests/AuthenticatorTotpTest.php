<?php

namespace Nails\MFA\Driver\Authentication\Tests;

use Nails\Auth\Resource\User;
use Nails\MFA\Driver\Authentication\Authenticator;
use OTPHP\TOTP;
use PHPUnit\Framework\TestCase;

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
        $sSecret = TOTP::generate(secretSize: 20)->getSecret();
        $iNow    = 1_700_000_000;
        $iSlice  = (int) floor($iNow / 30);
        $sCode   = TOTP::createFromSecret($sSecret)->at($iNow);

        self::assertSame(
            $iSlice,
            Authenticator::matchingPeriod($sSecret, $sCode, 1, $iNow)
        );
    }

    public function testMatchingPeriodRejectsWrongCode(): void
    {
        $sSecret = TOTP::generate(secretSize: 20)->getSecret();

        self::assertNull(
            Authenticator::matchingPeriod($sSecret, '000000', 1, 1_700_000_000)
        );
    }

    public function testMatchingPeriodIgnoresFormatting(): void
    {
        $sSecret = TOTP::generate(secretSize: 20)->getSecret();
        $iNow    = 1_700_000_030;
        $iSlice  = (int) floor($iNow / 30);
        $sCode   = TOTP::createFromSecret($sSecret)->at($iNow);

        self::assertSame(
            $iSlice,
            Authenticator::matchingPeriod($sSecret, substr($sCode, 0, 3) . ' ' . substr($sCode, 3), 1, $iNow)
        );
    }

    public function testMatchingPeriodAcceptsRfc6238Sha1Vector(): void
    {
        //  RFC 6238 Appendix B, SHA-1 seed "12345678901234567890" at T = 1111111109
        $sSecret = 'GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQ';
        $iNow    = 1_111_111_109;
        $iSlice  = (int) floor($iNow / 30);

        self::assertSame(
            $iSlice,
            Authenticator::matchingPeriod($sSecret, '081804', 0, $iNow)
        );
    }
}
