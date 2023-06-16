<?php
/**
 * Admin Custom MetaBox
 *
 * To use, run the following within the page output of the admin page you want to output the metabox...
 *
 * // Get screen id
 * $screen = get_current_screen();
 *
 * // Init metabox class
 * $metabox = new MetaBoxController($screen->id);
 *
 * // Add the metabox to the system for later output (use add() multiple times for more than one)
 * $metabox->add(array(
 *      'id' => 'sheet',
 *      'title' => esc_html__('Sign-up Sheet', 'fdsus'),
 *      'order' => 10,
 *      'options' => array(
 *          'label'    => 'Display Label',
 *          'name'     => 'field_name',
 *          'type'     => 'text', // Field type
 *          'note'     => 'Optional note',
 *          'options'  => array(), // optional array for select and multi-checbox/radio type fields
 *          'order'    => 10, // sort order
 *          'class'    => 'some-class' // adds class to surrounding <tr> element
 *          'disabled' => false, // mark input field as disabled
 *          'value'    => '' // optional value that would override the default get_option() value pulled in this class
 *      )
 * ));
 *
 * // Output the metaboxes (use only ONCE)
 * $metabox->output();
 *
 * Tips:
 *  - make sure to wrap your output with the CSS class `metabox-holder` somewhere in a parent element
 *  - make sure to include this JS on the page `wp_enqueue_script( 'post' );`
 */

namespace FDSUS\Controller\Admin;

class MetaBox
{
    public $screenId;
    public $id = '';
    public $title = '';
    public $args = array();

    /**
     * Construct
     *
     * @param int $screenId
     */
    public function __construct($screenId)
    {
        $this->screenId = $screenId;
        add_action('add_meta_boxes_' . $screenId, array($this, 'addFromAction'));
    }

    /**
     * Output
     */
    public function output()
    {
        do_meta_boxes($this->screenId, 'normal', '');
    }

    /**
     * Add
     *
     * @param array $args ['id', 'title', 'options']
     */
    public function add($args)
    {
        do_action('add_meta_boxes_' . $this->screenId, $args);
    }

    /**
     * Add from action
     *
     * @param array $args
     */
    public function addFromAction($args)
    {
        /**
         * @var int|bool $id
         * @var string   $title
         * @var array    $options
         */
        extract(
            shortcode_atts(
                array(
                    'id'      => '',
                    'title'   => '',
                    'options' => array(),
                ), $args
            )
        );

        add_meta_box(
            $id,
            $title,
            array($this, 'content'),
            $this->screenId,
            'normal',
            'default',
            array('options' => $options)
        );
    }

    /**
     * Content
     *
     * @param string $noidea unused
     * @param array  $callbackData
     */
    public function content($noidea, $callbackData)
    {
        if (!empty($callbackData['args'])
            && !empty($callbackData['args']['options'])
            && !is_array($callbackData['args']['options'])) {
            return;
        }
        ?>
        <table class="form-table">
            <?php
            foreach ($callbackData['args']['options'] as $o) :
                if (!isset($o['label'])) {
                    $o['label'] = isset($o[0]) ? $o[0] : null;
                }
                if (!isset($o['note'])) {
                    $o['note'] = isset($o[3]) ? $o[3] : null;
                }
                ?>
                <tr<?php if (!empty($o['class'])) echo ' class="' . esc_attr($o['class']) . '"'; ?>>
                    <th scope="row"><?php echo wp_kses_post($o['label']); ?>:</th>
                    <td>
                        <?php $this->displayFieldByType($o); ?>
                        <span class="description"><?php echo wp_kses_post($o['note']); ?></span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
        <?php
    }

    /**
     * Display field by type
     *
     * @param array|string $o
     * @param string|null  $parentName
     * @param string|null  $value
     */
    public function displayFieldByType($o, $parentName = null, $value = null)
    {
        // Set variables
        if (!isset($o['label'])) {
            $o['label'] = isset($o[0]) ? $o[0] : null;
        }
        if (!isset($o['name'])) {
            $o['name'] = isset($o[1]) ? $o[1] : null;
        }
        if (!empty($parentName)) {
            $o['name'] = $parentName . '[' . $o['name'] . ']';
        }
        if (!isset($o['type'])) {
            $o['type'] = isset($o[2]) ? $o[2] : null;
        }
        if (!isset($o['options'])) {
            $o['options'] = isset($o[4]) ? $o[4] : array();
        }
        $value = (!empty($value)) ? $value : (isset($o['value']) ? $o['value'] : get_option($o['name']));
        $disabled = !empty($o['disabled']) ? ' disabled' : '';

        // Output by type
        switch ($o['type']) {
            case 'text':
                echo '<input type="text" id="'.$o['name'].'" name="'.$o['name'].'" value="'.esc_attr($value).'" size="20"' . $disabled . '>';
                break;
            case 'checkbox':
                echo '<input type="checkbox" id="'.$o['name'].'" name="'.$o['name'].'" value="true"'.(($value === 'true') ? ' checked="checked"' : '') . $disabled . '>';
                break;
            case 'checkboxes':
                $i=0;
                foreach ($o['options'] AS $k=>$v) {
                    $checked = (is_array($value) && in_array($k, $value)) ? ' checked="checked"' : '';
                    echo '<input type="checkbox" name="'.$o['name'].'[]" value="'.$k.'"'.$checked.' id="'.$o['name'].'-'.$i.'"' . $disabled . '>';
                    echo ' <label for="'.$o['name'].'-'.$i.'">'.$v.'</label><br />';
                    $i++;
                }
                break;
            case 'textarea':
                echo '<textarea id="'.$o['name'].'" name="'.$o['name'].'" rows="8" style="width: 100%;"' . $disabled . '>'.esc_attr($value).'</textarea>';
                break;
            case 'dropdown':
                echo '<select id="'.$o['name'].'" name="'.$o['name'].'"' . $disabled . '>';
                foreach ($o['options'] AS $k=>$v) {
                    $selected = ($value == $k) ? ' selected="selected"' : '';
                    echo '<option value="'.$k.'"'.$selected.'>'.$v.'</option>';
                }
                echo '</select>';
                break;
            case 'multiselect':
                echo '<select multiple="multiple" class="chosen-select" id="'.$o['name'].'" name="'.$o['name'].'[]"' . $disabled . '>';
                foreach ($o['options'] AS $k=>$v) {
                    $selected = (is_array($value) && in_array($k, $value)) ? ' selected="selected"' : '';
                    echo '<option value="'.$k.'"'.$selected.'>'.$v.'</option>';
                }
                echo '</select>';
                break;
            case 'button':
                echo sprintf(
                    '<a href="%s" class="button button-secondary %s" id="%s"%s%s>%s</a>',
                    $o['options']['href'],
                    (!empty($o['options']['class'])) ? esc_attr($o['options']['class']) : null,
                    $o['name'],
                    (!empty($o['options']['target'])) ? ' target="' . esc_attr($o['options']['target']) . '"' : null,
                    (!empty($o['options']['onclick'])) ? ' onclick="' . esc_attr($o['options']['onclick']) . '"' : null,
                    $o['label']
                );
                break;
            case 'repeater':
                echo '</td><tr' . (!empty($o['class']) ? ' class="' . esc_attr($o['class']) . '"' : '') . '><td colspan="2"><table class="dls-sus-repeater">';

                echo '<tr>';
                if (!empty($o['options'])) {
                    foreach ($o['options'] AS $k=>$v) {
                        $description = (!empty($v['note'])) ? ' <span class="description">'.$v['note'].'</span>' : null;
                        echo '<th class="'.$o['name'].'_'.$k.'">'.$v['label'].$description.'</th>';
                    }
                }
                echo '</tr>';

                if (!empty($value)) {
                    foreach ($value as $val_k=>$val_v) {
                        echo '<tr>';
                        foreach ($o['options'] AS $k=>$v) {
                            echo '<td class="'.$o['name'].'_'.$k.'">';
                            if (!isset($value[$val_k][$v['name']])) $value[$val_k][$v['name']] = null;
                            $this->displayFieldByType($v, $o['name'].'['.$val_k.']', $value[$val_k][$v['name']]);
                            echo '</td>';
                        }
                        echo '</tr>';
                    }
                }

                echo '<tr>';
                if (!empty($o['options'])) {
                    foreach ($o['options'] AS $k=>$v) {
                        echo '<td class="'.$o['name'].'_'.$k.'">';
                        if (!isset($val_k)) $val_k = 0;
                        $this->displayFieldByType($v, $o['name'].'['.($val_k+1).']');
                        echo '</td>';
                    }
                }
                echo '</tr>';
                echo '</table>';
        }
    }
}
