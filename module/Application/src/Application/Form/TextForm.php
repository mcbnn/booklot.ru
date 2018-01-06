<?php

namespace Application\Form;

use Doctrine\ORM\EntityManager;
use Zend\Form\Form;

class TextForm extends Form
{
    public function __construct(EntityManager $em)
    {
        parent::__construct('text');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
        $this->add(
            [
                'name'       => 'text',
                'type'       => 'textarea',
                'options'    => ['label' => 'Content'],
                'attributes' => [
                    'required' => true,
                    'class' => 'wysihtml5',
                ],
            ]
        );
        $this->add(
            [
                'name'       => 'num',
                'type'       => 'text',
                'options'    => ['label' => 'Страница'],
                'attributes' => [
                    'required' => true,
                    'class' => 'form-control input-lg',
                ],
            ]
        );
        $this->add(
            [
                'name'       => 'submit',
                'attributes' => [
                    'type'  => 'submit',
                    'value' => 'Сохранить',
                    'id'    => 'submitbutton',
                    'class' => 'btn btn-success my-s',
                ],
            ]
        );
    }
}