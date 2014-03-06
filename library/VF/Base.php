<?php

/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 *
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @author     Kyle Cannon <kyle.d.cannon@gmail.com>
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
abstract class VF_Base extends VF_Db
{
    protected $schema;
    protected $config;

    public function __construct(VF_Schema $schema, Zend_Db_Adapter_Abstract $adapter, VF_Config $config)
    {
        parent::__construct($adapter);
        $this->schema = $schema;
        $this->config = $config;
    }

    /**
     * @deprecated
     * @see getSchema
     *
     * @return VF_Schema
     */
    public function schema()
    {
        return $this->getSchema();
    }

    /**
     * @return VF_Schema
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * @return VF_Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @param Zend_Config $config
     *
     * @return Zend_Config
     */
    public function setConfig(Zend_Config $config)
    {
        return $this->getConfig()->merge($config);
    }

    /**
     * @deprecated
     * @see getSchemaLevels
     *
     * @return array|null|string
     */
    public function getLevels()
    {
        return $this->getSchemaLevels();
    }

    public function getSchemaLevels()
    {
        return $this->getSchema()->getLevels();
    }


}