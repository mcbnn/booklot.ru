<?php
	/**
	 * Created by JetBrains PhpStorm.
	 * User: ageev
	 * Date: 3/7/13
	 * Time: 11:33 AM
	 */
	namespace Application\Form;

	use Zend\Form\Form;
	use Zend\Form\Element;

	class ChangePasswordForm extends Form
	{
		public function __construct($name = null)
		{
			// we want to ignore the name passed
			parent::__construct('album');
			$this->setAttributes(
				array(
					'method' => 'post',
					'class' => 'form-horizontal'
				)
			);
			$this->add(array(
				'type' => 'Zend\Form\Element\Text',
				'name' => 'username',
				'attributes' => array(
					'disabled' => 'true',
				),
				'options' => array(
					'label' => 'ИНН',
					'label_attributes' => array(
						'class' => 'control-label'
					),
				),
			));
			$this->add(array(
				'name' => 'password',
				'type' => 'Zend\Form\Element\Password',
				'attributes' => array(
					'placeholder' => '***********',
				),
				'options' => array(
					'label' => 'Пароль',
					'label_attributes' => array(
						'class' => 'control-label'
					),
				),
			));
			$this->add(array(
				'name' => 'password',
				'type' => 'Zend\Form\Element\Password',
				'attributes' => array(
					'placeholder' => '***********',
				),
				'options' => array(
					'label' => 'Пароль',
					'label_attributes' => array(
						'class' => 'control-label'
					),
				),
			));
			$this->add(array(
				'type'  => 'Zend\Form\Element\Checkbox',
				'name' => 'rememberme',
				'options' => array(
					'label' => 'Запомнить меня',
					'label_attributes' => array(
						'class' => 'checkbox'
					),
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