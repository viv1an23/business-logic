<?php

namespace App\Form;

use App\Entity\Player;
use App\Repository\PlayerRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @template-extends AbstractType<int>
 */
class AddPlayersType extends AbstractType
{
    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'players',
                EntityType::class,
                [
                    'class' => Player::class,
                    'label' => 'Players *',
                    'required' => true,
                    'mapped' => true,
                    'multiple' => true,
                    'query_builder' => function (PlayerRepository $er) {
                        return $er->createQueryBuilder('p')
                            ->where('p.team IS NULL');
                    },
                    'choice_label' => 'name',
                    'choice_value' => 'id',
                    'attr' => [
                        'class' => 'select',
                    ],
                ]
            );
    }

    /**
     * {@inheritDoc }
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
