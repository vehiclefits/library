<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
abstract class VF_Import extends VF_Import_Abstract implements VF_Configurable
{
    const E_WARNING = 1;

    protected $count = 0;

    /** @var VF_Vehicle_Finder */
    protected $vehicleFinder;
    /** @var Zend_Config */
    protected $config;

    function __construct(
        $file,
        VF_Schema $schema,
        Zend_Db_Adapter_Abstract $adapter,
        Zend_Config $config,
        VF_Level_Finder $levelFinder,
        VF_Vehicle_Finder $vehicleFinder
    ) {
        parent::__construct($file, $schema, $adapter, $config, $levelFinder, $vehicleFinder);
        $this->vehicleFinder = $vehicleFinder;
    }

    abstract function import();

    function doImport()
    {
        $this->insertRowsIntoTempTable();
        $this->insertLevelsFromTempTable();
        $this->insertFitmentsFromTempTable();
        $this->insertVehicleRecords();
        $this->cleanupTempTable();
        $this->runDeprecatedImports();
    }

    function runDeprecatedImports()
    {
    }

    function cleanupTempTable()
    {
        $this->query('DELETE FROM elite_import');
    }

    function insertLevelsFromTempTable()
    {
        foreach ($this->getSchema()->getLevels() as $level) {
            $this->updateIdsInTempTable($level);
            $this->extractLevelsFromImportTable($level);
            $this->updateIdsInTempTable($level);
        }
    }

    function extractLevelsFromImportTable($level)
    {
        if (!$this->getSchema()->hasParent($level)) {
            $sql = sprintf('INSERT INTO ' . $this->getSchema()->levelTable($level) . ' (title)
                SELECT DISTINCT %1$s FROM elite_import WHERE universal != 1 && `%1$s` != "" && %1$s_id = 0', str_replace(' ', '_', $level));
            $this->query($sql);
        } else {
            $sql = sprintf('INSERT INTO `' . $this->getSchema()->levelTable($level) . '` (`title`)
                    SELECT DISTINCT `%1$s` FROM `elite_import` WHERE `%1$s` != "" && universal != 1 && `%1$s_id` = 0',
                str_replace(' ', '_', $level)
            );
            $this->query($sql);
        }
    }

    function updateIdsInTempTable($level)
    {
        $sql = sprintf(
            'UPDATE elite_import i, `' . $this->getSchema()->levelTable($level) . '` l
                SET i.`%1$s_id` = l.id
                WHERE i.`%1$s` = l.title',
            str_replace(' ', '_', $level)
        );
        $this->query($sql);
    }

    function insertVehicleRecords()
    {
        $cols = array();
        foreach ($this->getSchema()->getLevels() as $i => $col) {
            $cols[] = $this->getReadAdapter()->quoteIdentifier(str_replace(' ', '_', $col));
            $cols[] = $this->getReadAdapter()->quoteIdentifier(str_replace(' ', '_', $col) . '_id');
        }
        $query = 'REPLACE INTO ' . $this->getSchema()->definitionTable() . ' (' . implode(',', $cols) . ')';
        $query .= ' SELECT DISTINCT ' . implode(',', $cols) . ' FROM elite_import WHERE universal != 1';
        $this->query($query);
    }

    function columns($row)
    {
        $newRow = array();
        foreach ($row as $key => $value) {
            $newRow[str_replace(' ', '_', $key)] = $value;
        }
        return $newRow;
    }

    function insertFitmentsFromTempTable()
    {
    }

    /** @return array Field positions keyed by the field's names */
    function getFieldPositions()
    {
        if (isset($this->fieldPositions)) {
            return $this->fieldPositions;
        }
        parent::getFieldPositions();
        if (false == $this->fieldPositions) {
            throw new VF_Import_VehiclesList_CSV_Exception_FieldHeaders('Field headers missing');
        }
        foreach ($this->getSchema()->getLevels() as $level) {
            if (!$this->allowMissingFields() &&
                !isset($this->fieldPositions[$level]) && (
                    !isset($this->fieldPositions[$level . '_start']) && !isset($this->fieldPositions[$level . '_end'])) &&
                !isset($this->fieldPositions[$level . '_range'])
            ) {
                throw new VF_Import_VehiclesList_CSV_Exception_FieldHeaders('Unable to locate field header for [' . $level . '], perhaps not using comma delimiter' . print_r($this->fieldPositions, 1));
            }
        }
        return $this->fieldPositions;
    }

    function allowMissingFields()
    {
        if ($this->getConfig()->importer->allowMissingFields === true) {
            return true;
        }
        return $this->getConfig()->importer->allowMissingFields != false && $this->getConfig()->importer->allowMissingFields != 'false';
    }

    function getConfig()
    {
        return $this->config;
    }

    function setConfig(Zend_Config $config)
    {
        $this->config = $this->getConfig()->merge($config);
    }

    function getVehicleFinder()
    {
        return $this->vehicleFinder;
    }

}