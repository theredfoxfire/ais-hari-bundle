<?php

namespace Ais\HariBundle\Tests\Handler;

use Ais\HariBundle\Handler\HariHandler;
use Ais\HariBundle\Model\HariInterface;
use Ais\HariBundle\Entity\Hari;

class HariHandlerTest extends \PHPUnit_Framework_TestCase
{
    const DOSEN_CLASS = 'Ais\HariBundle\Tests\Handler\DummyHari';

    /** @var HariHandler */
    protected $hariHandler;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $om;
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $repository;

    public function setUp()
    {
        if (!interface_exists('Doctrine\Common\Persistence\ObjectManager')) {
            $this->markTestSkipped('Doctrine Common has to be installed for this test to run.');
        }
        
        $class = $this->getMock('Doctrine\Common\Persistence\Mapping\ClassMetadata');
        $this->om = $this->getMock('Doctrine\Common\Persistence\ObjectManager');
        $this->repository = $this->getMock('Doctrine\Common\Persistence\ObjectRepository');
        $this->formFactory = $this->getMock('Symfony\Component\Form\FormFactoryInterface');

        $this->om->expects($this->any())
            ->method('getRepository')
            ->with($this->equalTo(static::DOSEN_CLASS))
            ->will($this->returnValue($this->repository));
        $this->om->expects($this->any())
            ->method('getClassMetadata')
            ->with($this->equalTo(static::DOSEN_CLASS))
            ->will($this->returnValue($class));
        $class->expects($this->any())
            ->method('getName')
            ->will($this->returnValue(static::DOSEN_CLASS));
    }


    public function testGet()
    {
        $id = 1;
        $hari = $this->getHari();
        $this->repository->expects($this->once())->method('find')
            ->with($this->equalTo($id))
            ->will($this->returnValue($hari));

        $this->hariHandler = $this->createHariHandler($this->om, static::DOSEN_CLASS,  $this->formFactory);

        $this->hariHandler->get($id);
    }

    public function testAll()
    {
        $offset = 1;
        $limit = 2;

        $haris = $this->getHaris(2);
        $this->repository->expects($this->once())->method('findBy')
            ->with(array(), null, $limit, $offset)
            ->will($this->returnValue($haris));

        $this->hariHandler = $this->createHariHandler($this->om, static::DOSEN_CLASS,  $this->formFactory);

        $all = $this->hariHandler->all($limit, $offset);

        $this->assertEquals($haris, $all);
    }

    public function testPost()
    {
        $title = 'title1';
        $body = 'body1';

        $parameters = array('title' => $title, 'body' => $body);

        $hari = $this->getHari();
        $hari->setTitle($title);
        $hari->setBody($body);

        $form = $this->getMock('Ais\HariBundle\Tests\FormInterface'); //'Symfony\Component\Form\FormInterface' bugs on iterator
        $form->expects($this->once())
            ->method('submit')
            ->with($this->anything());
        $form->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));
        $form->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($hari));

        $this->formFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($form));

        $this->hariHandler = $this->createHariHandler($this->om, static::DOSEN_CLASS,  $this->formFactory);
        $hariObject = $this->hariHandler->post($parameters);

        $this->assertEquals($hariObject, $hari);
    }

    /**
     * @expectedException Ais\HariBundle\Exception\InvalidFormException
     */
    public function testPostShouldRaiseException()
    {
        $title = 'title1';
        $body = 'body1';

        $parameters = array('title' => $title, 'body' => $body);

        $hari = $this->getHari();
        $hari->setTitle($title);
        $hari->setBody($body);

        $form = $this->getMock('Ais\HariBundle\Tests\FormInterface'); //'Symfony\Component\Form\FormInterface' bugs on iterator
        $form->expects($this->once())
            ->method('submit')
            ->with($this->anything());
        $form->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(false));

        $this->formFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($form));

        $this->hariHandler = $this->createHariHandler($this->om, static::DOSEN_CLASS,  $this->formFactory);
        $this->hariHandler->post($parameters);
    }

    public function testPut()
    {
        $title = 'title1';
        $body = 'body1';

        $parameters = array('title' => $title, 'body' => $body);

        $hari = $this->getHari();
        $hari->setTitle($title);
        $hari->setBody($body);

        $form = $this->getMock('Ais\HariBundle\Tests\FormInterface'); //'Symfony\Component\Form\FormInterface' bugs on iterator
        $form->expects($this->once())
            ->method('submit')
            ->with($this->anything());
        $form->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));
        $form->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($hari));

        $this->formFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($form));

        $this->hariHandler = $this->createHariHandler($this->om, static::DOSEN_CLASS,  $this->formFactory);
        $hariObject = $this->hariHandler->put($hari, $parameters);

        $this->assertEquals($hariObject, $hari);
    }

    public function testPatch()
    {
        $title = 'title1';
        $body = 'body1';

        $parameters = array('body' => $body);

        $hari = $this->getHari();
        $hari->setTitle($title);
        $hari->setBody($body);

        $form = $this->getMock('Ais\HariBundle\Tests\FormInterface'); //'Symfony\Component\Form\FormInterface' bugs on iterator
        $form->expects($this->once())
            ->method('submit')
            ->with($this->anything());
        $form->expects($this->once())
            ->method('isValid')
            ->will($this->returnValue(true));
        $form->expects($this->once())
            ->method('getData')
            ->will($this->returnValue($hari));

        $this->formFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($form));

        $this->hariHandler = $this->createHariHandler($this->om, static::DOSEN_CLASS,  $this->formFactory);
        $hariObject = $this->hariHandler->patch($hari, $parameters);

        $this->assertEquals($hariObject, $hari);
    }


    protected function createHariHandler($objectManager, $hariClass, $formFactory)
    {
        return new HariHandler($objectManager, $hariClass, $formFactory);
    }

    protected function getHari()
    {
        $hariClass = static::DOSEN_CLASS;

        return new $hariClass();
    }

    protected function getHaris($maxHaris = 5)
    {
        $haris = array();
        for($i = 0; $i < $maxHaris; $i++) {
            $haris[] = $this->getHari();
        }

        return $haris;
    }
}

class DummyHari extends Hari
{
}
