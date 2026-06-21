<?php
/**
 * @package     Com_BooksList
 * @copyright   (C) 2026 Nickpsal
 * @license     GNU General Public License version 3 or later
 */

namespace Nickpsal\Component\BooksList\Administrator\Helper;

\defined('_JEXEC') or die;

/**
 * Lightweight spreadsheet reader for .xlsx and .csv files.
 *
 * Reads .xlsx without any external dependency by parsing the OOXML package
 * (ZipArchive + SimpleXML). Falls back to native CSV parsing for .csv files.
 */
class SpreadsheetReader
{
	/**
	 * Read a file into a 2D array of rows. The first row is treated as data
	 * by this method; header handling is left to the caller.
	 *
	 * @param   string  $path       Absolute path to the file.
	 * @param   string  $extension  Lower-case file extension (xlsx|csv).
	 *
	 * @return  array  Array of rows, each an array of cell values.
	 *
	 * @throws  \RuntimeException
	 */
	public static function read(string $path, string $extension): array
	{
		switch ($extension) {
			case 'csv':
				return self::readCsv($path);

			case 'xlsx':
				return self::readXlsx($path);

			default:
				throw new \RuntimeException('COM_BOOKS_LIST_IMPORT_ERROR_TYPE');
		}
	}

	/**
	 * Parse a CSV file. Auto-detects comma or semicolon delimiter.
	 *
	 * @param   string  $path  Absolute path.
	 *
	 * @return  array
	 */
	protected static function readCsv(string $path): array
	{
		$rows = [];

		if (($handle = fopen($path, 'r')) === false) {
			throw new \RuntimeException('COM_BOOKS_LIST_IMPORT_ERROR_NOFILE');
		}

		// Detect delimiter from the first line.
		$firstLine = fgets($handle);
		$delimiter = (substr_count($firstLine, ';') > substr_count($firstLine, ',')) ? ';' : ',';
		rewind($handle);

		while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
			// Skip fully empty lines.
			if ($data === [null] || (count($data) === 1 && trim((string) $data[0]) === '')) {
				continue;
			}

			$rows[] = array_map(static fn ($v) => is_string($v) ? trim($v) : $v, $data);
		}

		fclose($handle);

		return $rows;
	}

	/**
	 * Parse an .xlsx file (first worksheet) without external libraries.
	 *
	 * @param   string  $path  Absolute path.
	 *
	 * @return  array
	 */
	protected static function readXlsx(string $path): array
	{
		if (!class_exists(\ZipArchive::class)) {
			throw new \RuntimeException('The PHP Zip extension is required to import .xlsx files.');
		}

		$zip = new \ZipArchive();

		if ($zip->open($path) !== true) {
			throw new \RuntimeException('COM_BOOKS_LIST_IMPORT_ERROR_TYPE');
		}

		// Shared strings table (optional).
		$sharedStrings = [];

		if (($ssXml = $zip->getFromName('xl/sharedStrings.xml')) !== false) {
			$ss = simplexml_load_string($ssXml);

			if ($ss !== false) {
				foreach ($ss->si as $si) {
					$sharedStrings[] = self::extractStringItem($si);
				}
			}
		}

		// Resolve the first worksheet target.
		$sheetPath = self::resolveFirstSheet($zip);
		$sheetXml  = $zip->getFromName($sheetPath);
		$zip->close();

		if ($sheetXml === false) {
			throw new \RuntimeException('COM_BOOKS_LIST_IMPORT_ERROR_TYPE');
		}

		$sheet = simplexml_load_string($sheetXml);

		if ($sheet === false || !isset($sheet->sheetData)) {
			return [];
		}

		$rows = [];

		foreach ($sheet->sheetData->row as $row) {
			$cells = [];

			foreach ($row->c as $c) {
				$colIndex = self::columnIndex((string) $c['r']);
				$type     = (string) $c['t'];
				$value    = '';

				if ($type === 's') {
					// Shared string index.
					$idx   = (int) $c->v;
					$value = $sharedStrings[$idx] ?? '';
				} elseif ($type === 'inlineStr') {
					$value = self::extractStringItem($c->is);
				} else {
					$value = isset($c->v) ? (string) $c->v : '';
				}

				$cells[$colIndex] = is_string($value) ? trim($value) : $value;
			}

			if (empty($cells)) {
				continue;
			}

			// Normalise to a 0-based, gap-filled row.
			$max    = max(array_keys($cells));
			$normal = [];

			for ($i = 0; $i <= $max; $i++) {
				$normal[$i] = $cells[$i] ?? '';
			}

			// Skip fully empty rows.
			if (implode('', $normal) === '') {
				continue;
			}

			$rows[] = $normal;
		}

		return $rows;
	}

	/**
	 * Extract text from a shared-string / inline-string <si> element,
	 * concatenating rich-text runs.
	 *
	 * @param   \SimpleXMLElement  $si  The string item element.
	 *
	 * @return  string
	 */
	protected static function extractStringItem(\SimpleXMLElement $si): string
	{
		if (isset($si->t)) {
			return (string) $si->t;
		}

		$text = '';

		if (isset($si->r)) {
			foreach ($si->r as $run) {
				$text .= (string) $run->t;
			}
		}

		return $text;
	}

	/**
	 * Find the path of the first worksheet inside the package.
	 *
	 * @param   \ZipArchive  $zip  The open archive.
	 *
	 * @return  string
	 */
	protected static function resolveFirstSheet(\ZipArchive $zip): string
	{
		// Most files expose the first sheet here.
		if ($zip->locateName('xl/worksheets/sheet1.xml') !== false) {
			return 'xl/worksheets/sheet1.xml';
		}

		// Otherwise scan for the first worksheet entry.
		for ($i = 0; $i < $zip->numFiles; $i++) {
			$name = $zip->getNameIndex($i);

			if (strpos($name, 'xl/worksheets/sheet') === 0 && str_ends_with($name, '.xml')) {
				return $name;
			}
		}

		return 'xl/worksheets/sheet1.xml';
	}

	/**
	 * Convert an Excel cell reference (e.g. "B5") to a 0-based column index.
	 *
	 * @param   string  $ref  Cell reference.
	 *
	 * @return  int
	 */
	protected static function columnIndex(string $ref): int
	{
		preg_match('/^([A-Z]+)/', $ref, $m);

		$letters = $m[1] ?? 'A';
		$index   = 0;

		for ($i = 0, $len = strlen($letters); $i < $len; $i++) {
			$index = $index * 26 + (ord($letters[$i]) - 64);
		}

		return $index - 1;
	}
}
