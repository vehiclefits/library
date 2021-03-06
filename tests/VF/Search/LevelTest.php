<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Search_LevelTest extends VF_TestCase
{
    const MAKE = 'Honda';
    const MODEL = 'Civic';
    const YEAR = '2002';

    function testMakeSelected()
    {
        $vehicle = $this->createMMY(self::MAKE, self::MODEL, self::YEAR);
        $request = new Zend_Controller_Request_Http();
        $request->setParam('make', $vehicle->getLevel('make')->getTitle());
        $search = new VF_Search_Form;
        VF_Singleton::getInstance()->setRequest($request);
        $searchlevel = new VF_Search_Level();
        $searchlevel->display($search, 'make');
        $entity = $this->levelFinder()->find('make', $vehicle->getValue('make'));
        $this->assertTrue($searchlevel->isLevelSelected($entity));
    }

    // 0000468: When making an incomplete selection "change" button on my garage produces error
    function testModelSelected()
    {
        $vehicle = $this->createMMY(self::MAKE, self::MODEL, self::YEAR);
        $request = new Zend_Controller_Request_Http();
        $request->setParam('make', $vehicle->getLevel('make')->getTitle());
        $search = new VF_Search_Form;
        VF_Singleton::getInstance()->setRequest($request);
        $searchlevel = new VF_Search_Level();
        $searchlevel->display($search, 'year');
        $request->setParam('make', $vehicle->getLevel('make')->getTitle());
        $request->setParam('model', $vehicle->getLevel('model')->getTitle());
        $entity = $this->levelFinder()->find('year', $vehicle->getValue('year'));
        $this->assertFalse($searchlevel->isLevelSelected($entity));
    }

    function testYearSelected()
    {
        $vehicle = $this->createMMY(self::MAKE, self::MODEL, self::YEAR);
        $request = new Zend_Controller_Request_Http();
        $request->setParam('make', $vehicle->getLevel('make')->getTitle());
        $request->setParam('model', $vehicle->getLevel('model')->getTitle());
        $request->setParam('year', $vehicle->getLevel('year')->getTitle());
        $search = new VF_Search_Form;
        VF_Singleton::getInstance()->setRequest($request);
        $searchlevel = new VF_Search_Level();
        $searchlevel->display($search, 'year');
        $entity = $this->levelFinder()->find('year', $vehicle->getValue('year'));
        $this->assertTrue($searchlevel->isLevelSelected($entity));
    }

    function testYearAlnumSelected()
    {
        $vehicle = $this->createMMY('Honda', 'Civic', '2000');
        $request = new Zend_Controller_Request_Http();
        $request->setParam('make', $vehicle->getLevel('make')->getTitle());
        $request->setParam('model', $vehicle->getLevel('model')->getTitle());
        $request->setParam('year', $vehicle->getLevel('year')->getTitle());
        $search = new VF_Search_Form;
        VF_Singleton::getInstance()->setRequest($request);
        $searchlevel = new VF_Search_Level();
        $searchlevel->display($search, 'year');
        $entity = $vehicle->getLevel('year');
        $this->assertTrue($searchlevel->isLevelSelected($entity));
    }
}