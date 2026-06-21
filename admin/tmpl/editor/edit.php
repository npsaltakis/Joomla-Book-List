<?php
/**
 * @package     Com_BooksList
 * @copyright   (C) 2026 Nickpsal
 * @license     GNU General Public License version 3 or later
 */

\defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;

/** @var \Nickpsal\Component\BooksList\Administrator\View\Editor\HtmlView $this */

$this->getDocument()->getWebAssetManager()->useScript('keepalive')->useScript('form.validate');
?>
<form action="<?php echo Route::_('index.php?option=com_books_list&layout=edit&id=' . (int) $this->item->id); ?>"
	method="post" name="adminForm" id="editor-form" class="form-validate">
	<div class="main-card">
		<div class="row">
			<div class="col-lg-8">
				<?php echo $this->form->renderField('name'); ?>
				<?php echo $this->form->renderField('alias'); ?>
				<?php echo $this->form->renderField('description'); ?>
			</div>
			<div class="col-lg-4">
				<?php echo $this->form->renderField('state'); ?>
				<?php echo $this->form->renderField('access'); ?>
				<?php echo $this->form->renderField('language'); ?>
			</div>
		</div>
		<input type="hidden" name="task" value="">
		<?php echo HTMLHelper::_('form.token'); ?>
	</div>
</form>
