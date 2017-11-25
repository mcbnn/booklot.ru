<?php

namespace Application\Form;

use Zend\Form\Form;

class ModelForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('model');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'id_brand',
            'options' => array(
                'disable_inarray_validator' => true,
            ),
        ));
        $this->add(array(
            'name' => 'name_model',
            'attributes' => array(
                'type' => 'text',
                'class' => 'varchartext'
            ),
        ));
        $this->add(array(
            'name' => 'alias_model',
            'attributes' => array(
                'type' => 'text',
                'class' => 'varchartext'
            ),
        ));
        $this->add(array(
            'name' => 'order_model',
            'attributes' => array(
                'type' => 'text',
                'class' => 'varchartext'
            ),
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