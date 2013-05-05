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
class VF_Note_Observer_Importer_MappingsTests_PreviousFitmentTest extends VF_Note_Observer_Importer_MappingsTests_TestCase
{
    protected $product_id;
    
    function doSetUp()
    {
        $this->switchSchema('make,model,year');
        $this->product_id = $this->insertProduct('sku');
    }
    
    function testPreviousFitment()
    {
        $vehicle = $this->createMMY('Honda','Civic','2000');
        $fitmentId = $this->insertMappingMMY($vehicle, $this->product_id);
        
        $this->createNoteDefinition('code','message');
        $this->import('sku,make,model,year,notes' . "\n" .
                      'sku,Honda,Civic,2000,code');
                      
        $fitId = $this->getFitIdForSku('sku');
        $notes = $this->noteFinder()->getNotes( $fitId );
        $this->assertEquals( 1, count($notes), 'fitment notes should supercede previous fitment' );
    }
}