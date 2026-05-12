<?php
namespace App\Form;
use App\Entity\Meuble;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
class MeubleType extends AbstractType{
    public function buildForm(
        FormBuilderInterface $builder,array $options):void
        {
            $builder
                ->add('nom')
                ->add('description',TextareaType::class,['required'=>false])
                ->add('prix',MoneyType::class,['currency'=>'TND'])
                ->add('stock')
                ->add('image',TextType::class,['required'=>false,'label'=>'URL image'])
                ->add('categorie')
                ->add('Ajouter',SubmitType::class,['attr'=>['class'=>'btn btn-primary rounded-pill px-4']]);
        }
    public function configureOptions(OptionsResolver $resolver):void{
        $resolver->setDefaults(['data_class'=>Meuble::class]);
    }
}
