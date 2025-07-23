<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Category extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('wikiarticles_model');
        $this->load->model('wikibooks_model');
        $this->load->model('Wikicategory_model');
    }

    public function index()
    {
        $data['title'] = _l('wiki_category');
        $this->load->view('wiki_category', $data);
    }

    public function category_save()
    {
        if ($this->input->post()) {
            $data = $this->input->post();

            $id = $data['category_hid'];
            unset($data['category_hid']);

            $res = [];
            if (is_numeric($id) && $id != "") {
                $this->Wikicategory_model->update($id, $data);
                $res = ['success' => true, 'message' => _l('category_updated_successfully'),];
            } else {
                $this->Wikicategory_model->add($data);
                $res = ['success' => true, 'message' => _l('category_added_successfully'),];
            }

            echo json_encode($res);
        }
    }

    public function get_category()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('wiki', 'table/category'));
        }
    }

    public function edit_category($id)
    {
        if (is_numeric($id) && $id != "") {
            $data = $this->Wikicategory_model->edit_category($id);

            $res = [];
            if (!empty($data)) {
                $res = ['success' => true, 'data' => $data];
            } else {
                $res = ['success' => false, 'message' => _l('Category not found')];
            }

            echo json_encode($res);
        }
    }

    public function delete_category($id)
    {
        if (is_numeric($id) && $id != "") {
            $data = $this->Wikicategory_model->delete_category($id);

            $res = [];
            if ($data) {
                $res = ['success' => true, 'message' => _l('category_deleted_successfully'),];
            } else {
                $res = ['success' => false, 'message' => _l('fail_to_delete'),];
            }

            echo json_encode($res);
        }
    }
}
