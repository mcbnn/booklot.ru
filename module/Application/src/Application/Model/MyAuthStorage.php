<?php
/**
 * Created by JetBrains PhpStorm.
 * User: ageev
 * Date: 3/28/13
 * Time: 4:04 PM
 */
	namespace Application\Model;

	use Zend\Authentication\Storage;

	class MyAuthStorage extends Storage\Session
	{
		public function setRememberMe($rememberMe = 0, $time = 12090600)
		{
			if ($rememberMe == 1) {
				$this->session->getManager()->rememberMe($time);
			}

		}

		public function forgetMe()
		{
			$this->session->getManager()->forgetMe();

		}
	}