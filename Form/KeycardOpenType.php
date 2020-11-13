<?php
/**
 * Created by PhpStorm.
 * User: mbaechtel
 * Date: 18/11/14
 * Time: 14:20.
 */

namespace Eliberty\Bundle\FormElementTypeBundle\Form;

use Eliberty\Bundle\FormElementTypeBundle\Form\DataTransformer\SkiCardTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class KeycardOpenType.
 */
class KeycardOpenType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $baseOptions = $luhnOptions = [
            'required' => $options['required'],
            'label'    => ' - ',
        ];
        $baseOptions['label_render'] = false;
        $baseOptions['attr']         = ['maxlength' => 10, 'class' => 'keycard-part-base'];
        $luhnOptions['attr']         = ['maxlength' => 1, 'class' => 'keycard-part-luhn'];

        $builder
            ->add('base', TextType::class, $baseOptions)
            ->add('luhn', TextType::class, $luhnOptions)
            ->addViewTransformer($this->getTransformer());
    }

    /**
     * @return SkiCardTransformer
     */
    public function getTransformer()
    {
        return new SkiCardTransformer(['base', 'luhn']);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $compound = function (Options $options) {
            return true;
        };

        $emptyValue = $emptyValueDefault = function (Options $options) {
            return $options['required'] ? null : '';
        };

        $emptyValueNormalizer = function (Options $options, $emptyValue) use ($emptyValueDefault) {
            if (\is_array($emptyValue)) {
                $default = $emptyValueDefault($options);

                return array_merge(
                    ['base' => $default, 'luhn' => $default],
                    $emptyValue
                );
            }

            return [
                'base' => $emptyValue,
                'luhn' => $emptyValue,
            ];
        };

        $resolver->setDefaults(
            [
                'compound'       => $compound,
                'required'       => false,
                'empty_value'    => $emptyValue,
                'error_bubbling' => true,
                'data_class'     => null,
            ]
        );

        $resolver->setNormalizer('empty_value', $emptyValueNormalizer);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'eliberty_keycard_open';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}
