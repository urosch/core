<?php
/**
 * @author Sujith Haridasan <sharidasan@owncloud.com>
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

namespace OC\Settings\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\IGroupManager;
use OCP\IRequest;
use OCP\IUser;
use OCP\IUserSession;

class GroupsController extends Controller {
	private $groupManager;
	private $userSession;
	public function __construct(string $appName,
								IRequest $request,
								IGroupManager $groupManager,
								IUserSession $userSession) {
		parent::__construct($appName, $request);
		$this->groupManager = $groupManager;
		$this->userSession = $userSession;
	}

	/**
	 * Get the groups for the user
	 *
	 * @NoAdminRequired
	 * @return DataResponse
	 */
	public function getGroupsForUser() {
		$user = $this->userSession->getUser();

		if ($user === null) {
			return new DataResponse(
				[
					'status' => 'error',
					'data' => [
						'message' => 'No groups available'
					]
				],
				Http::STATUS_NOT_FOUND
			);
		}

		$adminGroups = [];
		$userGroups = [];
		$isAdmin = $this->groupManager->isAdmin($user->getUID());
		$groups = $this->getGroups($isAdmin, $user, '');
		foreach ($groups as $group) {
			if ($group->getGID() === 'admin') {
				$adminGroup['id'] = $group->getGID();
				$adminGroup['name'] = $group->getGID();
				$adminGroup['userCount'] = 1;
				$adminGroups[] = $adminGroup;
			} else {
				$userGroup['id'] = $group->getGID();
				$userGroup['name'] = $group->getGID();
				$userGroup['userCount'] = $group->count('');
				$userGroups[] = $userGroup;
			}
		}
		return new DataResponse(
			[
				'data' => ['adminGroups' => $adminGroups, 'groups' => $userGroups]
			], Http::STATUS_OK);
	}

	/**
	 * @param $isAdmin
	 * @param IUser $user
	 * @param string $search
	 * @return array|\OCP\IGroup[]
	 */
	private function getGroups($isAdmin, IUser $user, $search = '') {
		if ($isAdmin === true) {
			return $this->groupManager->search($search, null, null, 'management');
		} elseif ($user !== null) {
			return $this->groupManager->getSubAdmin()->getSubAdminsGroups($user);
		}
		return [];
	}
}
