<?php
/**
 * Task Collection
 */

namespace FDSUS\Model;

use FDSUS\Model\Task as TaskModel;
use WP_Query;
use WP_Post;

class TaskCollection extends Base
{
    /** @var TaskModel[] */
    public $posts = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        return $this;
    }

    /**
     * Get by sheet id
     *
     * @param int   $sheetId
     * @param array $args for get_posts()
     *
     * @return TaskModel[]
     */
    public function getBySheetId($sheetId, $args = array())
    {
        $defaults = array(
            'posts_per_page'   => -1,
            'post_type'        => TaskModel::POST_TYPE,
            'post_parent'      => $sheetId,
            'post_status'      => 'publish',
            'suppress_filters' => true,
        );
        $args = wp_parse_args($args, $defaults);
        $this->init($args);

        return $this->posts;
    }

    /**
     * Get posts and convert to custom object
     *
     * @param array $args for get_posts()
     *
     * @return array|void
     */
    private function init($args)
    {
        $query = new WP_Query;
        $this->posts = $query->query($args);
        $this->convertToCustomObjects();
        usort($this->posts, array(&$this, 'orderTaskBySort'));
    }

    /**
     * Order an array of tasks by the "sort" field
     * Works as part of usort()
     * Ex: usort( $tasks, array( &$this, '_order_task_by_sort') );
     *
     * @param $a
     * @param $b
     *
     * @return int
     */
    private function orderTaskBySort($a, $b)
    {
        return (int)$a->dlssus_sort - (int)$b->dlssus_sort;
    }

    /**
     * Convert standard WP_Post to custom objects
     */
    public function convertToCustomObjects()
    {
        /**
         * @var int     $key
         * @var WP_Post $post
         */
        foreach ($this->posts as $key => $post) {
            $this->posts[$key] = new TaskModel($post);
        }
    }
}
