<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\Functional\Admin\Document;

use Symfony\Cmf\Component\Testing\Functional\BaseTestCase;
use Symfony\Cmf\Bundle\MenuBundle\Document\MenuNode;
use Symfony\Cmf\Component\Testing\Document\Content;
use Doctrine\ODM\PHPCR\Document\Generic;

class MenuNodeTest extends BaseTestCase
{
    public function setUp()
    {
        $this->db('PHPCR')->createTestNode();
        $this->dm = $this->db('PHPCR')->getOm();
        $this->baseNode = $this->dm->find(null, '/test');

        $this->weakContent = new Content;
        $this->weakContent->setParent($this->baseNode);
        $this->weakContent->setName('fake_weak_content');
        $this->dm->persist($this->weakContent);

        $this->hardContent = new Content;
        $this->hardContent->setParent($this->baseNode);
        $this->hardContent->setName('fake_hard_content');
        $this->dm->persist($this->hardContent);

        $this->child1 = new MenuNode;
        $this->child1->setName('child1');
    }

    protected function getNewInstance()
    {
        return new MenuNode;
    }

    public function testMenuNode()
    {
        $data = array(
            'name' => 'test-node',
            'label' => 'label_foobar',
            'uri' => 'http://www.example.com/foo',
            'route' => 'foo_route',
            'weakContent' => $this->weakContent,
            'hardContent' => $this->hardContent,
            'attributes' => array(
                'attr_foobar_1' => 'barfoo',
                'attr_foobar_2' => 'barfoo',
            ),
            'childrenAttributes' => array(
                'child_foobar_1' => 'barfoo',
                'child_foobar_2' => 'barfoo',
            ),
            'extras' => array(
                'extra_foobar_1' => 'barfoo',
                'extra_foobar_2' => 'barfoo',
            ),
            'routeParameters' => array(
                'route_param_foobar_1' => 'barfoo',
                'route_param_foobar_2' => 'barfoo',
            ),
        );

        $menuNode = $this->getNewInstance();
        $refl = new \ReflectionClass($menuNode);

        $menuNode->setParent($this->baseNode);

        foreach ($data as $key => $value) {
            $refl = new \ReflectionClass($menuNode);
            $prop = $refl->getProperty($key);
            $prop->setAccessible(true);
            $prop->setValue($menuNode, $value);
        }

        $menuNode->addChild($this->child1);

        $this->dm->persist($menuNode);
        $this->dm->flush();
        $this->dm->clear();

        $menuNode = $this->dm->find(null, '/test/test-node');

        $this->assertNotNull($menuNode);

        foreach ($data as $key => $value) {
            $prop = $refl->getProperty($key);
            $prop->setAccessible(true);
            $v = $prop->getValue($menuNode);

            if (is_scalar($value)) {
                $this->assertEquals($value, $v);
            }
        }

        // test objects
        $prop = $refl->getProperty('weakContent');
        $prop->setAccessible(true);
        $content = $prop->getValue($menuNode);
        $this->assertEquals('fake_weak_content', $content->getName());

        $prop = $refl->getProperty('hardContent');
        $prop->setAccessible(true);
        $content = $prop->getValue($menuNode);
        $menuNode = $this->dm->find(null, '/test/test-node');
        $content = $menuNode->getContent();
        $this->assertEquals('fake_weak_content', $content->getName());

        // test children
        $this->assertCount(1, $menuNode->getChildren());
    }
}
