<?php
/**
 * Vehicle Fits (http://www.vehiclefits.com for more information.)
 * @copyright  Copyright (c) Vehicle Fits, llc
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class VF_Product extends VF_AbstractFinder
{
    /** @var array Collection of VF_Vehicle */
    protected $fits = NULL;
    /** @var VF_Vehicle the customer has associated */
    protected $fit;
    /** @var VF_Schema */
    protected $schema;
    /** @var Zend_Config */
    protected $config;
    /** @var Zend_Db_Adapter_Abstract */
    protected $readAdapter;
    /** @var VF_Vehicle_Finder */
    protected $vehicleFinder;

    protected $id;

    function setId($id)
    {
        $this->id = $id;
    }

    function getId()
    {
        return $this->id;
    }

    function getFitModels()
    {
        $fits = $this->getFits();
        $return = array();
        foreach ($fits as $fitRow) {
            $fit = $this->createFitFromRow($fitRow);
            array_push($return, $fit);
        }
        return $return;
    }

    /** Get a result set for the fits for this product */
    function getFits()
    {
        if (!is_null($this->fits)) {
            return $this->fits;
        }
        if ($productId = (int)$this->getId()) {
            $this->fits = $this->doGetFits($productId);
            return $this->fits;
        }
        return array();
    }

    function customPrice(VF_Vehicle $vehicle)
    {
        $select = $this->getReadAdapter()->select();
        $select->from(array('m' => $this->getSchema()->mappingsTable()), array('price'));
        foreach ($vehicle->toValueArray() as $parentType => $parentId) {
            if (!in_array($parentType, $this->getSchema()->getLevels())) {
                throw new VF_Level_Exception($parentType);
            }
            if (!(int)$parentId) {
                continue;
            }
            $select->where(sprintf('m.`%s_id` = ?', $parentType), $parentId);
        }
        $select->where('`entity_id` = ?', $this->getId());
        $price = $this->query($select)->fetchColumn();
        return (!$price) ? null : $price;
    }

    function getOrderBy()
    {
        $levels = $this->schema->getLevels();
        $c = count($levels);
        $sql = '';
        for ($i = 0; $i <= $c - 1; $i++) {
            $sql .= '`' . $levels[$i] . '`' . ($i < $c - 1 ? ',' : '');
        }
        return $sql;
    }

    /**
     * Add one or more fitment(s) described by an array of level IDs
     *
     * Examples -  add make 5 and all its children:
     * array( 'make' => 5 )
     *
     *  ...   is the same as:
     * array( 'make' => 5, 'model' => 0 )
     *
     * ... or add a individual fit:
     * array( 'make' => 5, 'model' => 3, 'year' => 4 )
     *
     * ... is the same as
     * array( 'year' => 4 )
     *
     * @param array fitToAdd - fitment to add represented as an array keyed by level name [string]
     * @return integer ID of fitment row created
     */
    function addVafFit(array $fitToAdd)
    {
        $vehicles = $this->vehicleFinder()->findByLevelIds($fitToAdd);
        $mapping_id = null;
        foreach ($vehicles as $vehicle) {
            $mapping_id = $this->insertMapping($vehicle);
        }
        return $mapping_id;
    }

    function vehicleFinder()
    {
        return $this->vehicleFinder;
    }

    function insertMapping(VF_Vehicle $vehicle)
    {
        $mapping = new VF_Mapping($this->getId(), $vehicle, $this->getSchema(), $this->getReadAdapter(
        ), $this->getConfig());
        return $mapping->save();
    }

    function deleteVafFit($mapping_id)
    {
        $sql = sprintf("DELETE FROM `" . $this->getSchema()->mappingsTable() . "` WHERE `id` = %d", (int)$mapping_id);
        $this->query($sql);
        if (file_exists(ELITE_PATH . '/Vafnote')) {
            $sql = sprintf("DELETE FROM `elite_mapping_notes` WHERE `fit_id` = %d", (int)$mapping_id);
            $this->query($sql);
        }
    }

    /** @return boolean */
    function isUniversal()
    {
        $sql = sprintf(
            "
            SELECT
                count( * )
            FROM
                `" . $this->getSchema()->mappingsTable() . "`
		    WHERE
		        `entity_id` = %d
		    AND
		        `universal` = 1
		    ",
            (int)$this->getId()
        );
        $result = $this->query($sql);
        $count = $result->fetchColumn();
        return $count == 0 ? false : true;
    }

    /** @param boolean */
    function setUniversal($universal)
    {
        if (!$universal) {
            $query = sprintf("DELETE FROM " . $this->getSchema()->mappingsTable() . " WHERE universal = 1 AND entity_id = %d", $this->getId());
            $r = $this->query($query);
            return;
        }
        $sql = sprintf("REPLACE INTO `" . $this->getSchema()->mappingsTable() . "`
                        (`universal`,`entity_id`)
                        VALUES
                        (%d,%d)",
            1,
            (int)$this->getId());
        $this->query($sql);
    }

    function getName($name)
    {
        $this->setFitFromGlobalIfNoLocalFitment();
        if (!$this->rewritesOn() || !$this->fitsSelection()) {
            return $name;
        }
        $template = $this->getConfig()->seo->productNameTemplate;
        if (empty($template)) {
            $template = '_product_ for _vehicle_';
        }
        $find = array('_product_', '_vehicle_');
        $vehicle = $this->getFirstCurrentlySelectedFitment();

        $replace = array($name, (string)$vehicle);
        return str_replace($find, $replace, $template);
    }

    function setFitFromGlobalIfNoLocalFitment()
    {
        if (!$this->fit) {
            $this->fit = VF_Singleton::getInstance()->vehicleSelection();
        }
    }

    function rewritesOn()
    {
        return $this->getConfig()->seo->rewriteProductName;
    }

    function globalRewritesOn()
    {
        return $this->getConfig()->seo->globalRewrites;
    }

    function setCurrentlySelectedFit($fit)
    {
        if(!is_array($fit)) {
            $this->fit = array($fit);
            return;
        }
        $this->fit = $fit;
    }

    function currentlySelectedFit()
    {
        $this->setFitFromGlobalIfNoLocalFitment();
        if ($this->fit) {
            return $this->fit;
        } else {
            return array();
        }
    }

    /**
     * Will return VF_Vehicle if there is a fitment to be selected
     * or false if there are no fitments selected.
     *
     * @author Kyle Cannon <kyle.d.cannon@gmail.com>
     * @return bool|VF_Vehicle
     */
    public function getFirstCurrentlySelectedFitment() {
        if($this->hasFitmentBeenSelected()) {
            $fitments = $this->currentlySelectedFit();
            return $fitments[0];
        }
        return false;
    }
    /**
     * Will return true if there is one or more fitment(s) currently selected
     * or false if there are no fitment(s) selected.
     *
     * @author Kyle Cannon <kyle.d.cannon@gmail.com>
     * @return bool
     */
    public function hasFitmentBeenSelected() {
        return count($this->currentlySelectedFit()) > 0;
    }

    function fitsSelection()
    {
        if (!$this->hasFitmentBeenSelected()) {
            return false;
        }
        $vehicle = $this->getFirstCurrentlySelectedFitment();
        return $this->fitsVehicle($vehicle);
    }

    function fitsVehicle(VF_Vehicle $vehicle)
    {
        $select = $this->getReadAdapter()->select()
            ->from($this->getSchema()->mappingsTable(), array('count(*)'))
            ->where('entity_id = ?', $this->getId());
        $params = $vehicle->toValueArray();
        foreach ($params as $param => $value) {
            $select->where($param .= '_id = ?', $value);
        }
        $count = $select->query()->fetchColumn();
        return 0 != $count;
    }

    /** @todo move to vfmagento */
    function isInEnabledCategory(Elite_Vaf_Model_Catalog_Category_Filter $filter, $categoryIds)
    {
        foreach ($categoryIds as $categoryId) {
            if ($filter->shouldShow($categoryId)) {
                return true;
            }
        }
        return false;
    }

    function getMappingId(VF_Vehicle $vehicle)
    {
        $select = $this->getReadAdapter()->select()
            ->from($this->getSchema()->mappingsTable(), 'id')
            ->where($this->schema->getLeafLevel() . '_id = ?', $vehicle->getLeafValue())
            ->where('entity_id = ?', $this->getId());
        return $select->query()->fetchColumn();
    }

    /**
     * Create duplicate
     *
     * @return Mage_Catalog_Model_Product
     */
    function duplicate()
    {   
        $leaf = $this->schema->getLeafLevel() . '_id';
        $newProduct = parent::duplicate();
        foreach ($this->getFits() as $fit) {
            print_r($fit);
            exit;
            $vehicle = $this->vehicleFinder->findByLeaf($fit->$leaf);
            $newProduct->insertMapping($vehicle);
        }
        if ($this->isUniversal()) {
            $newProduct->setUniversal(true);
        }
        return $newProduct;
    }

    /**
     * @param Elite_Vaf_Model_Abstract - if is an "aggregrate" of fits ( iterate and add it's children )
     */
    function doAddFit($entity)
    {
        $params = array($entity->getType() => $entity->getTitle());
        $vehicles = $this->vehicleFinder->findByLevels($params);
        return $vehicles;
    }

    function createFitFromRow($row)
    {
        return new VF_Vehicle($this->getSchema(), $this->getReadAdapter(), $this->getConfig(), $this->getLevelFinder(
        ), $this->getVehicleFinder(), $row->id, $row);
    }

    function doGetFits($productId)
    {
        $select = new VF_Select($this->getReadAdapter(), $this->getSchema());
        $select->from($this->getSchema()->mappingsTable())
            ->joinAndSelectLevels()
            ->where('entity_id=?', $productId);
        $result = $this->query($select);
        $fits = array();
        while ($row = $result->fetchObject()) {
            if ($row->universal) {
                continue;
            }
            $fits[] = $row;
        }
        return $fits;
    }
}