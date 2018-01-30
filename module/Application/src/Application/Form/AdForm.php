<?php

namespace Application\Form;

use Doctrine\ORM\EntityManager;
use Zend\Form\Form;

class AdForm extends Form
{
    public function __construct(EntityManager $em)
    {
        parent::__construct('ad');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
        $this->add(
            [
                'name'       => 'name',
                'type'       => 'text',
                'options'    => ['label' => 'Название'],
                'attributes' => [
                    'class' => 'form-control input-lg autosearch',
                    'required'  => true,
                    'data-name' => 'b_name'
                ],
            ]
        );
        $this->add(
            [
                'name'       => 'text',
                'type'       => 'textarea',
                'options'    => ['label' => 'Код'],
                'attributes' => [
                ],
            ]
        );
        $this->add(
            [
                'name'       => 'vis',
                'type'       => 'text',
                'options'    => ['label' => 'Показать'],
                'attributes' => [
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