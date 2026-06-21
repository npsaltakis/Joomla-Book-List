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

/** @var \Nickpsal\Component\BooksList\Administrator\View\Book\HtmlView $this */

$this->getDocument()->getWebAssetManager()
	->useScript('keepalive')
	->useScript('form.validate');
?>
<form action="<?php echo Route::_('index.php?option=com_books_list&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" name="adminForm" id="book-form" aria-label="<?php echo Text::_('COM_BOOKS_LIST_MANAGER_BOOK_EDIT'); ?>"
	class="form-validate">

	<div class="main-card">
		<?php echo HTMLHelper::_('uitab.startTabSet', 'myTab', ['active' => 'details']); ?>

		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'details', Text::_('JGLOBAL_FIELDSET_BASIC')); ?>
			<div class="row">
				<div class="col-lg-9">
					<?php echo $this->form->renderField('title'); ?>
					<?php echo $this->form->renderField('subtitle'); ?>
					<?php echo $this->form->renderField('alias'); ?>
					<?php echo $this->form->renderField('authors'); ?>
					<?php echo $this->form->renderField('description'); ?>
					<?php echo $this->form->renderField('url'); ?>
					<?php echo $this->form->renderField('url_label'); ?>
				</div>
				<div class="col-lg-3">
					<?php echo $this->form->renderField('catid'); ?>
					<?php echo $this->form->renderField('editor_id'); ?>
					<?php echo $this->form->renderField('isbn'); ?>
					<?php echo $this->form->renderField('issn'); ?>
					<?php echo $this->form->renderField('year'); ?>
					<?php echo $this->form->renderField('pages'); ?>
					<?php echo $this->form->renderField('language_book'); ?>
					<?php echo $this->form->renderField('price'); ?>
					<?php echo $this->form->renderField('image'); ?>
					<?php echo $this->form->renderField('file'); ?>
					<?php echo $this->form->renderField('tags'); ?>
				</div>
			</div>
		<?php echo HTMLHelper::_('uitab.endTab'); ?>

		<?php echo HTMLHelper::_('uitab.addTab', 'myTab', 'publishing', Text::_('JGLOBAL_FIELDSET_PUBLISHING')); ?>
			<div class="row">
				<div class="col-lg-6">
					<?php echo $this->form->renderFieldset('publishing'); ?>
				</div>
				<div class="col-lg-6">
					<?php echo $this->form->renderFieldset('metadata'); ?>
				</div>
			</div>
		<?php echo HTMLHelper::_('uitab.endTab'); ?>

		<?php echo HTMLHelper::_('uitab.endTabSet'); ?>

		<input type="hidden" name="task" value="">
		<?php echo HTMLHelper::_('form.token'); ?>
	</div>
</form>
