<?php
/**
 * @package     Com_BooksList
 * @copyright   (C) 2026 Nickpsal
 * @license     GNU General Public License version 3 or later
 */

namespace Nickpsal\Component\BooksList\Administrator\Controller;

use Joomla\CMS\MVC\Controller\BaseController;

\defined('_JEXEC') or die;

/**
 * Default admin controller for com_books_list.
 */
class DisplayController extends BaseController
{
	/**
	 * The URL option for the component.
	 *
	 * @var string
	 */
	protected $option = 'com_books_list';

	/**
	 * The default view.
	 *
	 * @var string
	 */
	protected $default_view = 'books';
}
