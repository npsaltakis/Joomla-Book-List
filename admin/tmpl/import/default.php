<?php
/**
 * @package     Com_BooksList
 * @copyright   (C) 2026 Nickpsal
 * @license     GNU General Public License version 3 or later
 */

\defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

/** @var \Nickpsal\Component\BooksList\Administrator\View\Import\HtmlView $this */
?>
<form action="<?php echo Route::_('index.php?option=com_books_list&task=import.import'); ?>"
	method="post" name="adminForm" id="import-form" enctype="multipart/form-data">
	<div class="main-card p-3">
		<div class="row">
			<div class="col-lg-8">
				<p class="text-muted"><?php echo Text::_('COM_BOOKS_LIST_IMPORT_DESC'); ?></p>

				<div class="control-group">
					<div class="control-label">
						<label for="import_file"><?php echo Text::_('COM_BOOKS_LIST_IMPORT_FILE_LABEL'); ?></label>
					</div>
					<div class="controls">
						<input type="file" name="import_file" id="import_file" accept=".xlsx,.csv" class="form-control" required>
					</div>
				</div>

				<button type="submit" class="btn btn-primary mt-3">
					<span class="icon-upload" aria-hidden="true"></span>
					<?php echo Text::_('COM_BOOKS_LIST_IMPORT_BUTTON'); ?>
				</button>
			</div>
			<div class="col-lg-4">
				<div class="card">
					<div class="card-header"><?php echo Text::_('COM_BOOKS_LIST_IMPORT_COLUMNS_TITLE'); ?></div>
					<div class="card-body small">
						<p><?php echo Text::_('COM_BOOKS_LIST_IMPORT_COLUMNS_DESC'); ?></p>
						<code>title</code>, <code>subtitle</code>, <code>isbn</code>, <code>issn</code>,
						<code>year</code>, <code>pages</code>, <code>language</code>, <code>price</code>,
						<code>category</code>, <code>editor</code>, <code>authors</code>,
						<code>description</code>, <code>url</code>
					</div>
				</div>
			</div>
		</div>

		<input type="hidden" name="task" value="import.import">
		<?php echo HTMLHelper::_('form.token'); ?>
	</div>
</form>
