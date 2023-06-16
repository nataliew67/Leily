<?php
/**
 * Setting: Sheet Order Model
 */

namespace FDSUS\Model\Settings;

class SheetOrder
{
    /** @var array  */
    protected $options = array();

    /** @var  */
    protected $value;

    /** @var string  */
    protected $defaultOptionKey = 'date_asc';

    public function __construct()
    {
        $this->options = array(
            'date_asc'  => array(
                'label'     => esc_html__('Date (ASC)', 'fdsus'),
                'direction' => 'ASC',
                'sort_by'   => 'dlssus_end_date',
            ),
            'date_desc' => array(
                'label'     => esc_html__('Date (DESC)', 'fdsus'),
                'direction' => 'DESC',
                'sort_by'   => 'dlssus_end_date',
            ),
            'id_asc'    => array(
                'label'     => esc_html__('Sheet ID (ASC)', 'fdsus'),
                'direction' => 'ASC',
                'sort_by'   => 'ID',
            ),
            'id_desc'   => array(
                'label'     => esc_html__('Sheet ID (DESC)', 'fdsus'),
                'direction' => 'DESC',
                'sort_by'   => 'ID',
            ),
        );
    }

    /**
     * Get option
     *
     * @return false|mixed|null
     */
    public function get()
    {
        $this->value = get_option('dls_sus_sheet_order');

        if ($this->value === false) {
            $this->value = $this->defaultOptionKey;
        }

        return $this->cleanDeprecatedValues($this->value);
    }

    /**
     * Options array
     *
     * @return array
     */
    public function options()
    {
        $optionsArray = array();
        foreach ($this->options as $key => $option) {
            $optionsArray[$key] = $option['label'];
        }

        return $optionsArray;
    }

    /**
     * Get sort direction
     *
     * @param $key
     *
     * @return false|mixed
     */
    public function direction($key = null)
    {
        if (is_null($key)) {
            $key = $this->get();
        }

        return !empty($this->options[$key]) ? $this->options[$key]['direction'] : false;
    }

    /**
     * Get sort by
     *
     * @param $key
     *
     * @return false|mixed
     */
    public function sortBy($key = null)
    {
        if (is_null($key)) {
            $key = $this->get();
        }

        return !empty($this->options[$key]) ? $this->options[$key]['sort_by'] : false;
    }

    /**
     * Clean deprecated values and save as new ones
     *
     * @param mixed $value
     *
     * @return mixed|string
     */
    private function cleanDeprecatedValues($value)
    {
        if ($value === '0') {
            $this->value = 'date_asc';
        } elseif ($value === '1') {
            $this->value = 'id_asc';
        }

        if ($value !== $this->value) {
            update_option('dls_sus_sheet_order', $this->value);
            return $this->value;
        }

        return $value;
    }
}
