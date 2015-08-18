<?php
/**
 * User: José Ramón Fernandez Leis
 * Email: jdeveloper.inxenio@gmail.com
 * Date: 17/08/15
 * Time: 16:29
 */

namespace Ant\Bundle\ApiSocialBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ReportPhotoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('reason', 'textarea', array('required' => true));
    }

    public function getName()
    {
        return 'photo_report';
    }
}