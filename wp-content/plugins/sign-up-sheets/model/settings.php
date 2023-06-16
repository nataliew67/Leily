<?php
/**
 * Settings Model
 */

namespace FDSUS\Model;

use FDSUS\Id as Id;

if (Id::isPro()) {
    class SettingsParent extends Pro\Settings {}
} else {
    class SettingsParent {}
}

class Settings extends SettingsParent
{
    /** @var Settings|null  */
    private static $instance = null;

    /** @var array $text can be overridden in Settings */
    public static $text = array();

    /** @var array  */
    public static $defaultMailSubjects = array();

    /** @var array  */
    public static $defaultMailMessages = array();

    /**
     * Constructor (instantiation disallowed)
     */
    private function __construct()
    {
        self::$defaultMailSubjects = array(
            'signup'   => esc_html__('Thank you for signing up!', 'fdsus'),
            'remove'   => esc_html__('Sign-up has been removed', 'fdsus'),
            'reminder' => esc_html__('Sign-up Reminder', 'fdsus'),
            'status'   => esc_html__('Sign-up Status Report', 'fdsus'),
        );

        self::$defaultMailMessages['signup'] = 'This message was sent to confirm that you signed up for...'
            . PHP_EOL . PHP_EOL
            . '{signup_details}' . PHP_EOL . PHP_EOL
            . 'To cancel your sign-up '
            . 'contact us at {from_email}' . PHP_EOL . PHP_EOL
            . 'Thanks,' . PHP_EOL
            . '{site_name}' . PHP_EOL
            . '{site_url}';
    }

    /**
     * Get singleton instance
     *
     * @return Settings|null
     */
    public static function instance()
    {
        if (self::$instance === null) {
            self::$instance = new Settings;
        }

        self::setText();

        return self::$instance;
    }

    /**
     * Set text and override values
     */
    private static function setText()
    {
        self::$text = array(
            'task_title_label' => array(
                'label'   => esc_html__('Task Title Label', 'fdsus'),
                'default' => esc_html__('What', 'fdsus'),
            ),
        );

        foreach (self::$text as $key => $text) {
            $override = get_option('dls_sus_text_' . $key, false);
            if ($override === false || $override === '') {
                self::$text[$key]['value'] = self::$text[$key]['default'];
            } else {
                self::$text[$key]['value'] = $override;
            }
        }
        reset(self::$text);
    }

    /**
     * Is receipt enabled?
     *
     * @return bool
     */
    public static function isReceiptEnabled()
    {
        return get_option('dls_sus_signup_receipt') === 'true';
    }

    /**
     * Is display all signup data enabled?
     *
     * @return bool
     */
    public static function isDisplayAllSignupDataEnabled()
    {
        return get_option('dls_sus_display_all') === 'true';
    }

    /**
     * Is detailed errors enabled?
     *
     * @return bool
     */
    public static function isDetailedErrors()
    {
        return Id::DEBUG_DISPLAY || get_option('dls_sus_detailed_errors') === 'true';
    }

    /**
     * Is user auto-populate disabled?
     *
     * @return bool
     */
    public static function isUserAutopopulateDisabled()
    {
        return get_option('dls_sus_disable_user_autopopulate') === 'true';
    }

    /**
     * Is spam honeypot disabled?
     *
     * @return bool
     */
    public static function isHoneypotDisabled()
    {
        return get_option('dls_sus_disable_honeypot') === 'true';
    }

    /**
     * Is spam honeypot disabled?
     *
     * @return bool
     */
    public static function isEmailValidationEnabled()
    {
        return get_option('dls_sus_deactivate_email_validation') !== 'true';
    }

    /**
     * Is all captcha disabled?
     *
     * @return bool
     */
    public static function isAllCaptchaDisabled()
    {
        return get_option('dls_sus_disable_captcha') === 'true';
    }

    /**
     * Is reCAPTCHA enabled?
     *
     * @return bool
     */
    public static function isRecaptchaEnabled()
    {
        return get_option('dls_sus_recaptcha') === 'true' && !self::isAllCaptchaDisabled();
    }

    /**
     * Get reCAPTCHA Version
     *
     * @return string
     */
    public static function getRecaptchaVersion()
    {
        $version = get_option('dls_sus_recaptcha_version');

        return !$version ? 'v2-checkbox' : $version;
    }

    /**
     * Get current plugin basename depending on if we are in the pro or free version
     *
     * @return string
     */
    public static function getCurrentPluginBasename()
    {
        return Id::isPro() ? Id::PRO_PLUGIN_BASENAME : Id::FREE_PLUGIN_BASENAME;
    }

    /**
     * Is confirmation email enabled?
     * This method is the source of truth for this option including default value if not set
     *
     * @return bool
     */
    public static function isConfirmationEmailEnabled()
    {
        $option = get_option('fdsus_enable_confirmation_email');
        return $option === false || $option === 'true';
    }

    /**
     * Is removal confirmation email enabled?
     * This method is the source of truth for this option including default value if not set
     *
     * @return bool
     */
    public static function isRemovalConfirmationEmailEnabled()
    {
        $option = get_option('fdsus_enable_removal_confirmation_email');
        return $option === false || $option === 'true';
    }
}
