<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Komponen extends CI_Controller {

	/**
	 * constructor
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->library('template');
		$this->load->model(array('komponen_model','perangkat_model'));
		//$this->output->enable_profiler(true);

		//check authentication
		$this->auth();
	}

	/**
	 * get doc values from input form
	 * @return [type] [description]
	 */
	public function _get_doc()
	{
		$data = array(
			'name' => $this->input->post('name'),
			'spec' => $this->input->post('spec'),
			'id_perangkat' => $this->input->post('id_perangkat'),
		);

		return $data;
	}
	
	/**
	 * Load Page Index
	 * @return [type] [description]
	 */
	public function index()
	{
		//get all record
		$data['models'] = $this->komponen_model->get_all_with_relation();
		$this->template->set_navbar('templates/navbar');
		$this->template->load('main', 'komponen/index', $data);
	}

	/**
	 * Load Page Insert
	 * @return [type] [description]
	 */
	public function insert()
	{
		//set navbar
		$this->template->set_navbar('templates/navbar');

		//set rules form validation
		$this->_set_validation();
		//get perangkat list
		$data['perangkat'] = $this->perangkat_model->get_all();

		if($this->form_validation->run() == FALSE)
		{
			//if error
			$this->template->load('main', 'komponen/insert', $data);			
		}
		else 
		{
			//if success
			//get values from input form
			$data = $this->_get_doc();

			//call method 'create' on komponen model to insert record into table
			$res = $this->komponen_model->create($data);

			if($res) {
				//if process insert success
				$this->session->set_flashdata('message_success', "Successfully inserting data");
			} else {
				//process insert failed
				$this->session->set_flashdata('message_danger', "Failed inserting data");	
			}

			redirect('komponen');
			
		}
	}

	/**
	 * Load Page Update
	 * @return [type] [description]
	 */
	public function update($id)
	{
		if($id) {
			//load helper validation to set form
			$this->load->helper('validation');

			//set condition
			$condition = array('komponen.id' => $id);
			
			//get a record by id
			$data['model'] = $this->komponen_model->get_one($condition);
			//get perangkat list
			$data['perangkat'] = $this->perangkat_model->get_all();

			//set navbar in template
			$this->template->set_navbar('templates/navbar');

			//set rules form validation
			$this->_set_validation();

			//check validation
			if($this->form_validation->run() == FALSE)
			{
				//if error
				$this->template->load('main', 'komponen/update', $data);			
			}
			else 
			{
				//if success
				//prepare data
				//get values from input form
				$data = $this->_get_doc();

				//call method 'create' on komponen model to insert record into table
				$res = $this->komponen_model->update($condition, $data);

				if($res) {
					//if process insert success
					$this->session->set_flashdata('message_success', "Successfully updating data");
				} else {
					//process insert failed
					$this->session->set_flashdata('message_danger', "Failed updating data");	
				}

				redirect('komponen');
				
			}	
		} else {
			$this->session->set_flashdata('message_danger', "System Error");
			redirect('komponen');
		}
	}

	/**
	 * Load Page View
	 * @return [type] [description]
	 */
	public function view($id)
	{
		if($id) {
			$condition = array('komponen.id'=> $id);
			$data['model'] = $this->komponen_model->get_one($condition);

			$this->template->set_navbar('templates/navbar');
			$this->template->load('main', 'komponen/view', $data);		
		} else {
			$this->session->set_flashdata('message_danger', "System Error");
			redirect('komponen');
		}
		
	}	

	/**
	 * Action to Delete Record
	 * @return [type] [description]
	 */
	public function delete($id)
	{
		if($id) {
			$condition = array('id' => $id);

			$res = $this->komponen_model->delete($condition);
			
			if($res)
				$this->session->set_flashdata('message_success', "Successfully deleting data");			
		}

		$this->session->set_flashdata('message_danger', "System Error");

		redirect('komponen');
	}

	/**
	 * private method to set rules form validation
	 */
	private function _set_validation()
	{
		$this->load->library('form_validation');
		$config = array(
	        array(
	                'field' => 'name',
	                'label' => 'Name',
	                'rules' => 'required'
	        ),
	        array(
	                'field' => 'spec',
	                'label' => 'Specification',
	                'rules' => 'required',
	        ),
	        array(
	                'field' => 'id_perangkat',
	                'label' => 'Perangkat',
	                'rules' => 'required'
	        )
		);

		$this->form_validation->set_rules($config);
	}

	private function auth()
	{
		if($this->session->userdata('is_logged_in'))
		{
			return TRUE;
		} else {
			redirect('user');
		}
	}
}
