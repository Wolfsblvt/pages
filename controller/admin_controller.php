<?php
/**
*
* Pages extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace phpbb\pages\controller;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
* Admin controller
*/
class admin_controller implements admin_interface
{
	/** @var \phpbb\controller\helper */
	protected $helper;

	/** @var \phpbb\log\log */
	protected $log;

	/** @var \phpbb\pages\operators\page */
	protected $page_operator;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var ContainerInterface */
	protected $phpbb_container;

	/** string Custom form action */
	protected $u_action;

	/**
	* Constructor
	*
	* @param \phpbb\controller\helper             $helper          Controller helper object
	* @param \phpbb\log\log                       $log             The phpBB log system
	* @param \phpbb\pages\operators\page          $page_operator   Pages operator object
	* @param \phpbb\request\request               $request         Request object
	* @param \phpbb\template\template             $template        Template object
	* @param \phpbb\user                          $user            User object
	* @param ContainerInterface                   $phpbb_container Service container interface
	* @return \phpbb\pages\controller\admin_controller
	* @access public
	*/
	public function __construct(\phpbb\controller\helper $helper, \phpbb\log\log $log, \phpbb\pages\operators\page $page_operator, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user, ContainerInterface $phpbb_container)
	{
		$this->helper = $helper;
		$this->log = $log;
		$this->page_operator = $page_operator;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->container = $phpbb_container;
	}

	/**
	* Display the pages
	*
	* @return null
	* @access public
	*/
	public function display_pages()
	{
		// Grab all the pages from the db
		$entities = $this->page_operator->get_pages();

		// Process each page entity for display
		foreach ($entities as $entity)
		{
			// Set output block vars for display in the template
			$this->template->assign_block_vars('pages', array(
				'PAGES_TITLE'		=> $entity->get_title(),
				'PAGES_DESCRIPTION'	=> $entity->get_description(),
				'PAGES_ROUTE'		=> $entity->get_route(),
				'PAGES_TEMPLATE'	=> $entity->get_template(),
				'PAGES_ORDER'		=> $entity->get_order(),

				'U_DELETE'			=> "{$this->u_action}&amp;action=delete&amp;page_id=" . $entity->get_id(),
				'U_EDIT'			=> "{$this->u_action}&amp;action=edit&amp;page_id=" . $entity->get_id(),
				'U_PAGES_ROUTE'		=> $this->helper->route('phpbb_pages_main_controller', array('route' => $entity->get_route())),
			));
		}

		// Set output vars for display in the template
		$this->template->assign_vars(array(
			'U_ACTION'		=> $this->u_action,
			'U_ADD_PAGE'	=> "{$this->u_action}&amp;action=add",
		));
	}

	/**
	* Add a page
	*
	* @return null
	* @access public
	*/
	public function add_page()
	{
		// @todo
	}

	/**
	* Edit a page
	*
	* @param int $page_id The page identifier to edit
	* @return null
	* @access public
	*/
	public function edit_page($page_id)
	{
		// @todo
	}

	/**
	* Process page data to be added or edited
	*
	* @param object $entity The page entity object
	* @param array $data The form data to be processed
	* @return null
	* @access protected
	*/
	protected function add_edit_page_data($entity, $data)
	{
		// @todo
	}

	/**
	* Delete a page
	*
	* @param int $page_id The page identifier to delete
	* @return null
	* @access public
	*/
	public function delete_page($page_id)
	{
		// Initiate and load the page entity
		$entity = $this->container->get('phpbb.pages.entity')->load($page_id);

		try
		{
			// Delete the page
			$this->page_operator->delete_page($page_id);
		}
		catch (\phpbb\pages\exception\base $e)
		{
			// Display an error message if delete failed
			trigger_error($this->user->lang('ACP_PAGES_DELETE_ERRORED') . adm_back_link($this->u_action), E_USER_WARNING);
		}

		// Log the action
		$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'ACP_PAGES_DELETED_LOG', time(), array($entity->get_title()));

		// If AJAX was used, show user a result message
		if ($this->request->is_ajax())
		{
			$json_response = new \phpbb\json_response;
			$json_response->send(array(
				'MESSAGE_TITLE'	=> $this->user->lang['INFORMATION'],
				'MESSAGE_TEXT'	=> $this->user->lang('ACP_PAGES_DELETE_SUCCESS'),
				'REFRESH_DATA'	=> array(
					'time'	=> 3
				)
			));
		}
	}

	/**
	* Set page url
	*
	* @param string $u_action Custom form action
	* @return null
	* @access public
	*/
	public function set_page_url($u_action)
	{
		$this->u_action = $u_action;
	}
}
