<?php

namespace Application\Form;

use Doctrine\ORM\EntityManager;
use Zend\Form\Form;
use Application\Entity\MZhanr;

class FilesParseForm extends Form
{
    public function __construct(EntityManager $em)
    {
        parent::__construct('FilesParse');
        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
        $this->add(
            [
                'name'       => 'file',
                'type'       => 'file',
                'options'    => ['label' => 'File'],
                'attributes' => [
                    'class'    => 'form-control',
                    'required' => true,
                    'multiple' => true,
                ],
            ]
        );
	    $this->add(
		    [
			    'name'       => 'm_serii',
			    'type'       => 'text',
			    'options'    => ['label' => 'Серия'],
			    'attributes' => [
				    'class' => 'form-control input-lg',
			    ],
		    ]
	    );
        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'validation',
            'options' => array(
                'use_hidden_element' => true,
                'checked_value' => '1',
                'unchecked_value' => '0',
                'label' => 'Валидация'
            ),
            'attributes' => [
                'value' => '1',
            ],

        ));
        $this->add(
            [
                'name'       => 'submit',
                'attributes' => [
                    'type'  => 'submit',
                    'value' => 'Сохранить',
                    'id'    => 'submitbutton',
                    'class' => 'btn btn-success my-s margin-bootom',
                ],
            ]
        );
    }
}