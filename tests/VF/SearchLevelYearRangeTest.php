<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_SearchLevelYearRangeTest extends VF_TestCase
{

    function testYearSelected()
    {
        $civic2000 = $this->createMMY('Honda', 'Civic', '2000');
        $civic2001 = $this->createMMY('Honda', 'Civic', '2001');
        $request = new Zend_Controller_Request_Http();
        $request->setParams(array(
            'make' => $civic2000->getValue('make'),
            'model' => $civic2000->getValue('model'),
            'year_start' => $civic2000->getLevel('year')->getId(),
            'year_end' => $civic2000->getLevel('year')->getId()
        ));
        $search = new VF_Search_Form;
        VF_Singleton::getInstance()->setRequest($request);
        $searchlevel = new VF_SearchLevel();
        $searchlevel->display($search, 'year', null, null, 'year_start');
        $this->assertTrue($searchlevel->isLevelSelected($civic2000->getLevel('year')));
        $this->assertFalse($searchlevel->isLevelSelected($civic2001->getLevel('year')));
    }

    function testYearSelected2()
    {
        $civic2000 = $this->createMMY('Honda', 'Civic', '2000');
        $civic2001 = $this->createMMY('Honda', 'Civic', '2001');
        $request = new Zend_Controller_Request_Http();
        $request->setParams(array(
            'make' => $civic2000->getValue('make'),
            'model' => $civic2000->getValue('model'),
            'year_start' => $civic2001->getLevel('year')->getId(),
            'year_end' => $civic2001->getLevel('year')->getId()
        ));
        $search = new VF_Search_Form;
        VF_Singleton::getInstance()->setRequest($request);
        $searchlevel = new VF_SearchLevel();
        $searchlevel->display($search, 'year', null, null, 'year_start');
        $this->assertTrue($searchlevel->isLevelSelected($civic2001->getLevel('year')));
        $this->assertFalse($searchlevel->isLevelSelected($civic2000->getLevel('year')));
    }

    function testYearSelected3()
    {
        $civic2000 = $this->createMMY('Honda', 'Civic', '2000');
        $civic2001 = $this->createMMY('Honda', 'Civic', '2001');
        $request = new Zend_Controller_Request_Http();
        $request->setParams(array(
            'make' => $civic2000->getValue('make'),
            'model' => $civic2000->getValue('model'),
            'year_start' => $civic2000->getLevel('year')->getId(),
            'year_end' => $civic2001->getLevel('year')->getId()
        ));
        $search = new VF_Search_Form;
        VF_Singleton::getInstance()->setRequest($request);
        $searchlevel = new VF_SearchLevel();
        $searchlevel->display($search, 'year', null, null, 'year_start');
        $this->assertTrue($searchlevel->isLevelSelected($civic2000->getLevel('year')));
        $this->assertFalse($searchlevel->isLevelSelected($civic2001->getLevel('year')));
    }

    function testYearSelected_YearEnd()
    {
        $civic2000 = $this->createMMY('Honda', 'Civic', '2000');
        $civic2001 = $this->createMMY('Honda', 'Civic', '2001');
        $request = new Zend_Controller_Request_Http();
        $request->setParams(array(
            'make' => $civic2000->getValue('make'),
            'model' => $civic2000->getValue('model'),
            'year_start' => $civic2000->getLevel('year')->getId(),
            'year_end' => $civic2001->getLevel('year')->getId()
        ));
        $search = new VF_Search_Form;
        VF_Singleton::getInstance()->setRequest($request);
        $searchlevel = new VF_SearchLevel();
        $searchlevel->display($search, 'year', null, null, 'year_end');
        $this->assertFalse($searchlevel->isLevelSelected($civic2000->getLevel('year')));
        $this->assertTrue($searchlevel->isLevelSelected($civic2001->getLevel('year')));
    }
}