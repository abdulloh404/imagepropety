<?php

use CodeIgniter\Controller;


class BannerUpload extends CI_Controller
{


    /**
     * Manage __construct
     *
     * @return Response
     */

    public function __construct()
    {
        parent::__construct();
        $this->load->helper(array('form', 'url'));
    }


    /**
     * Manage index
     *
     * @return Response
     */

    public function index()
    {

        $this->load->view('BannerUploadForm', array('error' => ''));
    }


    /**
     * Manage uploadBanner
     *
     * @return Response
     */

    public function BannerAdd()
    {

        $config['upload_path']   = './public/upload/tb_banners/';
        $config['allowed_types'] = 'gif|jpg|png';
        $config['max_size']      = 1024;
        $this->load->library('upload', $config);

        if (!$this->upload->do_upload('banner')) {
            $error = array('error' => $this->upload->display_errors());
            $this->load->view('BannerUploadForm', $error);
        } else {
            print_r('Banner Uploaded Successfully.');
            exit;
        }
    }
}