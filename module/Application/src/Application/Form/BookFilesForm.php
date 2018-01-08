<?php

namespace Application\Form;

use Doctrine\ORM\EntityManager;
use Zend\Form\Form;
use Application\Entity\MZhanr;

class BookFilesForm extends Form
{
    public function __construct(EntityManager $em)
    {
        parent::__construct('bookFiles');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
        $this->add(
            [
                'name'       => 'file',
                'type' => 'file',
                'options'    => ['label' => 'File'],
                'attributes' => [
                    'class' => 'form-control',
                    'required' => true
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