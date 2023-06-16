<?php
/**
 * Signup Collection
 */

namespace FDSUS\Model;

use FDSUS\Id;
use FDSUS\Model\Signup as SignupModel;
use WP_Query;
use WP_Post;

if (Id::isPro()) {
    class SignupCollectionParent extends Pro\SignupCollection {}
} else {
    class SignupCollectionParent extends Base {}
}

class SignupCollection extends SignupCollectionParent
{
    /** @var WP_POST[]|SignupModel[] */
    public $posts = array();

    /** @var WP_Query */
    protected $query;

    /**
     * Constructor
     *
     * @param int   $taskId
     * @param array $args for get_posts()
     *
     * @return self
     */
    public function __construct($taskId = 0, $args = array())
    {
        parent::__construct();

        return $this;
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
        $this->query = new WP_Query;
        $this->posts = $this->query->query($args);
        $this->convertToCustomObjects();
    }

    /**
     * Get by task id
     *
     * @param int   $taskId
     * @param array $args for get_posts()
     *
     * @return SignupModel[]
     */
    public function getByTask($taskId = 0, $args = array())
    {
        if (empty($taskId)) {
            return $this->posts;
        }

        $defaults = array(
            'posts_per_page'   => -1,
            'post_type'        => SignupModel::POST_TYPE,
            'post_parent'      => $taskId,
            'post_status'      => 'publish',
            'suppress_filters' => true,
            'orderby'          => 'date, ID',
            'order'            => 'ASC',
        );
        $args = wp_parse_args($args, $defaults);
        $this->init($args);

        return $this->posts;
    }

    /**
     * Get by task id
     *
     * @param string $email
     * @param array  $args for get_posts()
     *
     * @return SignupModel[]
     */
    public function getByEmail($email = '', $args = array())
    {
        if (empty($email)) {
            return $this->posts;
        }

        $defaults = array(
            'posts_per_page'   => -1,
            'post_type'        => SignupModel::POST_TYPE,
            'post_status'      => 'publish',
            'suppress_filters' => true,
            'orderby'          => 'date, ID',
            'order'            => 'ASC',
            'meta_query' => array(
                array(
                    'key'     => 'dlssus_email',
                    'value'   => $email
                ),
            ),
        );
        $args = wp_parse_args($args, $defaults);
        $this->init($args);

        return $this->posts;
    }

    /**
     * @return int
     */
    public function getMaxNumPages()
    {
        return $this->query->max_num_pages;
    }

    /**
     * Convert standard WP_Post to custom object
     */
    public function convertToCustomObjects()
    {
        /**
         * @var int     $key
         * @var WP_Post $post
         */
        foreach ($this->posts as $key => $post) {
            $this->posts[$key] = new SignupModel($post);
        }
    }
}
