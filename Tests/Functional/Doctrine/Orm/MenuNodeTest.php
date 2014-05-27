<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2014 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\Functional\Doctrine\Orm;

use Doctrine\ODM\PHPCR\ObjectManagerInterface;
use Symfony\Cmf\Bundle\MenuBundle\Tests\Resources\Document\Content;
use Symfony\Cmf\Component\Testing\Functional\BaseTestCase;
use Symfony\Cmf\Bundle\MenuBundle\Doctrine\Orm\MenuNode;

class MenuNodeTest extends BaseTestCase
{
    private $content;

    /**
     * @var ObjectManagerInterface
     */
    private $em;

    private $rootDocument;

    /**
     * @var MenuNode
     */
    private $child1;

    public function setUp()
    {
        $this->em = $this->db('ORM')->getOm();

        $this->child1 = new MenuNode;
        $this->child1->setName('child1');
    }

    protected function getKernelConfiguration()
    {
        return array(
            'environment' => 'orm',
        );
    }

    public function testMenuNode()
    {
        $data = array(
            'name' => 'test-node',
            'label' => 'label_foobar',
            'uri' => 'http://www.example.com/foo',
            'route' => 'foo_route',
            'linkType' => 'route',
            'publishable' => false,
            'publishStartDate' => new \DateTime('2013-06-18'),
            'publishEndDate' => new \DateTime('2013-06-18'),
            'attributes' => array(
                'attr_foobar_1' => 'barfoo',
                'attr_foobar_2' => 'barfoo',
            ),
            'childrenAttributes' => array(
                'child_foobar_1' => 'barfoo',
                'child_foobar_2' => 'barfoo',
            ),
            'linkAttributes' => array(
                'link_foobar_1' => 'barfoo',
                'link_foobar_2' => 'barfoo',
            ),
            'labelAttributes' => array(
                'label_foobar_1' => 'barfoo',
                'label_foobar_2' => 'barfoo',
            ),
            'extras' => array(
                'extra_foobar_1' => 'barfoo',
                'extra_foobar_2' => 'barfoo',
            ),
            'routeParameters' => array(
                'route_param_foobar_1' => 'barfoo',
                'route_param_foobar_2' => 'barfoo',
            ),
            'routeAbsolute' => true,
            'display' => false,
            'displayChildren' => false,
        );

        $startDateString = $data['publishStartDate']->format('Y-m-d');
        $endDateString = $data['publishEndDate']->format('Y-m-d');

        $menuNode = new MenuNode;
        $refl = new \ReflectionClass($menuNode);

        foreach ($data as $key => $value) {
            $refl = new \ReflectionClass($menuNode);
            $prop = $refl->getProperty($key);
            $prop->setAccessible(true);
            $prop->setValue($menuNode, $value);
        }

        $menuNode->addChild($this->child1);

        $this->em->persist($menuNode);
        $this->em->flush();
        $this->em->clear();

        $menuNode = $this->em->find(null, '/test/test-node');

        $this->assertNotNull($menuNode);

        foreach ($data as $key => $value) {
            $prop = $refl->getProperty($key);
            $prop->setAccessible(true);
            $v = $prop->getValue($menuNode);

            if (!is_object($value)) {
                $this->assertEquals($value, $v);
            }
        }

        // test objects
        $prop = $refl->getProperty('content');
        $prop->setAccessible(true);
        $content = $prop->getValue($menuNode);
        $this->assertEquals('fake_weak_content', $content->getName());

        // test children
        $this->assertCount(1, $menuNode->getChildren());

        // test publish start and end
        $publishStartDate = $menuNode->getPublishStartDate();
        $publishEndDate = $menuNode->getPublishEndDate();

        $this->assertInstanceOf('\DateTime', $publishStartDate);
        $this->assertInstanceOf('\DateTime', $publishEndDate);
        $this->assertEquals($startDateString, $publishStartDate->format('Y-m-d'));
        $this->assertEquals($endDateString, $publishEndDate->format('Y-m-d'));
    }
}
