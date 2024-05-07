<?php

namespace App\Form;

use App\Entity\Task;
use App\Entity\Column;
use App\Repository\ColumnRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $project = $options['projet'];

        $builder
            ->add('title')
            ->add('content')
            ->add('position', EntityType::class, [
                'class' => Column::class,
                'query_builder' => function (ColumnRepository $cr) use ($project): QueryBuilder {
                    return $cr->createQueryBuilder('c')
                        ->andWhere('c.project = :project')
                        ->setParameter('project', $project)
                        ->orderBy('c.title', 'ASC');
                },
                'choice_label' => 'title',
                'autocomplete' => true,
            ])
            ->add('deadLine', null, [
                'widget' => 'single_text',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ])
            ->setRequired('projet');
    }
}