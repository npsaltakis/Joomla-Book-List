# Books List — Joomla Component (`com_books_list`)

A Joomla component for managing and presenting a catalogue of **books**, their
**authors** and **publishers/editors**, with a clean Bootstrap-based frontend.

- **Author:** Nickpsal · <nickpsal@gmail.com> · <https://github.com/nickpsal>
- **Version:** 1.0.0
- **License:** GNU General Public License v3 or later

---

## Features

- **Books catalogue** — title, subtitle, ISBN/ISSN, year, pages, language,
  price, cover image, attached file, external URL, description and full
  metadata/SEO fields.
- **Authors** — many-to-many relation with books (a book can have several
  authors, an author several books), photo, biography and per-author page.
- **Publishers / Editors** — managed list, linked to each book.
- **Categories** — full integration with Joomla's native `com_categories`.
- **Frontend views**
  - Books list with **filters** (search, category, author, editor, year) and
    pagination.
  - Single book page.
  - **Authors** grid — square-photo cards, sorted alphabetically by surname.
  - Single author page (biography + their books).
  - Editors / publishers list.
- **Spreadsheet import** — bulk-import books from **`.xlsx`** or **`.csv`**
  files. The `.xlsx` reader parses the OOXML package directly (ZipArchive +
  SimpleXML) — **no external libraries required**.
- **Access levels, language associations, hits and a rating field** built in.

## Requirements

- Joomla **4.4+** or **5.x**
- PHP **8.1+** (with the `zip` extension enabled for `.xlsx` import)
- MySQL / MariaDB

## Installation

1. Download the latest package: `dist/com_books_list_v1.0.0.zip`
   (or build it yourself — see [Building](#building-the-install-package)).
2. In the Joomla admin go to **System → Install → Extensions**.
3. Upload the ZIP. The manifest uses `method="upgrade"`, so installing a newer
   version over an existing one upgrades it without losing data.

After installation the component appears under
**Components → Books List**, with submenus for Books, Authors, Editors,
Categories and Import.

## Usage

### Backend
Manage Books, Authors, Editors and Categories from
**Components → Books List**. Use **Import** to load a spreadsheet of books.

### Frontend
Create menu items pointing to the component's views (Books list, single Book,
Authors, single Author, Editors). The books list menu item exposes parameters
for showing/hiding the cover and the filter bar, the page heading, and the
number of items per page.

## Data model

The component creates the following tables (with the site's table prefix):

| Table                          | Purpose                                   |
| ------------------------------ | ----------------------------------------- |
| `#__booklist_books`            | Books (title, ISBN, year, price, …)       |
| `#__booklist_authors`          | Authors (name, lastname, photo, bio)      |
| `#__booklist_editors`          | Publishers / editors                      |
| `#__booklist_book_author`      | Book ↔ author many-to-many link table     |

## Project structure

```
com_books_list.xml        Extension manifest
script.php                Install/uninstall script
admin/                    Backend (controllers, models, views, forms, SQL, language)
site/                     Frontend (controllers, models, views, templates, router)
media/                    CSS / JS / images (installed to media/com_books_list)
dist/                     Built install package(s) — not tracked in git
_migrate/                 Local migration helpers — not tracked in git
```

The PHP namespace root is `Nickpsal\Component\BooksList`.

## Building the install package

The repository is the raw extension tree (manifest at the root), so a package
is simply a ZIP of the project contents with the manifest at the ZIP root.

PowerShell:

```powershell
Add-Type -AssemblyName System.IO.Compression.FileSystem
$src  = (Get-Location).Path
$dest = "dist/com_books_list_v1.0.0.zip"
New-Item -ItemType Directory -Force dist | Out-Null
[System.IO.Compression.ZipFile]::CreateFromDirectory($src, $dest)
```

> Exclude `dist/`, `_migrate/` and the `.git` folder from the package.

## License

This program is free software: you can redistribute it and/or modify it under
the terms of the **GNU General Public License v3** (or, at your option, any
later version). See <https://www.gnu.org/licenses/gpl-3.0.html>.
