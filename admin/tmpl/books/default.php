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

/** @var \Nickpsal\Component\BooksList\Administrator\View\Books\HtmlView $this */

$wa = $this->getDocument()->getWebAssetManager();
$wa->useScript('table.columns')->useScript('multiselect');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo Route::_('index.php?option=com_books_list&view=books'); ?>" method="post" name="adminForm" id="adminForm">
	<div class="row">
		<div class="col-md-12">
			<div id="j-main-container" class="j-main-container">
				<?php echo \Joomla\CMS\Layout\LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>

				<?php if (empty($this->items)) : ?>
					<div class="alert alert-info">
						<span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
						<?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
					</div>
				<?php else : ?>
					<table class="table" id="bookList">
						<caption class="visually-hidden"><?php echo Text::_('COM_BOOKS_LIST_MANAGER_BOOKS'); ?></caption>
						<thead>
							<tr>
								<td class="w-1 text-center">
									<?php echo HTMLHelper::_('grid.checkall'); ?>
								</td>
								<th scope="col" class="w-1 text-center">
									<?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
								</th>
								<th scope="col">
									<?php echo HTMLHelper::_('searchtools.sort', 'COM_BOOKS_LIST_HEADING_TITLE', 'a.title', $listDirn, $listOrder); ?>
								</th>
								<th scope="col" class="d-none d-md-table-cell">
									<?php echo HTMLHelper::_('searchtools.sort', 'COM_BOOKS_LIST_HEADING_CATEGORY', 'category_title', $listDirn, $listOrder); ?>
								</th>
								<th scope="col" class="d-none d-md-table-cell">
									<?php echo HTMLHelper::_('searchtools.sort', 'COM_BOOKS_LIST_FIELD_EDITOR_LABEL', 'editor_name', $listDirn, $listOrder); ?>
								</th>
								<th scope="col" class="w-5 d-none d-md-table-cell text-center">
									<?php echo HTMLHelper::_('searchtools.sort', 'COM_BOOKS_LIST_HEADING_YEAR', 'a.year', $listDirn, $listOrder); ?>
								</th>
								<th scope="col" class="d-none d-lg-table-cell">
									<?php echo HTMLHelper::_('searchtools.sort', 'COM_BOOKS_LIST_HEADING_ISBN', 'a.isbn', $listDirn, $listOrder); ?>
								</th>
								<th scope="col" class="w-3 d-none d-lg-table-cell text-center">
									<?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
								</th>
							</tr>
						</thead>
						<tbody>
						<?php foreach ($this->items as $i => $item) : ?>
							<tr class="row<?php echo $i % 2; ?>">
								<td class="text-center">
									<?php echo HTMLHelper::_('grid.id', $i, $item->id, false, 'cid', 'cb', $item->title); ?>
								</td>
								<td class="text-center">
									<?php echo HTMLHelper::_('jgrid.published', $item->state, $i, 'books.', true); ?>
								</td>
								<th scope="row">
									<a href="<?php echo Route::_('index.php?option=com_books_list&task=book.edit&id=' . (int) $item->id); ?>">
										<?php echo $this->escape($item->title); ?>
									</a>
									<?php if (!empty($item->subtitle)) : ?>
										<div class="small text-muted"><?php echo $this->escape($item->subtitle); ?></div>
									<?php endif; ?>
								</th>
								<td class="d-none d-md-table-cell"><?php echo $this->escape($item->category_title ?? ''); ?></td>
								<td class="d-none d-md-table-cell"><?php echo $this->escape($item->editor_name ?? ''); ?></td>
								<td class="d-none d-md-table-cell text-center"><?php echo $item->year ? (int) $item->year : ''; ?></td>
								<td class="d-none d-lg-table-cell"><?php echo $this->escape($item->isbn); ?></td>
								<td class="d-none d-lg-table-cell text-center"><?php echo (int) $item->id; ?></td>
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
