<?php

namespace Symfony\Cmf\Bundle\MenuBundle;

use Knp\Menu\NodeInterface;
use Knp\Menu\Silex\RouterAwareFactory;
use Knp\Menu\MenuItem;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ContentAwareFactory extends RouterAwareFactory
{
    protected $contentRouter;
    protected $container;

    /**
     * @param Container $container to fetch the request in order to determine
     *      whether this is the current menu item
     * @param UrlGeneratorInterface $generator for the parent class
     * @param UrlGeneratorInterface $contentRouter to generate routes when
     *      content is set
     * @param string $routeName the name of the route to use. DynamicRouter
     *      ignores this.
     */
    public function __construct(ContainerInterface $container, UrlGeneratorInterface $generator, UrlGeneratorInterface $contentRouter, $contentKey, $routeName = null)
    {
        parent::__construct($generator);
        $this->contentRouter = $contentRouter;
        $this->container = $container;
        $this->contentKey = $contentKey;
        $this->routeName = $routeName;
    }

    /**
     * Create a menu item from a NodeInterface
     *
     * @param NodeInterface $node
     * @return MenuItem
     */
    public function createFromNode(NodeInterface $node)
    {
        $item = $this->createItem($node->getName(), $node->getOptions());

        foreach ($node->getChildren() as $childNode) {
            if ($childNode instanceof NodeInterface) {
                $item->addChild($this->createFromNode($childNode));
            }
        }

        return $item;
    }

    public function createItem($name, array $options = array())
    {
        $current = false;
        if (!empty($options['content'])) {
            try {
                $request = $this->container->get('request');
                if ($options['content'] instanceof Route
                    && $options['content']->getOption('currentUriPrefix')
                    && 0 === strpos($request->getPathinfo(), $options['content']->getOption('currentUriPrefix'))
                ) {
                    $current = true;
                } elseif ($request->attributes->get($this->contentKey) === $options['content']) {
                    $current = true;
                }
            } catch (\Exception $e) {}

            $routeParameters = $options['routeParameters'];
            $options['uri'] = $this->contentRouter->generate($options['content'], $routeParameters, $options['routeAbsolute']);
            unset($options['route']);
        }

        $item = parent::createItem($name, $options);
        if ($current) {
            $item->setCurrent(true);
        }

        return $item;
    }
}
