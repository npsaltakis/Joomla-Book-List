<?php
/**
 * @package     Com_BooksList
 * @copyright   (C) 2026 Nickpsal
 * @license     GNU General Public License version 3 or later
 */

namespace Nickpsal\Component\BooksList\Site\Helper;

\defined('_JEXEC') or die;

/**
 * Route helper for building non-SEF query links that the router turns into SEF URLs.
 */
class RouteHelper
{
	/**
	 * Build the query link for a single book.
	 *
	 * @param   int     $id        Book id.
	 * @param   int     $catid     Category id.
	 * @param   string  $language  Language tag.
	 * @param   string  $alias     Book alias.
	 *
	 * @return  string
	 */
	public static function getBookRoute(int $id, int $catid = 0, string $language = '*', string $alias = ''): string
	{
		$slug = $alias !== '' ? ($id . ':' . $alias) : (string) $id;

		$link = 'index.php?option=com_books_list&view=book&id=' . $slug;

		if ($catid) {
			$link .= '&catid=' . $catid;
		}

		if ($language && $language !== '*') {
			$link .= '&lang=' . $language;
		}

		return $link;
	}

	/**
	 * Build the query link for a single author.
	 *
	 * @param   int     $id        Author id.
	 * @param   string  $language  Language tag.
	 * @param   string  $alias     Author alias.
	 *
	 * @return  string
	 */
	public static function getAuthorRoute(int $id, string $language = '*', string $alias = ''): string
	{
		$slug = $alias !== '' ? ($id . ':' . $alias) : (string) $id;

		$link = 'index.php?option=com_books_list&view=author&id=' . $slug;

		if ($language && $language !== '*') {
			$link .= '&lang=' . $language;
		}

		return $link;
	}

	/**
	 * Build the query link for an editor / publisher (their books listing).
	 *
	 * @param   int     $id        Editor id.
	 * @param   string  $language  Language tag.
	 *
	 * @return  string
	 */
	public static function getEditorRoute(int $id, string $language = '*'): string
	{
		$link = 'index.php?option=com_books_list&view=books&filter_editor=' . $id;

		if ($language && $language !== '*') {
			$link .= '&lang=' . $language;
		}

		return $link;
	}

	/**
	 * Build the query link for a category listing.
	 *
	 * @param   int     $catid     Category id.
	 * @param   string  $language  Language tag.
	 *
	 * @return  string
	 */
	public static function getCategoryRoute(int $catid, string $language = '*'): string
	{
		$link = 'index.php?option=com_books_list&view=books&filter_catid=' . $catid;

		if ($language && $language !== '*') {
			$link .= '&lang=' . $language;
		}

		return $link;
	}
}
