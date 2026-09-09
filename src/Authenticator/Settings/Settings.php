<?php

namespace Nails\MFA\Driver\Authentication\Authenticator\Settings;

use Nails\Common\Interfaces;
use Nails\Components\Setting;
use Nails\Factory;

class Settings implements Interfaces\Component\Settings
{
    const KEY_ISSUER           = 'issuer';
    const KEY_FEEDBACK_PROMPT  = 'feedback_prompt';
    const KEY_FEEDBACK_INVALID = 'feedback_invalid';

    // --------------------------------------------------------------------------

    public function getLabel(): string
    {
        return 'MFA: Authenticator';
    }

    // --------------------------------------------------------------------------

    public function getPermissions(): array
    {
        return [];
    }

    // --------------------------------------------------------------------------

    public function get(): array
    {
        /** @var Setting $oIssuer */
        $oIssuer = Factory::factory('ComponentSetting');
        $oIssuer
            ->setKey(static::KEY_ISSUER)
            ->setLabel('Issuer name')
            ->setDefault('')
            ->setInfo('Shown in authenticator apps. Leave blank to use the application name.');

        /** @var Setting $oPrompt */
        $oPrompt = Factory::factory('ComponentSetting');
        $oPrompt
            ->setKey(static::KEY_FEEDBACK_PROMPT)
            ->setLabel('Prompt')
            ->setFieldset('User Feedback')
            ->setDefault('Enter the code from your authenticator app.');

        /** @var Setting $oInvalid */
        $oInvalid = Factory::factory('ComponentSetting');
        $oInvalid
            ->setKey(static::KEY_FEEDBACK_INVALID)
            ->setLabel('Invalid Code Entered')
            ->setFieldset('User Feedback')
            ->setDefault('Invalid code entered. Please try again.');

        return [
            $oIssuer,
            $oPrompt,
            $oInvalid,
        ];
    }
}
