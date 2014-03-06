<?php


class VF_AjaxTests_AjaxTestCase extends VF_TestCase
{
    public function execute()
    {
        ob_start();
        $_GET['front'] = 1;
        $this->getAjax()->execute();
        return ob_get_clean();
    }

    /** @return VF_Ajax */
    public function getAjax()
    {
        return new VF_Ajax($this->getServiceContainer()->getSchemaClass(), $this->getServiceContainer()
            ->getReadAdapterClass(), $this->getServiceContainer()->getConfigClass(), $this->getServiceContainer()
            ->getLevelFinderClass(), $this->getServiceContainer()->getVehicleFinderClass(), $this->getServiceContainer()
            ->getRequestClass());
    }
}