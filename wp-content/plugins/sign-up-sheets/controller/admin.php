<?php
/**
 * Admin Class
 */

namespace FDSUS\Controller;

use FDSUS\Id;
use FDSUS\Controller\Migrate as Migrate;
use FDSUS\Model\Data;
use FDSUS\Model\Sheet as SheetModel;
use FDSUS\Model\SignupCollection as SignupCollection;
use FDSUS\Model\Task as TaskModel;
use FDSUS\Model\Signup as SignupModel;
use FDSUS\Model\Settings;
use FDSUS\Lib\Dls\Notice;
use WP_Query;

class Admin
{

    private $data;
    public $table;

    /**
     * Admin constructor
     */
    public function __construct()
    {
        $this->data = new Data();

        if (Id::DEBUG_DISPLAY || get_option('dls_sus_detailed_errors') === 'true') {
            $this->data->detailed_errors = true;
        }

        add_action('admin_head', array(&$this, 'head'));
        if (
            isset($_GET['page'])
            && (strpos($_GET['page'], 'fdsus')) !== false
        ) {
            add_action('admin_footer', array(&$this, 'footer'));
        }
        add_action('admin_init', array(&$this, 'process_sheet_copy'), 11);
        add_action('wp_print_scripts', array(&$this, 'dequeueUnused'), 100);
        add_action('admin_enqueue_scripts', array(&$this, 'add_scripts'), 999);
        add_action('admin_notices', array(&$this, 'admin_notices'), 99999);
        add_action('deleted_post', array(&$this, 'cleanupSheetDeletion'), 10, 2);
        add_action('manage_' . SheetModel::POST_TYPE . '_posts_custom_column', array(&$this, 'add_sheets_columns'), 10, 2);
        add_action('admin_init', array(&$this, 'checkMigrateStatus'), 0);
        add_action('pre_get_posts', array(&$this, 'orderby_task_date'));
        add_filter('plugin_action_links_' . Settings::getCurrentPluginBasename(), array(&$this, 'settingsLink'));
        add_action('in_admin_footer', array(&$this, 'admin_footer_text'), 100);
        add_filter('post_row_actions', array(&$this, 'post_row_actions'), 10, 2);
        add_filter('manage_edit-' . SheetModel::POST_TYPE . '_columns', array(&$this, 'add_sheets_column_header'));
        add_filter('manage_edit-' . SheetModel::POST_TYPE . '_sortable_columns', array(&$this, 'add_sheets_sortable_columns'));
    }

    /**
     * Order by meta value in case of task date sort
     *
     * @param WP_Query $query
     */
    public function orderby_task_date( $query )
    {
        if (!is_admin()) return;

        $orderby = $query->get( 'orderby');

        if ('task_date' == $orderby) {
            $query->set('meta_key','dlssus_date');
            $query->set('orderby','meta_value');
        }
    }

    /**
     * Initial check migrate status on page load
     */
    public function checkMigrateStatus()
    {
        // if migrate is running add notice;
        $migrate = new Migrate();
        $status = $migrate->getStatus();
        if (!in_array($status['state'], array('running', 'rerun'))) return;
        Notice::add('info', esc_html__('Sign-up sheets database upgrade is processing.', 'fdsus'), false, Id::PREFIX . '-migrate-status');
    }

    /**
     * Add sheets column header
     *
     * @param array $columns
     * @return array
     */
    public function add_sheets_column_header($columns)
    {
        unset($columns['date']);
        return array_merge($columns, array(
            'task_date' => esc_html__('Sheet Date', 'fdsus'),
            'total_tasks' => esc_html__('# of Tasks', 'fdsus'),
            'total_spots' => esc_html__('Total Spots', 'fdsus'),
            'filled_spots' => esc_html__('Filled Spots', 'fdsus'))
        );
    }

    /**
     * Add sheets sortable columns.
     *
     * @param $sortable_columns
     *
     * @return mixed
     */
    public function add_sheets_sortable_columns($sortable_columns)
    {
        $sortable_columns['task_date'] = 'task_date';
        return $sortable_columns;
    }

    /**
     * Add sheets columns
     *
     * @param string $column
     * @param int $post_id
     */
    public function add_sheets_columns($column, $post_id)
    {
        $sheet = new SheetModel($post_id);

        switch ($column) {
            case 'task_date':
                if (!empty($sheet->{Id::PREFIX . '_date'})) {
                    echo date('Y-m-d', strtotime($sheet->{Id::PREFIX . '_date'}));
                }
                break;
            case 'total_tasks':
                echo $sheet->dlssus_task_count;
                break;
            case 'total_spots':
                echo $sheet->getTotalSpots();
                break;
            case 'filled_spots':
                echo $sheet->getSignupCount();
                break;
        }
    }

    /**
     * Process sheet copy
     */
    public function process_sheet_copy()
    {
        if (!isset($_GET['action']) || $_GET['action'] != 'copysheet') {
            return;
        }

        $caps = $this->data->get_add_caps_array(SheetModel::POST_TYPE);
        if (!current_user_can($caps['read_post'])) {
            wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'fdsus'));
        }

        if (empty($_GET['sheet_id'])) {
            wp_die(esc_html__('No sheet ID found.', 'fdsus'));
        }

        $sheetId = null;
        if (isset($_GET['sheet_id']) && !is_numeric($_GET['sheet_id'])) {
            wp_die(esc_html__('Invalid sheet ID.', 'fdsus'));
        } else {
            if (isset($_GET['sheet_id'])) {
                $sheetId = (int)$_GET['sheet_id'];
            }
        }

        // Copy Whole Sheet
        $sheet = new SheetModel($sheetId);
        $newSheetId = $sheet->copy();
        if (!is_wp_error($newSheetId)) {
            $newSheetLink = htmlspecialchars_decode(get_edit_post_link($newSheetId));
            header('Location: ' . $newSheetLink);
            exit;
        }
    }

    /**
     * Enqueue plugin css and js files
     */
    public function head()
    {
        // Dequeue meta library scripts for now
        if (wp_script_is('dlsmb-main', $list = 'enqueued')) {
            wp_dequeue_script('dlsmb-main');
        }
        if (wp_style_is('dlsmb-style', $list = 'enqueued')) {
            wp_dequeue_style('dlsmb-style');
        }

        global $post_type;
        $page = isset($_GET['page']) ? $_GET['page'] : '';
        $currentScreen = get_current_screen();
        $isSusPage = 'dlssus_sheet' == $post_type || strpos($page, 'dlssus') === 0;

        if ($isSusPage || $currentScreen->id === 'dashboard') {
            wp_enqueue_style(
                Id::PREFIX . '-admin',
                plugins_url('css/admin.css', dirname(__FILE__)),
                array(),
                Id::VERSION
            );
        }

        if ($isSusPage) {
            // Add back scripts
            wp_enqueue_script('metabox-main-js', plugins_url('lib/dls/meta-boxes/assets/admin.js', dirname(__FILE__)));
            wp_enqueue_style('metabox-main-style', plugins_url('lib/dls/meta-boxes/assets/style.css', dirname(__FILE__)));

            wp_enqueue_script(
                Id::PREFIX . '-admin',
                plugins_url('js/admin.js', dirname(__FILE__)),
                array(
                    'jquery',
                    'post',
                    Id::PREFIX . '-jquery-comments',
                    'metabox-main-js'
                ),
                Id::VERSION,
                true
            );

            wp_enqueue_script('post');

            wp_enqueue_script(
                Id::PREFIX . '-jquery-comments',
                plugins_url('js/jquery.comments.js', dirname(__FILE__)),
                array( 'jquery' ),
                Id::VERSION,
                true
            );
        }
    }

    /**
     * Dequeue Unused items
     */
    public function dequeueUnused()
    {
        // Remove timepicker since it's unused in SUS (to prevent conflicts)
        wp_dequeue_script('dlsmb-timepicker');
    }

    /**
     * Add to admin footer
     */
    public function footer()
    {
    }

    /**
     * Enqueue admin scripts
     */
    function add_scripts()
    {
        // Dequeue meta library scripts for now
        if (wp_script_is('dlsmb-main', $list = 'enqueued')) {
            wp_dequeue_script('dlsmb-main');
        }
        if (wp_style_is('dlsmb-style', $list = 'enqueued')) {
            wp_dequeue_style('dlsmb-style');
        }
        if (wp_style_is('dlsmb-jquery-ui', $list = 'enqueued')) {
            wp_dequeue_style('dlsmb-jquery-ui');
        }

        // Enqueue only on SUS pages
        if (get_post_type() == SheetModel::POST_TYPE) {
            wp_enqueue_script('jquery-ui-datepicker');
            wp_enqueue_script('jquery-ui-sortable');
            wp_enqueue_style('dlsmb-jquery-ui');
        }
    }

    /**
     * Customize post row actions for sheets
     *
     * @param array $actions
     * @param object $post
     *
     * @return mixed
     */
    public function post_row_actions($actions, $post)
    {
        if ($post->post_type == SheetModel::POST_TYPE) {

            $id = array(Id::PREFIX . '-id' => sprintf(
                '<span class="' . Id::PREFIX . '-id-value">' . esc_html__('ID', 'fdsus') . ': %s</span>',
                $post->ID
            ));
            $actions = $id + $actions;

            if ($post->post_status != 'trash') {
                $actions[Id::PREFIX . '-manage'] = sprintf(
                    '<a href="%s" title="" rel="permalink">%s</a>',
                    admin_url(
                        add_query_arg(
                            array(
                                'post_type' => SheetModel::POST_TYPE,
                                'page'      => Id::PREFIX . '-manage',
                                'sheet_id'  => (int)$post->ID
                            ), 'edit.php'
                        )
                    ),
                    esc_html__('Manage Sign-ups', 'fdsus')
                );

                $actions[Id::PREFIX . '-copy'] = sprintf(
                    '<a href="%s" title="" rel="permalink">%s</a>',
                    admin_url(
                        add_query_arg(
                            array(
                                'post_type' => SheetModel::POST_TYPE,
                                'page'      => Id::PREFIX . '-manage',
                                'sheet_id'  => (int)$post->ID,
                                'action'    => 'copysheet'
                            ), 'edit.php'
                        )
                    ),
                    esc_html__('Copy', 'fdsus')
                );
            }
        }

        return $actions;
    }

    /**
     * Include custom footer
     */
    public function admin_footer_text()
    {
        $showFooterText = false;
        if (
            (isset($_REQUEST['page'])
                && (strpos($_REQUEST['page'], 'dls-sus-') === 0
                    || strpos($_REQUEST['page'], Id::PREFIX) === 0
                )
            )
            || (get_post_type() == SheetModel::POST_TYPE
                || get_post_type() == TaskModel::POST_TYPE
                || get_post_type() == SignupModel::POST_TYPE
            )
        ) {
            $showFooterText = true;
        }

        /**
         * Filter to show footer text in the admin
         *
         * @param bool $showFooterText
         *
         * @return bool
         * @since 2.2
         */
        $showFooterText = apply_filters('fdsus_show_admin_footer_text', $showFooterText);

        if (!$showFooterText) {
            return;
        }

        $supportUrl = Id::isPro()
            ? 'https://www.fetchdesigns.com/forums/forum/sign-up-sheets-support/'
            : 'https://wordpress.org/support/plugin/sign-up-sheets/';
        ?>
        <footer class="fdsus-footer" aria-label="Sign-up Sheet">
            <p>
                <?php esc_html_e('Made by', 'fdsus'); ?> Fetch Designs
                <span class="fdsus-footer-getpro"></span>
                <?php if (!Id::isPro()): ?>
                    <a href="https://www.fetchdesigns.com/sign-up-sheets-wordpress-plugin/"><?php esc_html_e('Get Pro', 'fdsus'); ?></a>
                    &nbsp;&nbsp;|&nbsp;&nbsp;
                <?php endif; ?>
                <a href="<?php echo esc_url($supportUrl); ?>">
                    <span><?php esc_html_e('Need help? Get Support &raquo;', 'fdsus'); ?></span>
                </a>
            </p>
            <hr>
        </footer>
        <?php
    }

    /**
     * Add settings link on plugin page
     *
     * @param array $links
     *
     * @return array
     */
    function settingsLink($links)
    {
        $this->data->getSettingsUrl();
        $settingsLink = sprintf( '<a href="%s">%s</a>',
            admin_url('edit.php?post_type=dlssus_sheet&page=dlssus-settings'),
            esc_html__('Settings', 'fdsus')
        );
        array_unshift($links, $settingsLink);

        return $links;
    }

    /**
     * Cleanup orphaned signups and tasks after permanently deleting a sheet
     *
     * @param $postId
     */
    public function cleanupSheetDeletion($postId, $post)
    {
        global $post_type;

        switch ($post_type) {

            case SheetModel::POST_TYPE:
                $sheet = new SheetModel($post);
                $tasks = $sheet->getTasks();
                foreach ($tasks as $task) {
                    $signups = $task->getSignups();
                    foreach ($signups as $signup) {
                        $signup->delete();
                    }
                    $task->delete();
                }
                break;

            case TaskModel::POST_TYPE:
                $signupCollection = new SignupCollection();
                $signups = $signupCollection->getByTask($postId);
                foreach ($signups as $signup) {
                    $signup->delete();
                }
                $taskModel = new TaskModel();
                $taskModel->delete($postId);
                break;

        }
    }

    /**
     * Display admin notices
     */
    public function admin_notices()
    {
        $notices = get_transient(Id::PREFIX . '_admin_notices');
        if (empty($notices)) {
            return;
        }
        foreach ($notices as $type => $messages) {
            foreach ($messages as $message) {
                ?>
                <div class="<?php echo $type; ?>">
                    <p><?php echo $message; ?></p>
                </div>
                <?php
            }
        }
        delete_transient(Id::PREFIX . '_admin_notices');
    }

}
