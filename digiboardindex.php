<?php
/* Copyright (C) 2024 EVARISK <technique@evarisk.com>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 */

/**
 * \file    digiboardindex.php
 * \ingroup digiboard
 * \brief   Home page of digiboard top menu
 */

// Load DigiBoard environment
if (file_exists('digiboard.main.inc.php')) {
    require_once __DIR__ . '/digiboard.main.inc.php';
} elseif (file_exists('../digiboard.main.inc.php')) {
    require_once __DIR__ . '/../digiboard.main.inc.php';
} else {
    die('Include of digiboard main fails');
}

require_once __DIR__ . '/../saturne/core/tpl/index/index_view.tpl.php';
