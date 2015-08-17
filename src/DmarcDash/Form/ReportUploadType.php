<?php



namespace DmarcDash\Form;



use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;



class ReportUploadType extends AbstractType
{



    /**
     * Return entity type?
     *
     * @return   string
     */
    public function getName ()
    {
        return 'ReportUpload';
    }



    /**
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'domain'     => '',
        ));
    }



    /**
     * Build form
     *
     * @return Form?
     */
    public function buildForm (FormBuilderInterface $builder, array $options)
    {
        $builder->add('reportFile', 'file', array(
            'label' => 'Choose report files (.zip, .gz, .xml)',
            'multiple' => true,
            'attr'  => array(
                'autofocus' => 'true',
                'accept'    => '.zip, .gz, .xml',
            ),
        ));


        // This gets populated with domain for which this report is really for
        $builder->add('reportDomainName', 'hidden', array(
            'required' => false,
        ));


        // Submit button
        $submitLabel = "Start upload";
        $builder->add('submit', 'submit', array(
            'label' => $submitLabel,
            'attr'  => array(
                'class' => 'btn btn-success',
            ),
        ));
    }
}
