<?php
/**
 * @package     Com_BooksList
 * @copyright   (C) 2026 Nickpsal
 * @license     GNU General Public License version 3 or later
 */

namespace Nickpsal\Component\BooksList\Site\Controller;

use Joomla\CMS\MVC\Controller\BaseController;

\defined('_JEXEC') or die;

/**
 * Default site controller.
 */
class DisplayController extends BaseController
{
	protected $option = 'com_books_list';

	protected $default_view = 'books';

	public function display($cachable = false, $urlparams = [])
	{
		return parent::display($cachable, $urlparams);
	}
}
