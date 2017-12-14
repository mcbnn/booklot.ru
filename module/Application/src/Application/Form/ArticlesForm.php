<?php

namespace Application\Form;

use Doctrine\ORM\EntityManager;
use Zend\Form\Form;
use Application\Entity\MZhanr;

class ArticlesForm extends Form
{
    public function __construct(EntityManager $em)
    {
        parent::__construct('articles');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
        $this->add(
            [
                'name'       => 'title',
                'type'       => 'text',
                'options'    => ['label' => 'Title'],
                'attributes' => [
                    'class' => 'form-control input-lg',
                ],
            ]
        );
        $this->add(
            [
                'name'       => 'text',
                'type'       => 'textarea',
                'options'    => ['label' => 'Content'],
                'attributes' => [
                    'class' => 'form-control',
                ],
            ]
        );
        $this->add(
            [
                'name'       => 'menu_id',
                'type'       => 'DoctrineModule\Form\Element\ObjectSelect',
                'options'    => [
                    'label'          => 'Меню',
                    'object_manager' => $em,
                    'target_class'   => MZhanr::class,
                    'property'       => 'name',
                    'find_method'    => [
                        'name'   => 'findBy',
                        'params' => [
                            'criteria' => ['id' => 751],
                        ],
                    ],
                ],
                'attributes' => [
                    'required' => true,
                    'class'    => 'selectboxit',
                ],
            ]
        );
        $this->add(
            [
                'name'       => 'foto',
                'options'    => ['label' => 'Foto Upload'],
                'attributes' => [
                    'type' => 'file',
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