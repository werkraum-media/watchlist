<?php

declare(strict_types=1);

/*
 * Copyright (C) 2022 Daniel Siepmann <coding@daniel-siepmann.de>
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301, USA.
 */

namespace WerkraumMedia\Watchlist\Tests\Acceptance\Support;

use TYPO3\TestingFramework\Core\Acceptance\Extension\BackendEnvironment;

/**
 * Load various core extensions and styleguide and call styleguide generator
 */
class Environment extends BackendEnvironment
{
    /**
     * Load a list of core extensions and styleguide
     *
     * @var array
     */
    protected $localConfig = [
        'coreExtensionsToLoad' => [
            'install',
            'core',
            'backend',
            'extbase',
            'frontend',
            'fluid',
            'fluid_styled_content',
        ],
        'testExtensionsToLoad' => [
            'typo3conf/ext/watchlist',
        ],
        'csvDatabaseFixtures' => [
            __DIR__ . '/../../Fixtures/BasicDatabase.csv',
        ],
        'additionalFoldersToCreate' => [
            'config',
        ],
        'pathsToLinkInTestInstance' => [
            'typo3conf/ext/watchlist/Tests/Fixtures/Sites/' => 'config/sites',
        ],
    ];
}
