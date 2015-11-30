<?php

namespace Ais\HariBundle\Tests\Fixtures\Entity;

use Ais\HariBundle\Entity\Hari;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;


class LoadHariData implements FixtureInterface
{
    static public $haris = array();

    public function load(ObjectManager $manager)
    {
        $hari = new Hari();
        $hari->setTitle('title');
        $hari->setBody('body');

        $manager->persist($hari);
        $manager->flush();

        self::$haris[] = $hari;
    }
}
