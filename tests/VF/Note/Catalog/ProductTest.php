<?php
/**
 * Vehicle Fits
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to sales@vehiclefits.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Vehicle Fits to newer
 * versions in the future. If you wish to customize Vehicle Fits for your
 * needs please refer to http://www.vehiclefits.com for more information.
 * @copyright  Copyright (c) 2013 Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Elite_Vafnote_model_Catalog_ProductTest extends VF_TestCase
{
    function testShouldFindNumberOfNotes()
    {
        $this->createNoteDefinition('code1', 'this is my message');
        $vehicle = $this->createMMY();
        $product = $this->newNoteProduct(1);
        $this->insertMappingMMY($vehicle, $product->getId());

        $product->addNote($vehicle, 'code1');
        $this->assertEquals(1, $product->numberOfNotes($vehicle), 'should find number of notes for a vehicle');
    }

    function testWhenProductDoesntFitVehicle()
    {
        $this->createNoteDefinition('code1', 'this is my message');
        $vehicle1 = $this->createMMY();
        $vehicle2 = $this->createMMY();
        $product = $this->newNoteProduct(1);
        $product->addNote($vehicle1, 'code1');
        $this->assertEquals(0, $product->numberOfNotes($vehicle2), 'should find 0 notes when product doesnt fit vehicle');
    }

}