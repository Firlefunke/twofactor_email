<?php
declare(strict_types=1);
/**
 * @copyright Copyright (c) 2018, Roeland Jago Douma <roeland@famdouma.nl>
 *
 * @author Roeland Jago Douma <roeland@famdouma.nl>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

namespace OCA\TwoFactorEmail\Controller;

use OCA\TwoFactorEmail\Service\Totp;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;
use OCP\IUserSession;

class SettingsController extends Controller {

	/** @var IUserSession */
	private $userSession;

	/** @var Totp */
	private $totp;

	public function __construct(string $appName,
								IRequest $request,
								IUserSession $userSession,
								Totp $totp) {
		parent::__construct($appName, $request);

		$this->userSession = $userSession;
		$this->totp = $totp;
	}

	/**
	 * @NoAdminRequired
	 */
	public function getState(): JSONResponse {
		$user = $this->userSession->getUser();
		if (is_null($user)) {
			throw new Exception('user not available');
		}
		return new JSONResponse([
			'state' => $this->totp->hasSecret($user),
		]);
	}

	/**
	 * @NoAdminRequired
	 */
	public function enable(): JSONResponse {
		$user = $this->userSession->getUser();
		if (is_null($user)) {
			throw new Exception('user not available');
		}

		$this->totp->enable($user);
	}

	/**
	 * @NoAdminRequired
	 */
	public function disable(): JSONResponse {
		$user = $this->userSession->getUser();
		if (is_null($user)) {
			throw new Exception('user not available');
		}

		$this->totp->deleteSecret($user);
	}
}
