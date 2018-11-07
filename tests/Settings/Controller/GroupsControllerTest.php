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

namespace Test;

use OC\Settings\Controller\GroupsController;
use OCP\AppFramework\Http\DataResponse;
use OCP\IGroup;
use OCP\IGroupManager;
use OCP\IRequest;
use OCP\ISubAdminManager;
use OCP\IUser;
use OCP\IUserSession;

class GroupsControllerTest extends TestCase {
	private $appName;
	private $request;
	private $groupManager;
	private $userSession;
	private $groupsController;

	public function setUp() {
		parent::setUp();
		$this->appName = 'settings';
		$this->request = $this->createMock(IRequest::class);
		$this->groupManager = $this->createMock(IGroupManager::class);
		$this->userSession = $this->createMock(IUserSession::class);
		$this->groupsController = new GroupsController($this->appName, $this->request, $this->groupManager, $this->userSession);
	}

	public function provideAdminGroups() {
		return [
			[['admin', 'group1', 'group2']]
		];
	}

	/**
	 * @dataProvider provideAdminGroups
	 * @param $groups
	 */
	public function testGetGroupsForAdmin($groups) {
		$user = $this->createMock(IUser::class);
		$user->method('getUID')
			->willReturn('admin');

		$this->userSession->method('getUser')
			->willReturn($user);
		$this->groupManager->method('isAdmin')
			->willReturn(true);

		$groupsForAdmin = [];
		foreach ($groups as $group) {
			$groupObject = $this->createMock(IGroup::class);
			$groupObject->method('getGID')
				->willReturn($group);
			$groupObject->method('count')
				->willReturn(1);
			$groupsForAdmin[] = $groupObject;
		}

		$this->groupManager->method('search')
			->willReturn($groupsForAdmin);

		$expectedResult = new DataResponse(
			[
				'data' => [
					'adminGroups' => [['id' => 'admin', 'name' => 'admin', 'userCount' => 1]],
					'groups' => [
						['id' => 'group1', 'name' => 'group1', 'userCount' => 1],
						['id' => 'group2', 'name' => 'group2', 'userCount' => 1]
					]
				]

			]
		);
		$result = $this->groupsController->getGroupsForUser();
		$this->assertEquals($expectedResult, $result);
	}

	public function testGetGroupsForSubadmin() {
		$groups = ['group1', 'group2', 'group3'];
		$user = $this->createMock(IUser::class);
		$user->method('getUID')
			->willReturn('admin');

		$this->userSession->method('getUser')
			->willReturn($user);
		$this->groupManager->method('isAdmin')
			->willReturn(false);

		$groupsForSubAdmin = [];
		foreach ($groups as $group) {
			$groupObject = $this->createMock(IGroup::class);
			$groupObject->method('getGID')
				->willReturn($group);
			$groupObject->method('count')
				->willReturn(1);
			$groupsForSubAdmin[] = $groupObject;
		}

		$subAdminManager = $this->createMock(ISubAdminManager::class);
		$subAdminManager->method('getSubAdminsGroups')
			->with($user)
			->willReturn($groupsForSubAdmin);
		$this->groupManager->method('getSubAdmin')
			->willReturn($subAdminManager);

		$expectedResult = new DataResponse(
			[
				'data' => [
					'adminGroups' => [],
					'groups' => [
						['id' => 'group1', 'name' => 'group1', 'userCount' => 1],
						['id' => 'group2', 'name' => 'group2', 'userCount' => 1],
						['id' => 'group3', 'name' => 'group3', 'userCount' => 1]
					]
				]

			]
		);
		$result = $this->groupsController->getGroupsForUser();
		$this->assertEquals($expectedResult, $result);
	}
}
