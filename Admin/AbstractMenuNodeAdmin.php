<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MenuBundle\Admin;

use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrinePHPCRAdminBundle\Admin\Admin;
use Symfony\Cmf\Bundle\MenuBundle\Model\MenuNodeBase;

/**
 * Common base admin for Menu and MenuNode
 */
abstract class AbstractMenuNodeAdmin extends Admin
{
    /**
     * @var string
     */
    protected $contentRoot;

    /**
     * @var string
     */
    protected $menuRoot;

    /**
     * @var string
     */
    protected $translationDomain = 'CmfMenuBundle';

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->addIdentifier('id', 'text')
            ->add('name', 'text')
            ->add('label', 'text')
            ->add('uri', 'text')
            ->add('route', 'text')
            ;
    }

    /**
     * {@inheritDoc}
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->tab('form.tab_general')
                ->with('form.group_general')
                    ->add('name', 'text')
                    ->add('label', 'text')
        ;

        if (null === $this->getParentFieldDescription()) {
            // Add the choice for the node links "target"
            $formMapper
                ->add('linkType', 'choice_field_mask', array(
                    'choices' => array(
                            'route' => 'route',
                            'uri' => 'uri',
                            'content' => 'content',
                    ),
                    'map' => array(
                        'route' => array('route'),
                        'uri' => array('uri'),
                        'content' => array('content', 'doctrine_phpcr_odm_tree'),
                    ),
                    'empty_value' => 'auto',
                    'required' => false
                ))
                ->add('route', 'text', array('required' => false))
                ->add('uri', 'text', array('required' => false))
                ->add('content', 'doctrine_phpcr_odm_tree',
                    array(
                        'root_node' => $this->contentRoot,
                        'choice_list' => array(),
                        'required' => false
                    )
                )
            ;
        }

        $formMapper->end() // group general
            ->end() // tab general
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('name')
            ->add('label')
            ->add('uri')
            ->add('content', null, array('associated_property' => 'title'))
        ;
    }

    public function getExportFormats()
    {
        return array();
    }

    public function setContentRoot($contentRoot)
    {
        $this->contentRoot = $contentRoot;
    }

    public function setMenuRoot($menuRoot)
    {
        $this->menuRoot = $menuRoot;
    }

    public function setContentTreeBlock($contentTreeBlock)
    {
        $this->contentTreeBlock = $contentTreeBlock;
    }

    public function toString($object)
    {
        if ($object instanceof MenuNodeBase && $object->getLabel()) {
            return $object->getLabel();
        }

        return $this->trans('link_add', array(), 'SonataAdminBundle');
    }
}
