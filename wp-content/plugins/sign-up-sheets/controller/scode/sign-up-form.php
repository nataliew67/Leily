<?php
/**
 * [sign_up_form] Shortcode Controller
 */

namespace FDSUS\Controller\Scode;

use FDSUS\Id;
use FDSUS\Controller\Base;
use FDSUS\Controller\Mail as Mail;
use FDSUS\Model\Data;
use FDSUS\Model\Settings;
use FDSUS\Model\Signup;
use FDSUS\Model\States as StatesModel;
use FDSUS\Model\Sheet as SheetModel;
use FDSUS\Model\Task as TaskModel;
use FDSUS\Model\Signup as SignupModel;
use FDSUS\Lib\Dls\Notice;
use FDSUS\Lib\Exception;
use FDSUS\Lib\ReCaptcha\ReCaptcha;
use WP_User;

class SignUpForm extends Base
{

    private $data;
    private $mail;

    public function __construct()
    {
        parent::__construct();

        $this->data = new Data();
        $this->mail = new Mail();

        add_shortcode('sign_up_form', array(&$this, 'shortcode'));

        add_action('init', array(&$this, 'maybeProcessSignupForm'), 9);
    }

    /**
     * Enqueue plugin css and js files
     */
    public function addCssAndJsToSignUp()
    {
        wp_enqueue_script('jquery');
        wp_enqueue_style(Id::PREFIX . '-style');
        if (Settings::isEmailValidationEnabled()) {
            wp_enqueue_script(Id::PREFIX . '-mailcheck');
        }
        wp_enqueue_script(Id::PREFIX . '-js');

        if (Settings::isRecaptchaEnabled()) {
            wp_enqueue_script('fdsus-recaptcha');
        }
    }

    /**
     * Display signup form
     *
     * @param array $atts
     *
     * @return string
     */
    public function shortcode($atts)
    {
        $this->addCssAndJsToSignUp();

        ob_start();

        /** @var int|string $task_ids comma separated list of task ids */
        extract(
            shortcode_atts(
                array(
                    'task_ids' => 0, // int or array of task ids
                ), $atts
            )
        );

        $task_ids = explode(',', $task_ids);
        /** @var array $task_ids */
        $task_ids = array_map('intval', $task_ids); // convert all to int
        $task_id = current($task_ids);

        if (empty($task_id)) {
            echo '<p>' . esc_html__('Task not found.', 'fdsus') . '</p>';
            return ob_get_clean();
        }

        $task = new TaskModel($task_id);
        if (empty($task) || empty($task->post_parent)) {
            echo '<p>' . esc_html__('No Sign-up Form Found.', 'fdsus') . '</p>';
            return ob_get_clean();
        }

        $sheet = $task->getSheet();
        if (empty($sheet)) {
            echo '<p>' . esc_html__('No Sign-up Sheet Found.', 'fdsus') . '</p>';
            return ob_get_clean();
        }

        $_POST = $this->data->stripslashes_full($_POST);

        $multi_tag = '';
        $date_display = null;
        $signup_titles = array();
        if (isset($_POST['signupbox_multi'])) {
            if (is_array($_POST['signupbox_multi'])) {
                $tasks = $_POST['signupbox_multi'];

                $tasks_str = '';
                foreach ($tasks as $t) {
                    if ($tasks_str != '') {
                        $tasks_str .= ',';
                    }
                    $tasks_str .= $t;
                    $task = new TaskModel($t);
                    $date_display = null;
                    if ($date = $task->getDate()) {
                        $date_display = ' ' . esc_html__('on', 'fdsus')
                            . sprintf(' <em class="dls-sus-task-date">%s</em>',
                                date(get_option('date_format'), strtotime($date))
                            );
                    }
                    $signup_titles[] = $task->post_title . $date_display;
                }
                $multi_tag = '<input type="hidden" id="signupbox_multi_str"  name="signupbox_multi_str"  value="' . esc_attr($tasks_str) . '" />';
            }
        } elseif ( isset( $_POST['signupbox_multi_str'] ) ) {
            $multi_tag = '<input type="hidden" id="signupbox_multi_str"  name="signupbox_multi_str"  value="' . esc_attr($_POST['signupbox_multi_str']) . '" />';
            $st = explode(",", $_POST['signupbox_multi_str']);
            foreach ($st as $s) {
                $task = new TaskModel($s);
                $date_display = null;
                if ($date = $task->getDate()) {
                    $date_display = ' ' . esc_html__('on', 'fdsus')
                        . sprintf(
                            ' <em class="dls-sus-task-date">%s</em>',
                            date(get_option('date_format'), strtotime($date))
                        );
                }
                $signup_titles[] = $task->post_title . $date_display;
            }
        } else {
            if ($date = $task->getDate()) {
                $date_display = ' ' . esc_html__('on', 'fdsus')
                    . sprintf(
                        ' <em class="dls-sus-task-date">%s</em>',
                        date(get_option('date_format'), strtotime($date))
                    );
            }
            $signup_titles[] = $task->post_title . $date_display;
        }

        // Build signup title display string
        $last_element = array_pop($signup_titles);
        $signup_titles_str = $last_element;
        if (count($signup_titles) > 0) {
            $signup_titles_str = implode(', ', $signup_titles);
            $signup_titles_str .= ' and ' . $last_element;
        }

        $initial['firstname'] = isset($_POST['signup_firstname']) ? esc_attr($_POST['signup_firstname']) : '';
        $initial['lastname'] = isset($_POST['signup_lastname']) ? esc_attr($_POST['signup_lastname']) : '';
        $initial['email'] = isset($_POST['signup_email']) ? esc_attr($_POST['signup_email']) : '';
        $initial['phone'] = isset($_POST['signup_phone']) ? esc_attr($_POST['signup_phone']) : '';
        $initial['address'] = isset($_POST['signup_address']) ? esc_attr($_POST['signup_address']) : '';
        $initial['city'] = isset($_POST['signup_city']) ? esc_attr($_POST['signup_city']) : '';
        $initial['state'] = isset($_POST['signup_state']) ? esc_attr($_POST['signup_state']) : '';
        $initial['zip'] = isset($_POST['signup_zip']) ? esc_attr($_POST['signup_zip']) : '';

        /**
         * Filter for initial values of sign-up form fields
         *
         * @param array      $initial
         * @param SheetModel $sheet
         * @param TaskModel  $task
         *
         * @return array
         * @since 2.2
         */
        $initial = apply_filters('fdsus_initial_signup_form_values', $initial, $sheet, $task);

        // If not set, but logged in, pull from user
        if (!Settings::isUserAutopopulateDisabled()) {
            $currentUser = wp_get_current_user();
            if (($currentUser instanceof WP_User)) {
                if (empty($initial['firstname'])) {
                    $initial['firstname'] = $currentUser->user_firstname;
                }
                if (empty($initial['lastname'])) {
                    $initial['lastname'] = $currentUser->user_lastname;
                }
                if (empty($initial['email'])) {
                    $initial['email'] = $currentUser->user_email;
                }
            }
        }

        fdsus_the_signup_form_response();

        if (Notice::isContentHidden()) {
            return ob_get_clean();
        }

        $states = new StatesModel;

        $args = array(
            'sheet'               => $sheet,
            'task_id'             => $task_id,
            'signup_titles_str'   => $signup_titles_str,
            'initial'             => $initial,
            'multi_tag'           => $multi_tag,
            'states'              => $states->get(),
        );
        $this->locateTemplate('fdsus/sign-up-form.php', true, false, $args);

        return ob_get_clean();
    }

    /**
     * Process signup form if it needs to be
     *
     * @return bool
     */
    public function maybeProcessSignupForm()
    {
        $task_id = isset($_POST[Id::PREFIX . '_submitted']) ? absint($_POST[Id::PREFIX . '_submitted']) : 0;
        if (empty($task_id) || wp_doing_ajax()) {
            return false;
        }
        if (!isset($_POST['signup_nonce'])
            || !wp_verify_nonce($_POST['signup_nonce'], 'fdsus_signup_submit')
        ) {
            Notice::add('error', esc_html__('Sign-up nonce not valid', 'fdsus'), false, Id::PREFIX . '-signup-nonce-invalid');
            return false;
        }
        $task = new TaskModel($task_id);
        $sheet = new SheetModel($task->post_parent);

        if ($sheet->isValid() && $task->isValid()) {
            if (!$sheet->dlssus_is_active) {
                Notice::add('error', esc_html__('Sign-ups are no longer being accepted for this sheet.', 'fdsus'), false, Id::PREFIX . 'fdsus-signup-nonce-invalid');
                return false;
            }
            if (!$task->dlssus_is_active) {
                Notice::add('error', esc_html__('Sign-ups are no longer being accepted for this task.', 'fdsus'), false, Id::PREFIX . 'fdsus-signup-nonce-invalid');
                return false;
            }
            $this->_processSignupForm($task, $sheet);
        }

        return true;
    }

    /**
     * Process signup form
     *
     * @param TaskModel $task
     * @param SheetModel $sheet
     *
     * @return bool
     */
    private function _processSignupForm($task, $sheet)
    {
        if (wp_doing_ajax()) {
            return false;
        }

        // Pre-process actions
        do_action(Id::PREFIX . '_form_pre_process', $task);
        do_action(Id::PREFIX . "_form_pre_process_{$task->ID}", $task);

        $err = array();
        $successTaskIds = array();
        $successSignupIds = array();
        $signupId = null;

        // Error Handling
        $missingFieldNames = array();
        if (empty($_POST['signup_firstname'])) {
            $missingFieldNames[] = esc_html__('First Name', 'fdsus');
        }
        if (empty($_POST['signup_lastname'])) {
            $missingFieldNames[] = esc_html__('Last Name', 'fdsus');
        }
        if ($sheet->showEmail() && empty($_POST['signup_email'])) {
            $missingFieldNames[] = esc_html__('E-mail', 'fdsus');
        }
        if ($sheet->isPhoneRequired() && $sheet->showPhone() && empty($_POST['signup_phone'])) {
            $missingFieldNames[] = esc_html__('Phone', 'fdsus');
        }
        if ($sheet->isAddressRequired() && $sheet->showAddress()
            && (empty($_POST['signup_address'])
                || empty($_POST['signup_city'])
                || empty($_POST['signup_state'])
                || empty($_POST['signup_zip']))) {
            $missingFieldNames[] = esc_html__('Address', 'fdsus');
        }

        /**
         * Filter error handling on sign-up form
         *
         * @param array      $missingFieldNames
         * @param SheetModel $sheet
         *
         * @return array
         * @since 2.2
         */
        $missingFieldNames = apply_filters('fdsus_sign_up_form_errors_required_fields', $missingFieldNames, $sheet);

        if (!Settings::isAllCaptchaDisabled() && !Settings::isRecaptchaEnabled() && empty($_POST['spam_check'])) {
            $missingFieldNames[] = esc_html__('Math Question', 'fdsus');
        }

        if ($missingFieldNames) {
            $err[] = sprintf(
                /* translators: %s is replaced with a comma separated list of all missing required fields */
                esc_html__('Please complete the following required fields: %s', 'fdsus'),
                implode(', ', $missingFieldNames)
            );
        } elseif (!Settings::isAllCaptchaDisabled() && Settings::isRecaptchaEnabled()
            && empty($_POST['spam_check'])
            && !isset($_POST['double_signup'])
        ) {
            $recaptcha = new ReCaptcha(get_option('dls_sus_recaptcha_private_key'));
            $resp = $recaptcha->setExpectedHostname($_SERVER['HTTP_HOST'])
                ->verify($_POST["g-recaptcha-response"], $_SERVER['REMOTE_ADDR']);
            if (!$resp->isSuccess()) {
                $errors = $resp->getErrorCodes();
                $err[] = esc_html__('Please check that the reCAPTCHA field is valid.', 'fdsus');
            }
            //$missingFieldNames[] = esc_html__('Math Question', 'fdsus');
        } elseif (Settings::isEmailValidationEnabled() && (!filter_var($_POST['signup_email'], FILTER_VALIDATE_EMAIL))) {
            $err[] = esc_html__('Please check that your email address is properly formatted', 'fdsus');
        } elseif (Settings::isEmailValidationEnabled() && !checkdnsrr(substr($_POST['signup_email'], strpos($_POST['signup_email'], '@') + 1), 'MX')) {
            $err[] = esc_html__('Whoops, it looks like your email domain may not be valid.', 'fdsus');
        } elseif (!Settings::isRecaptchaEnabled()
            && (empty($_POST['spam_check']) || (!empty($_POST['spam_check']) && trim($_POST['spam_check']) != '8'))
            && !Settings::isAllCaptchaDisabled()
        ) {
            $err[] = sprintf(
                /* translators: %s is replaced with the users response to the simple captcha */
                esc_html__('Oh dear, 7 + 1 does not equal %s. Please try again.', 'fdsus'),
                esc_attr($_POST['spam_check'])
            );
        } elseif ($this->data->is_honeypot_enabled() && !empty($_POST['website'])) {
            $err[] = esc_html__('Sorry, your submission has been blocked.', 'fdsus');
        }

        if (!$err) {
            // Add Signup
            try {

                if (isset($_POST['signupbox_multi_str'])) {
                    $taskIds = explode(',', $_POST['signupbox_multi_str']);
                } else {
                    $taskIds = array($_GET['task_id']);
                }
                foreach ($taskIds as $taskId) {
                    $errorMsg = '';
                    /**
                     * Filter the error check that runs when processing the signup form before the new signup is added
                     * to the DB
                     *
                     * @param string $errorMsg
                     * @param int    $taskId
                     *
                     * @return string
                     *
                     * @api
                     * @since 2.2
                     */
                    $errorMsg = apply_filters('fdsus_error_before_add_signup', $errorMsg, $taskId);
                    if (is_wp_error($errorMsg)) {
                        throw new Exception($errorMsg->get_error_message());
                    }

                    $signup = new SignupModel();
                    $signupId = $signup->add($_POST, $taskId);

                    $task = new TaskModel($taskId);
                    $successTaskIds[] = (int)$taskId;
                    $successSignupIds[] = (int)$signupId;

                    $sendSignupConfirmationEmail = !empty($_POST['signup_email']);
                    /**
                     * Filter the flag to send the confirmation email on sign-up
                     *
                     * @param bool        $sendSignupConfirmationEmail
                     * @param SheetModel  $sheet
                     * @param TaskModel   $task
                     * @param SignupModel $signup
                     *
                     * @return bool
                     *
                     * @since 2.2.7
                     */
                    $sendSignupConfirmationEmail = apply_filters(
                        'fdsus_send_signup_confirmation_email',
                        $sendSignupConfirmationEmail, $sheet, $task, $signup
                    );

                    if ($sendSignupConfirmationEmail) {
                        $this->mail->send($_POST['signup_email'], $sheet, $task, $signupId, 'signup');
                    }
                }
            } catch (Exception $e) {
                $err[] = $e->getMessage();
            }
        }

        // Set error messages (success are set on sheet object after redirect)
        if (!empty($err)) {
            foreach ($err as $e) {
                Notice::add('warn', $e, false, Id::PREFIX . '-signup-form-err');
            }
        }

        // Post-process actions
        do_action(Id::PREFIX . "_form_post_process", $task, $signupId);
        do_action(Id::PREFIX . "_form_post_process_{$task->ID}", $task, $signupId);

        // If successful, redirect to sheet page
        if (empty($err) && !empty($successTaskIds)) {
            $currentUrl = remove_query_arg(array('task_id', 'action', 'signups', 'remove_spot_task_id', '_susnonce'));
            $currentUrl = add_query_arg(
                array('action' => 'signup', 'status' => 'success', 'tasks' => implode(',', $successTaskIds), 'signups' => implode(',', $successSignupIds)),
                wp_nonce_url($currentUrl, 'signup-success-' . implode(',', $successSignupIds) .'-tasks-' . implode(',', $successTaskIds), '_susnonce')
            );
            wp_redirect(urldecode($currentUrl));
            exit;
        }

    }

}
