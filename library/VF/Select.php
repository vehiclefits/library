<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Select extends Zend_Db_Select
{
    /** @var VF_Schema */
    protected $schema;
    const DEFINITIONS = 'definitions';
    const MAPPINGS = 'mappings';

    public function __construct(Zend_Db_Adapter_Abstract $adapter, VF_Schema $schema)
    {
        parent::__construct($adapter);
        $this->schema = $schema;
    }

    function joinAndSelectLevels($fromTable = null, $levels = array(), $schema = null)
    {
        switch ($fromTable) {
            case self::DEFINITIONS:
                $fromTable = $this->getSchema()->definitionTable();
                break;
            case null:
            case self::MAPPINGS;
                $fromTable = $this->getSchema()->mappingsTable();
                break;
            default:
                // assume they passed in a literal string
                break;
        }
        if (array() == $levels) {
            $levels = $this->getSchema()->getLevels();
        }
        foreach ($levels as $level) {
            $level = str_replace(' ', '_', $level);
            $table = 'elite_level_' . $this->getSchema()->id() . '_' . $level;
            $condition = "{$table}.id = {$fromTable}.{$level}_id";
            $this->joinLeft($table, $condition, array($level => 'title', $level . '_id' => 'id'));
        }
        return $this;
    }

    function whereLevelIdsEqual($levelIds)
    {
        foreach ($levelIds as $level => $id) {
            if ($id == false) {
                continue;
            }
            $this->where($this->inflect($level) . '_id = ?', $id);
        }
        return $this;
    }

    function getSchema()
    {
        return $this->schema;
    }

    function inflect($identifier)
    {
        return str_replace(' ', '_', $identifier);
    }
}