<?php

namespace wpf\wp\facades;

use wpf\helpers\ArrayHelper;
use wpf\helpers\Date;
use wpf\helpers\WP;

/**
 * Class File.
 */
final class File extends Post
{
    private $_type_info;
    private $_file_type;
    private $path;

    public $meta_visibility = true;
    public $is_annex = false;
    public $placement_date_field = 'placement_date';

    /**
     * File constructor.
     *
     * @param int|string|WP_Post|null $post
     */
    public function __construct($post = null)
    {
        parent::__construct($post);

        $this->_path = get_attached_file($this->ID);

        // Тип файла
        $this->_type_info = wp_check_filetype($this->_path);
        $this->_file_type = wp_ext2type($this->_type_info['ext']);
    }

    /**
     * @return bool
     */
    public function exists(): bool
    {
        return file_exists($this->path);
    }

    /**
     * @return string
     */
    public function getExt(): string
    {
        return $this->_type_info['ext'];
    }

    /**
     * @return string
     */
    public function getMimeType(): string
    {
        return $this->_type_info['type'];
    }

    /**
     * @return Date
     */
    public function pubDate()
    {
        $field_value = get_field($this->placement_date_field, $this->post);

        return new Date($field_value || $this->post_date);
    }

    public function getSize(): string
    {
        if (!$this->exists()) {
            return null;
        }

        return size_format(filesize($this->path));
    }

    /**
     * @return mixed
     */
    public function title()
    {
        return $this->post_excerpt ?: $this->post_title;
    }

    /**
     * @return mixed
     */
    public function url()
    {
        return wp_get_attachment_url($this->ID);
    }

    /**
     * @param bool $meta_visibility
     *
     * @return array|bool
     */
    public function getAnnexes($meta_visibility = true)
    {
        if ($this->is_annex || !$this->annexes) {
            return false;
        }
        $ids = wp_list_pluck($this->annexes, 'file');
        $annexes = [];
        foreach ($ids as $id) {
            $d = new self($id);
            $d->is_annex = true;
            $annexes[] = $d;
        }

        return $annexes;
    }

    /**
     * @return string
     */
    public function getIconClass()
    {
        $icons = app()->files['icons'];
        $prefix = app()->files['icons_prefix'];
        $uniq = $icons['default'] ?? false;
        if (isset($icons[$this->_file_type])) {
            $needed = $icons[$this->_file_type];
            if (ArrayHelper::isAssociative($needed)) {
                $uniq = $needed[$this->ext] ?? $needed['default'] ?? $uniq;
            }
        }

        return $uniq ? ($prefix . $uniq) : null;
    }

    /**
     * @param array $data
     *
     * @return int|WP_Error
     */
    public static function create(array $data)
    {
        $file = ArrayHelper::remove($data, 'file');
        if (is_null($file) || !ArrayHelper::isAssociative($file)) {
            return WP::error('missing_file',
        "The 'file' parameter can't sent. It should be an array like ['name', 'type', 'tmp_name', 'error', 'size'].");
        }

        require_once ABSPATH . 'wp-admin/includes/image.php';
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';

        $upload = wp_handle_upload($file, ['test_form' => false]);

        // $filename should be the path to a file in the upload directory.
        $filename = $upload['file'];

        // Check the type of tile. We'll use this as the 'post_mime_type'.
        $filetype = wp_check_filetype(basename($filename), null);

        // Get the path to the upload directory.
        $wp_upload_dir = wp_upload_dir();

        // Prepare an array of post data for the attachment.
        $data['guid'] = $wp_upload_dir['url'] . '/' . basename($filename);
        $data['post_mime_type'] = $filetype['type'];
        $data['post_status'] = 'inherit';
        $data['post_title'] = isset($data['post_title'])
      ? wp_strip_all_tags($data['post_title'])
      : preg_replace('/\.[^.]+$/', '', basename($filename));

        // Insert the attachment.
        $post_id = ArrayHelper::remove($data, 'post_id', 0);
        $result = wp_insert_attachment($data, $filename, $post_id, true);

        // Generate the metadata for the attachment, and update the database record.
        if (is_int($result)) {
            $attach_id = $result;
            $meta = wp_generate_attachment_metadata($attach_id, $filename);
            wp_update_attachment_metadata($attach_id, $meta);
        }

        return $result;
    }
}
