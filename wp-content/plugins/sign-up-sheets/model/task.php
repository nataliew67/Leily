<?php
/**
 * Task Model
 */

namespace FDSUS\Model;

use FDSUS\Id;
use WP_Post;

if (Id::isPro()) {
    class TaskParent extends Pro\Task {}
} else {
    class TaskParent extends TaskBase {}
}

class Task extends TaskParent
{
    /**
     * Constructor
     *
     * @param int|WP_Post $taskId id or post object
     */
    public function __construct($taskId = 0)
    {
        parent::__construct($taskId);
    }
}