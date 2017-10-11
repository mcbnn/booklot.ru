<?php
	/**
	 * Created by JetBrains PhpStorm.
	 * User: ageev
	 * Date: 3/7/13
	 * Time: 11:33 AM
	 */
	namespace Admin\Form;

	use Zend\Form\Form;
	use Zend\Form\Element;

	class LoginForm extends Form
	{
		public function __construct($name = null)
		{
			// we want to ignore the name passed
			parent::__construct('album');
			$this->setAttributes(
				array(
					'method' => 'post',
					'class' => 'form-horizontal',
					'role' => 'form',
					'id' => 'form_login'
				)
			);
			$this->add(array(
				'type' => 'Zend\Form\Element\Text',
				'name' => 'username',
				'attributes' => array(
					'placeholder' => 'Email',
					'class' => 'form-control',
					'id' => 'username',
					'data-mask' => 'email',
					'autocomplete' => 'off'
				),
				'options' => array(
					'label' => 'Логин',
				),
			));
			$this->add(array(
				'name' => 'password',
				'type' => 'Zend\Form\Element\Password',
				'attributes' => array(
					'placeholder' => '***********',
					'class' => 'form-control',
					'id' => 'password',
					'autocomplete' => 'off'
				
				),
				'options' => array(
					'label' => 'Пароль',
	
				),
			));
			$this->add(array(
				'type' => 'Zend\Form\Element\Button',
				'name' => 'submit',
				'attributes' => array(
					'class' => 'btn',
					'type' => 'submit',
				),
				'options' => array(
					'label' => '<i class="icon-user"></i> Войти',
				),
			));
		}
	}