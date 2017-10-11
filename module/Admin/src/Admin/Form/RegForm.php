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

	class RegForm extends Form
	{
		public function __construct($name = null)
		{
			// we want to ignore the name passed
			parent::__construct('reg');
			$this->setAttributes(
				array(
					'method' => 'post',
					'role' => 'form',
					'id' => 'form_register'
				)
			);
			$this->add(array(
				'type' => 'Zend\Form\Element\Text',
				'name' => 'name',
				'attributes' => array(
					'placeholder' => 'Login',
					'class' => 'form-control',
					'id' => 'name',
					'autocomplete' => 'off'
				),
				'options' => array(
					'label' => 'Логин',
				),
			));
			$this->add(array(
				'type' => 'Zend\Form\Element\Text',
				'name' => 'email',
				'attributes' => array(
					'placeholder' => 'Email',
					'class' => 'form-control',
					'id' => 'email',
					'autocomplete' => 'off',
				
				),
				'options' => array(
					'label' => 'Email',
				),
			));
			$this->add(array(
				'type' => 'Zend\Form\Element\Text',
				'name' => 'birth',
				'attributes' => array(
					'placeholder' => 'День рождения',
					'class' => 'form-control y-m-d',
					'id' => 'birth',
					'autocomplete' => 'off',
					'data-mask' => '9999-99-99'
				),
				'options' => array(
					'label' => 'день рождения',
				),
			));
			$this->add(array(
				'type' => 'Zend\Form\Element\Select',
				'name' => 'sex',
				'attributes' => array(
					'placeholder' => 'пол',
					'class' => 'form-control',
					'id' => 'sex',
					'autocomplete' => 'off',
				),
				'options' => array(
					'disable_inarray_validator' => true,
					'label' => 'пол',
					 'value_options' => array(
						  	'0' => 'пол',
                   			 'M' => 'м',
                    	     'F' => 'ж',
                        ),
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