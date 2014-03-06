<?php


class VF_Config extends Zend_Config
{

    public function __construct(Zend_Config $config)
    {
        parent::__construct($config->toArray(), true);
    }

    /** Get the option text prompting the user to make a selection */
    public function getDefaultSearchOptionText($level = null, $config = null)
    {
        $text = trim($this->search->defaultText);
        if (empty($text)) {
            $text = '-please select-';
        }
        $text = sprintf($text, ucfirst($level));
        return $text;
    }

    /** @return boolean wether or not to prefix select boxes with a label */
    public function showLabels()
    {
        if (isset($this->search->labels)) {
            return (bool)$this->search->labels;
        }
        return true;
    }

    public function displayBrTag()
    {
        if (is_null($this->search->insertBrTag)) {
            return true;
        }
        return (bool)$this->search->insertBrTag;
    }

    public function enableDirectory()
    {
        if (!is_null($this->directory->enable) && $this->directory->enable) {
            return true;
        }
        return false;
    }

    /** Get the option loading text for the ajax */
    public function getLoadingText()
    {
        return isset($this->search->loadingText) ? $this->search->loadingText
            : 'loading';
    }

    /** @todo Fix exception error  */
    function getBaseUrl($https = null)
    {
        if (isset($this->base_url)) {
            return $this->base_url;
        }
        throw new Exception('base URL has not been injected into the singleton');
    }

    function setBaseURL($url)
    {
        $this->base_url = $url;
    }

    /** @todo Fix exception error  */
    function processUrl()
    {
        if (isset($this->process_url)) {
            return $this->process_url;
        }
        throw new Exception('process URL has not been injected into the singleton');
    }

    function setProcessURL($url)
    {
        $this->process_url = $url;
    }

    function homepageSearchURL()
    {
        if (isset($this->homepagesearch_url)) {
            return $this->homepagesearch_url;
        }
        throw new Exception;
    }

    function setHomepagesearchURL($url)
    {
        $this->homepagesearch_url = $url;
    }

} 