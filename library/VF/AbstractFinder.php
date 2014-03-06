<?php


abstract class VF_AbstractFinder extends VF_Base
{

    /** @var VF_Level_Finder */
    protected $levelFinder;
    /** @var  VF_Level_Finder|VF_Level_Finder_Selector */
    protected $vehicleFinder;

    public function __construct(
        VF_Schema $schema,
        Zend_Db_Adapter_Abstract $adapter,
        VF_Config $config,
        VF_Level_Finder $levelFinder,
        VF_Vehicle_Finder $vehicleFinder
    ) {
        parent::__construct($schema, $adapter, $config);
        $this->levelFinder = $levelFinder;
        $this->vehicleFinder = $vehicleFinder;

    }

    /**
     * @deprecated
     * @see getLevelFinder
     *
     * @return VF_Level_Finder|VF_Level_Finder_Selector
     */
    protected function levelFinder()
    {
        return $this->getLevelFinder();
    }

    /**
     * @return VF_Vehicle_Finder
     */
    protected function getVehicleFinder()
    {
        return $this->vehicleFinder;
    }

    /**
     * @return VF_Level_Finder|VF_Level_Finder_Selector
     */
    protected function getLevelFinder()
    {
        return $this->levelFinder;
    }

} 