<?php
/**
 * @author Bart Visscher <bartv@thisnet.nl>
 * @author Björn Schießle <bjoern@schiessle.org>
 * @author Christopher Schäpers <kondou@ts.unde.re>
 * @author Christoph Wurst <christoph@owncloud.com>
 * @author Georg Ehrke <georg@owncloud.com>
 * @author Lukas Reschke <lukas@statuscode.ch>
 * @author Morris Jobke <hey@morrisjobke.de>
 * @author Raghu Nayyar <me@iraghu.com>
 * @author Robin Appelman <icewind@owncloud.com>
 * @author Roeland Jago Douma <rullzer@owncloud.com>
 * @author Thomas Müller <thomas.mueller@tmit.eu>
 * @author Tom Needham <tom@owncloud.com>
 * @author Vincent Petry <pvince81@owncloud.com>
 *
 * @copyright Copyright (c) 2018, ownCloud GmbH
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */

namespace OC\Settings;

$application = new Application();
$application->registerRoutes($this, [
	'resources' => [
		'auth_settings' => ['url' => '/settings/personal/authtokens'],
	],
	'routes' => [
		['name' => 'MailSettings#setMailSettings', 'url' => '/settings/admin/mailsettings', 'verb' => 'POST'],
		['name' => 'MailSettings#storeCredentials', 'url' => '/settings/admin/mailsettings/credentials', 'verb' => 'POST'],
		['name' => 'MailSettings#sendTestMail', 'url' => '/settings/admin/mailtest', 'verb' => 'POST'],
		['name' => 'AppSettings#listApps', 'url' => '/settings/apps/list', 'verb' => 'GET'],
		['name' => 'LogSettings#setLogLevel', 'url' => '/settings/admin/log/level', 'verb' => 'POST'],
		['name' => 'LogSettings#download', 'url' => '/settings/admin/log/download', 'verb' => 'GET'],
		['name' => 'CheckSetup#check', 'url' => '/settings/ajax/checksetup', 'verb' => 'GET'],
		['name' => 'CheckSetup#getFailedIntegrityCheckFiles', 'url' => '/settings/integrity/failed', 'verb' => 'GET'],
		['name' => 'CheckSetup#rescanFailedIntegrityCheck', 'url' => '/settings/integrity/rescan', 'verb' => 'GET'],
		['name' => 'Certificate#addPersonalRootCertificate', 'url' => '/settings/personal/certificate', 'verb' => 'POST'],
		['name' => 'Certificate#removePersonalRootCertificate', 'url' => '/settings/personal/certificate/{certificateIdentifier}', 'verb' => 'DELETE'],
		['name' => 'Certificate#addSystemRootCertificate', 'url' => '/settings/admin/certificate', 'verb' => 'POST'],
		['name' => 'Certificate#removeSystemRootCertificate', 'url' => '/settings/admin/certificate/{certificateIdentifier}', 'verb' => 'DELETE'],
		['name' => 'SettingsPage#getPersonal', 'url' => '/settings/personal', 'verb' => 'GET'],
		['name' => 'SettingsPage#getAdmin', 'url' => '/settings/admin', 'verb' => 'GET'],
		['name' => 'Users#changeMail', 'url' => '/settings/mailaddress/change/{token}/{userId}', 'verb' => 'GET'],
		['name' => 'Cors#getDomains', 'url' => '/settings/domains', 'verb' => 'GET'],
		['name' => 'Cors#addDomain', 'url' => '/settings/domains', 'verb' => 'POST'],
		['name' => 'Cors#removeDomain', 'url' => '/settings/domains/{id}', 'verb' => 'DELETE'],
		['name' => 'LegalSettings#setImprintUrl', 'url' => '/settings/admin/legal/imprint', 'verb' => 'POST'],
		['name' => 'LegalSettings#setPrivacyPolicyUrl', 'url' => '/settings/admin/legal/privacypolicy', 'verb' => 'POST'],
		['name' => 'Groups#getGroupsForUser', 'url' => '/settings/users/groups', 'verb' => 'GET'],
	]
]);

/** @var $this \OCP\Route\IRouter */

// Settings pages
$this->create('settings_help', '/settings/help')
	->actionInclude('settings/help.php');
// Settings ajax actions
// personal
$this->create('settings_personal_changepassword', '/settings/personal/changepassword')
	->post()
	->action('OC\Settings\ChangePassword\Controller', 'changePersonalPassword');
$this->create('settings_ajax_setlanguage', '/settings/ajax/setlanguage.php')
	->actionInclude('settings/ajax/setlanguage.php');
// apps
$this->create('settings_ajax_enableapp', '/settings/ajax/enableapp.php')
	->actionInclude('settings/ajax/enableapp.php');
$this->create('settings_ajax_disableapp', '/settings/ajax/disableapp.php')
	->actionInclude('settings/ajax/disableapp.php');
$this->create('settings_ajax_updateapp', '/settings/ajax/updateapp.php')
	->actionInclude('settings/ajax/updateapp.php');
$this->create('settings_ajax_uninstallapp', '/settings/ajax/uninstallapp.php')
	->actionInclude('settings/ajax/uninstallapp.php');
$this->create('settings_ajax_navigationdetect', '/settings/ajax/navigationdetect.php')
	->actionInclude('settings/ajax/navigationdetect.php');
// admin
$this->create('settings_ajax_excludegroups', '/settings/ajax/excludegroups.php')
	->actionInclude('settings/ajax/excludegroups.php');
