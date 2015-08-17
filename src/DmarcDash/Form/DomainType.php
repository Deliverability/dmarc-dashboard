<?php



namespace DmarcDash\Form;



use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;



class DomainType extends AbstractType
{



    public function getName()
    {
        return 'Domain';
    }



    public function buildForm (FormBuilderInterface $builder, array $options)
    {
        $builder->add('domainName', 'text', array(
            'label' => 'New domain to add',
            'attr'  => array(
                'autofocus' => 'true',
            ),
        ));



        // Submit button
        $builder->add('submit', 'submit', array(
            'label' => 'Add',
            'attr'  => array(
                'class' => 'btn btn-success',
            ),
        ));
    }
}
