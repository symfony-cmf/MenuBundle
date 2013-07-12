<?php

namespace Symfony\Cmf\Bundle\MenuBundle\Tests\Functional\Admin\MenuNodeAdminTest;

use Symfony\Cmf\Component\Testing\Functional\BaseTestCase;

class MenuAdminTest extends BaseTestCase
{
    public function setUp()
    {
        $this->db('PHPCR')->loadFixtures(array(
            'Symfony\Cmf\Bundle\MenuBundle\Tests\Functional\DataFixtures\PHPCR\LoadMenuData',
        ));
        $this->client = $this->createClient();
    }

    public function testMenuList()
    {
        $crawler = $this->client->request('GET', '/admin/bundle/menu/menu/list');
        $res = $this->client->getResponse();
        $this->assertEquals(200, $res->getStatusCode());
        $this->assertCount(1, $crawler->filter('html:contains("test-menu")'));
    }

    public function testMenuEdit()
    {
        $crawler = $this->client->request('GET', '/admin/bundle/menu/menu/test/test-menu/edit');
        $res = $this->client->getResponse();
        $this->assertEquals(200, $res->getStatusCode());
        $this->assertCount(1, $crawler->filter('input[value="test-menu"]'));
    }

    public function testMenuCreate()
    {
        $crawler = $this->client->request('GET', '/admin/bundle/menu/menu/create');
        $res = $this->client->getResponse();
        $this->assertEquals(200, $res->getStatusCode());
    }
}
