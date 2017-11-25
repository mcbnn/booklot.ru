<?php

namespace Application\Form;

use Zend\Form\Form;

class SectionForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('section');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
        $this->add(array(
            'name' => 'name_section',
            'attributes' => array(
                'type' => 'text',
            ),
        ));
        $this->add(array(
            'name' => 'alias_section',
            'attributes' => array(
                'type' => 'text',
            ),
        ));
        $this->add(array(
            'name' => 'order_section',
            'attributes' => array(
                'type' => 'text',
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