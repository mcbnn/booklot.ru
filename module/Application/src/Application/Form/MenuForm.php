<?php

namespace Application\Form;

use Zend\Form\Form;

class MenuForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('menu');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'id_menu_main',
            'options' => array(
                'disable_inarray_validator' => true,
            ),
        ));
        $this->add(array(
            'name' => 'vis_menu',
            'attributes' => array(
                'type' => 'text',

            ),
        ));
        $this->add(array(
            'name' => 'label_menu',
            'attributes' => array(
                'type' => 'text',
                'class' => 'varchartext'
            ),
        ));
        $this->add(array(
            'name' => 'route_menu',
            'attributes' => array(
                'type' => 'text',
                'class' => 'varchartext'
            ),
        ));
        $this->add(array(
            'name' => 'order_menu',
            'attributes' => array(
                'type' => 'text',
                'class' => 'integer'
            ),
        ));
		$this->add(array(
			'type' => 'Zend\Form\Element\Textarea',
			'name' => 'text_menu',
			'attributes' => array(
				'type' => 'text',
				'class' => 'wiswigs'
			),
		));
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'id_type',
            'options' => array(
                'disable_inarray_validator' => true,
            ),
        ));
        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'vis_menu',
            'options' => array(
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0'
            )
        ));
        $this->add(array(
            'name' => 'send',
            'type' => 'Zend\Form\Element\Submit',
            'attributes' => array(
                'class' => 'btn',
                'type' => 'submit',
                'value' => 'Сохранить'
            ),
        ));
	    $this->add(array(
		    'name' => 'foto_menu',
		    'type' => 'Zend\Form\Element\File',
	    ));
        $this->add(array(
            'name' => 'del',
            'type' => 'Zend\Form\Element\Submit',
            'attributes' => array(
                'class' => 'btn',
                'type' => 'submit',
                'value' => 'Удалить',
                'onclick' => 'return confirm(\'delete?\');'
            ),
        ));
    }
}

?>