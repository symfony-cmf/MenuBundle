<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\Document;
use Symfony\Cmf\Bundle\MenuBundle\Document\MenuNode;

class MenuNodeTest extends \PHPUnit_Framework_Testcase
{
    public function setUp()
    {
        $c1 = new MenuNode;
        $c1->setLabel('Child 1');
        $c2 = new MenuNode;
        $c2->setLabel('Child 2');
        $this->content = new \StdClass;
        $this->parentNode = new MenuNode;
        $this->node = new MenuNode;
        $this->node->setId('/foo/bar')
            ->setParent($this->parentNode)
            ->setName('test')
            ->setLabel('Test')
            ->setUri('http://www.example.com')
            ->setRoute('test_route')
            ->setContent($this->content)
            ->setWeak(false)
            ->setAttributes(array('foo' => 'bar'))
            ->setChildrenAttributes(array('bar' => 'foo'))
            ->setExtras(array('far' => 'boo'))
            ->setLinkAttributes(array('link' => 'knil'))
            ->setLabelAttributes(array('label' => 'lebal'))
            ->setDisplay(false)
            ->setDisplayChildren(false)
            ->setRouteAbsolute(true);
    }

    public function testGetters()
    {
        $this->assertSame($this->parentNode, $this->node->getParent());
        $this->assertEquals('test', $this->node->getName());
        $this->assertEquals('Test', $this->node->getLabel());
        $this->assertEquals('http://www.example.com', $this->node->getUri());
        $this->assertEquals('test_route', $this->node->getRoute());
        $this->assertSame($this->content, $this->node->getContent());
        $this->assertFalse($this->node->getWeak());
        $this->assertEquals(array('foo' => 'bar'), $this->node->getAttributes());
        $this->assertEquals('bar', $this->node->getAttribute('foo'));
        $this->assertEquals(array('bar' => 'foo'), $this->node->getChildrenAttributes());
        $this->assertEquals(array('far' => 'boo'), $this->node->getExtras());

        $this->parentNode = new MenuNode;
        $this->node->setPosition($this->parentNode, 'FOOO');
        $this->assertSame($this->parentNode, $this->node->getParent());
        $this->assertEquals('FOOO', $this->node->getName());
        $this->assertEquals(array('link' => 'knil'), $this->node->getLinkAttributes());
        $this->assertEquals(array('label' => 'lebal'), $this->node->getLabelAttributes());
        $this->assertFalse($this->node->getDisplay());
        $this->assertFalse($this->node->getDisplayChildren());
        $this->assertTrue($this->node->getRouteAbsolute());
    }

    public function testAddChild()
    {
        $c1 = new MenuNode;
        $c2 = new MenuNode;
        $m = new MenuNode;
        $m->addChild($c1);
        $ret = $m->addChild($c2);

        $children = $m->getChildren();
        $this->assertCount(2, $children);
        $this->assertSame($m, $children[0]->getParent());
        $this->assertSame($c2, $ret);
    }

    /**
     * @depends testGetters
     */
    public function testGetOptions()
    {
        $this->assertEquals(array(
            'uri' => $this->node->getUri(),
            'route' => $this->node->getRoute(),
            'label' => $this->node->getLabel(),
            'attributes' => $this->node->getAttributes(),
            'childrenAttributes' => $this->node->getChildrenAttributes(),
            'display' => $this->node->getDisplay(),
            'displayChildren' => $this->node->getDisplayChildren(),
            'content' => $this->node->getContent(),
            'routeParameters' => $this->node->getRouteParameters(),
            'routeAbsolute' => $this->node->getRouteAbsolute(),
            'linkAttributes' => $this->node->getLinkAttributes(),
            'labelAttributes' => $this->node->getLabelAttributes(),
        ), $this->node->getOptions());
    }
}
