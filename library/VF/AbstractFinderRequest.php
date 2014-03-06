<?php


abstract class VF_AbstractFinderRequest extends VF_AbstractFinder
{
    /** @var \Zend_Controller_Request_Abstract */
    protected $request;

    public function __construct(
        VF_Schema $schema,
        Zend_Db_Adapter_Abstract $adapter,
        VF_Config $config,
        VF_Level_Finder $levelFinder,
        VF_Vehicle_Finder $vehicleFinder,
        Zend_Controller_Request_Abstract $request
    ) {
        parent::__construct($schema, $adapter, $config, $levelFinder, $vehicleFinder);
        $this->request = $request;


    }

    /**
     * @return Zend_Controller_Request_Abstract
     */
    protected function getRequest()
    {
        return $this->request;
    }

} 