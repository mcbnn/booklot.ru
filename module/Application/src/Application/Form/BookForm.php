<?php

namespace Application\Form;

use Doctrine\ORM\EntityManager;
use Zend\Form\Form;
use Application\Entity\MZhanr;

class BookForm extends Form
{
    public function __construct(EntityManager $em)
    {
        parent::__construct('book');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
        $this->add(
            [
                'name'       => 'name',
                'type'       => 'text',
                'options'    => ['label' => 'Название'],
                'attributes' => [
                    'class' => 'form-control input-lg autosearch',
                    'required' => true,
                ],
            ]
        );
        $this->add(
            [
                'name'       => 'textSmall',
                'type'       => 'textarea',
                'options'    => ['label' => 'Анонс'],
                'attributes' => [
                    'class' => 'form-control input-lg',
                ],
            ]
        );
        $this->add(
            [
                'name'       => 'idBookLitmir',
                'type'       => 'text',
                'options'    => ['label' => 'id_book_litmir'],
                'attributes' => [
                    'class' => 'form-control input-lg',
                ],
            ]
        );
        $this->add(
            [
                'name'       => 'kolStr',
                'type'       => 'text',
                'options'    => ['label' => 'Кол.во страниц'],
                'attributes' => [
                    'class' => 'form-control input-lg',
                ],
            ]
        );
        $this->add(
            [
                'name'       => 'year',
                'type'       => 'text',
                'options'    => ['label' => 'Год'],
                'attributes' => [
                    'class' => 'form-control input-lg',
                ],
            ]
        );
        $this->add(
            [
                'name'       => 'lang',
                'type'       => 'text',
                'options'    => ['label' => 'Язык книги'],
                'attributes' => [
                    'class' => 'form-control input-lg autosearch',
                ],
            ]
        );
        $this->add(
            [
                'name'       => 'langOr',
                'type'       => 'text',
                'options'    => ['label' => 'Язык оригинальной книги'],
                'attributes' => [
                    'class' => 'form-control input-lg autosearch',
                ],
            ]
        );
        $this->add(
            [
                'name'       => 'city',
                'type'       => 'text',
                'options'    => ['label' => 'Город'],
                'attributes' => [
                    'class' => 'form-control input-lg autosearch',
                ],
            ]
        );
        $this->add(
            [
                'name'       => 'isbn',
                'type'       => 'text',
                'options'    => ['label' => 'ISBN'],
                'attributes' => [
                    'class' => 'form-control input-lg autosearch',
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
                'name'       => 'visit',
                'type'       => 'text',
                'options'    => ['label' => 'Просмотров'],
                'attributes' => [
                    'class' => 'form-control input-lg',
                ],
            ]
        );
        $this->add(
            [
                'name'       => 'sort',
                'type'       => 'text',
                'options'    => ['label' => 'sort'],
                'attributes' => [
                    'class' => 'form-control input-lg',
                    'values' => 0
                ],
            ]
        );
        $this->add(
            [
                'name'       => 'stars',
                'type'       => 'text',
                'options'    => ['label' => 'Звезд'],
                'attributes' => [
                    'class' => 'form-control input-lg',
                    'values' =>  3
                ],
            ]
        );
        $this->add(
            [
                'name'       => 'urlPartner',
                'type'       => 'text',
                'options'    => ['label' => 'Ссылка партнера'],
                'attributes' => [
                    'class' => 'form-control input-lg',
                ],
            ]
        );
        $this->add(
            [
                'name'       => 'menu',
                'type'       => 'DoctrineModule\Form\Element\ObjectSelect',
                'options'    => [
                    'label'          => 'Меню',
                    'object_manager' => $em,
                    'target_class'   => MZhanr::class,
                    'property'       => 'name',
                ],
                'attributes' => [
                    'required' => true,
                    'class'    => 'select2',
                ],
            ]
        );
        $this->add(
            [
                'name'       => 'foto',
                'type' => 'file',
                'options'    => ['label' => 'Фото'],
                'attributes' => [
                    'class' => 'form-control',
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