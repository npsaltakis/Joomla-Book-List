<?php
/**
 * @package     Com_BooksList
 * @copyright   (C) 2026 Nickpsal
 * @license     GNU General Public License version 3 or later
 */

namespace Nickpsal\Component\BooksList\Administrator\Controller;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

\defined('_JEXEC') or die;

/**
 * Import controller.
 */
class ImportController extends BaseController
{
	/**
	 * The URL option for the component.
	 *
	 * @var string
	 */
	protected $option = 'com_books_list';

	/**
	 * Default display task.
	 *
	 * @param   bool   $cachable
	 * @param   array  $urlparams
	 *
	 * @return  static
	 */
	public function display($cachable = false, $urlparams = [])
	{
		$this->input->set('view', 'import');

		return parent::display($cachable, $urlparams);
	}

	/**
	 * Handle the upload + import action.
	 *
	 * @return  void
	 */
	public function import()
	{
		$this->checkToken();

		$redirect = Route::_('index.php?option=com_books_list&view=import', false);

		if (!$this->app->getIdentity()->authorise('core.create', 'com_books_list')) {
			$this->setRedirect($redirect, Text::_('JERROR_ALERTNOAUTHOR'), 'error');

			return;
		}

		$file = $this->input->files->get('import_file', null, 'raw');

		try {
			if (empty($file) || empty($file['name'])) {
				throw new \RuntimeException('COM_BOOKS_LIST_IMPORT_ERROR_NOFILE');
			}

			/** @var \Nickpsal\Component\BooksList\Administrator\Model\ImportModel $model */
			$model = $this->getModel('Import', 'Administrator', ['ignore_request' => true]);
			$count = $model->import($file);

			$this->setRedirect($redirect, Text::sprintf('COM_BOOKS_LIST_IMPORT_SUCCESS', $count), 'message');
		} catch (\Throwable $e) {
			// The message may be a language key.
			$message = Text::_($e->getMessage());
			$this->setRedirect($redirect, $message, 'error');
		}
	}
}
