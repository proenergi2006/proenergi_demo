<?php
class pagination_bootstrap
{
    protected $base_url        = '';
    protected $getparams    = array();
    protected $total_rows     = 0;
    protected $num_pages     = 0;
    protected $num_links     = 4;
    protected $per_page     = 10;
    protected $cur_page     = 0;

    protected $data_page_attr         = 'data-pagination-page';
    protected $num_links_responsive = true;
    protected $encrypt_params         = true;
    protected $pageonly             = false;

    protected $first_link         = '<i class="fa fa-angle-double-left"></i>';
    protected $last_link         = '<i class="fa fa-angle-double-right"></i>';
    protected $prev_link         = '<i class="fa fa-angle-left"></i>';
    protected $next_link         = '<i class="fa fa-angle-right"></i>';


    public function __construct($params = array())
    {
        $this->initialize($params);
    }

    public function initialize(array $params = array())
    {
        foreach ($params as $key => $val) {
            if (property_exists($this, $key)) {
                $this->$key = $val;
            }
        }

        if ($this->total_rows > 0) {
            $this->num_pages     = (int) ceil($this->total_rows / $this->per_page);
            $this->num_links     = (int) $this->num_links;

            if (count($this->getparams) == 0) {
                $this->getparams['per_page'] = $this->per_page;
                $this->getparams['page']     = '';
            } else {
                $this->getparams['per_page'] = $this->per_page;
            }
            unset($this->getparams['_pjax']);

            $this->cur_page = $this->getparams['page'];
            if (!ctype_digit($this->cur_page) || (int) $this->cur_page === 0) {
                $this->cur_page = 1;
            } else {
                $this->cur_page = (int) $this->cur_page;
            }
            if ($this->cur_page > $this->num_pages) {
                $this->cur_page = $this->num_pages;
            }
        } else {
            $this->cur_page = 1;
        }

        return (!$this->pageonly ? $this : array("num_pages" => $this->num_pages, "num_links" => $this->num_links, "cur_page" => $this->cur_page, "per_page" => $this->per_page));
    }

    public function create_links_bootstrap()
    {
        if (($this->total_rows == 0 || $this->per_page == 0) || ($this->num_pages === 1)) {
            return '';
        }

        if ($this->num_links < 0) {
            show_error('Your number of links must be a non-negative number.');
        }

        $uri_page_number = $this->cur_page;

        $start    = (($this->cur_page - $this->num_links) > 0) ? $this->cur_page - ($this->num_links - 1) : 1;
        $end    = (($this->cur_page + $this->num_links) < $this->num_pages) ? $this->cur_page + $this->num_links : $this->num_pages;


        $base_url     = trim($this->base_url);
        $output     = '';

        if ($this->first_link !== FALSE) {
            $attributes = sprintf('%s="%d"', $this->data_page_attr, 1);

            $prmsep = '?';
            $prmtmp = http_build_query(array_merge($this->getparams, array('page' => ''))) . '1';
            $append = $prmsep . $prmtmp;
            $output .= '<li class="page-item"><a href="' . $base_url . $append . '"' . $attributes . '><span class="page-navigation">' . $this->first_link . '</span></a></li>';
        }

        if ($this->prev_link !== FALSE) {
            $i = $uri_page_number - 1;
            $attributes = sprintf('%s="%d"', $this->data_page_attr, ($this->cur_page - 1));

            if ($this->cur_page === 1) {
                $i = 1;
            }

            $prmsep = '?';
            $prmtmp = http_build_query(array_merge($this->getparams, array('page' => ''))) . $i;
            $append = $prmsep . $prmtmp;
            $output .= '<li class="page-item"><a href="' . $base_url . $append . '"' . $attributes . '><span class="page-navigation">' . $this->prev_link . '</span></a></li>';
        }

        for ($loop = $start - 1; $loop <= $end; $loop++) {
            $i = $loop;
            $attributes = sprintf('%s="%d"', $this->data_page_attr, $loop);

            if ($i >= 1) {
                if ($this->cur_page === $loop) {
                    $output .= '<li class="page-item active"><span class="page-link">' . $loop . '</span></li>';
                } else {
                    $prmsep = '?';
                    $prmtmp = http_build_query(array_merge($this->getparams, array('page' => ''))) . $i;
                    $append = $prmsep . $prmtmp;
                    $output .= '<li class="page-item"><a href="' . $base_url . $append . '"' . $attributes . '><span class="page-link">' . $loop . '</span></a></li>';
                }
            }
        }

        if ($this->next_link !== FALSE) {
            $i = $this->cur_page + 1;
            $attributes = sprintf('%s="%d"', $this->data_page_attr, $this->cur_page + 1);

            if ($this->cur_page == $this->num_pages) {
                $i = $this->num_pages;
            }

            $prmsep = '?';
            $prmtmp = http_build_query(array_merge($this->getparams, array('page' => ''))) . $i;
            $append = $prmsep . $prmtmp;
            $output .= '<li class="page-item"><a href="' . $base_url . $append . '"' . $attributes . '><span class="page-navigation">' . $this->next_link . '</span></a></li>';
        }

        if ($this->last_link !== FALSE) {
            $i = $this->num_pages;
            $attributes = sprintf('%s="%d"', $this->data_page_attr, $this->num_pages);

            $prmsep = '?';
            $prmtmp = http_build_query(array_merge($this->getparams, array('page' => ''))) . $i;
            $append = $prmsep . $prmtmp;
            $output .= '<li class="page-item"><a href="' . $base_url . $append . '"' . $attributes . '><span class="page-navigation">' . $this->last_link . '</span></a></li>';
        }

        $output = preg_replace('#([^:"])//+#', '\\1/', $output);
        return '<ul class="pagination">' . $output . '</ul>';
    }

    public function create_info_bootstrap()
    {
        if (($this->total_rows == 0 || $this->per_page == 0)) {
            return '';
        }
        return $this->total_rows . ' data ditampilkan dalam ' . $this->num_pages . ' halaman';
    }

    public function get_offset()
    {
        return ($this->cur_page - 1) * $this->per_page;
    }
}
