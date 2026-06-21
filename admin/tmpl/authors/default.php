<?php
/**
 * @package     Com_BooksList
 * @copyright   (C) 2026 Nickpsal
 * @license     GNU General Public License version 3 or later
 */

\defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

/** @var \Nickpsal\Component\BooksList\Administrator\View\Authors\HtmlView $this */

$this->getDocument()->getWebAssetManager()->useScript('multiselect');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo Route::_('index.php?option=com_books_list&view=authors'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="row">
		<div class="col-md-12">
			<div id="j-main-container" class="j-main-container">
				<?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>

				<?php if (empty($this->items)) : ?>
					<div class="alert alert-info"><?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?></div>
				<?php else : ?>
					<table class="table">
						<thead>
							<tr>
								<td class="w-1 text-center"><?php echo HTMLHelper::_('grid.checkall'); ?></td>
								<th class="w-1 text-center"><?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?></th>
								<th><?php echo HTMLHelper::_('searchtools.sort', 'COM_BOOKS_LIST_FIELD_LASTNAME_LABEL', 'a.lastname', $listDirn, $listOrder); ?></th>
								<th><?php echo HTMLHelper::_('searchtools.sort', 'COM_BOOKS_LIST_FIELD_NAME_LABEL', 'a.name', $listDirn, $listOrder); ?></th>
								<th class="w-10 text-center"><?php echo Text::_('COM_BOOKS_LIST_HEADING_BOOK_COUNT'); ?></th>
								<th class="w-3 text-center d-none d-md-table-cell"><?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?></th>
							</tr>
						</thead>
						<tbody>
						<?php foreach ($this->items as $i => $item) : ?>
							<tr class="row<?php echo $i % 2; ?>">
								<td class="text-center"><?php echo HTMLHelper::_('grid.id', $i, $item->id, false, 'cid', 'cb', $item->lastname); ?></td>
								<td class="text-center"><?php echo HTMLHelper::_('jgrid.published', $item->state, $i, 'authors.', true); ?></td>
								<th scope="row">
									<a href="<?php echo Route::_('index.php?option=com_books_list&task=author.edit&id=' . (int) $item->id); ?>">
										<?php echo $this->escape($item->lastname); ?>
									</a>
								</th>
								<td><?php echo $this->escape($item->name); ?></td>
								<td class="text-center"><?php echo (int) $item->book_count; ?></td>
								<td class="text-center d-none d-md-table-cell"><?php echo (int) $item->id; ?></td>
							</tr>
						<?php endforeach; ?>
						</tbody>
					</table>
					<?php echo $this->pagination->getListFooter(); ?>
				<?php endif; ?>

				<input type="hidden" name="task" value="">
				<input type="hidden" name="boxchecked" value="0">
				<?php echo HTMLHelper::_('form.token'); ?>
			</div>
		</div>
	</div>
</form>
