<?php

namespace Wyd2016Bundle\Model\Virtual;

use Doctrine\ORM\Tools\Pagination\Paginator as DoctrinePaginator;

/**
 * Model virtual
 */
class Paginator extends DoctrinePaginator
{
    /** @var integer */
    private $pageNo;

    /** @var integer */
    private $packSize;

    /** @var integer */
    private $routeName;

    /** @var integer */
    private $pageAttr;

    /** @var integer */
    private $routeParams;

    /**
     * Constructor
     *
     * @param Query|QueryBuilder $query               query
     * @param integer            $pageNo              page no
     * @param integer            $packSize            pack size
     * @param boolean            $fetchJoinCollection fetch join collection
     */
    public function __construct($query, $pageNo, $packSize, $fetchJoinCollection = true)
    {
        $this->pagesAround = 3;
        $this->pageNo = (integer) $pageNo;
        $this->packSize = (integer) $packSize;
        $this->routeName = '';
        $this->pageAttr = 'pageNo';
        $this->routeParams = array();

        parent::__construct($query, $fetchJoinCollection);
    }

    /**
     * Set pages around
     *
     * @param integer $pagesAround pages around
     *
     * @return self
     */
    public function setPagesAround($pagesAround)
    {
        $this->pagesAround = (integer) $pagesAround;

        return $this;
    }

    /**
     * Get pages around
     *
     * @return integer
     */
    public function getPagesAround()
    {
        return $this->pagesAround;
    }

    /**
     * Get page no
     *
     * @return integer
     */
    public function getPageNo()
    {
        return $this->pageNo;
    }

    /**
     * Get pages number
     *
     * @return integer
     */
    public function getPagesNumber()
    {
        $pagesNumber = ceil($this->count() / $this->getPackSize());

        return $pagesNumber;
    }

    /**
     * Get pack size
     *
     * @return integer
     */
    public function getPackSize()
    {
        return $this->packSize;
    }

    /**
     * Get first
     *
     * @return integer|null
     */
    public function getFirst()
    {
        $pageNo = $this->getPageNo() - $this->getPagesAround() > 1 ? 1 : null;

        return $pageNo;
    }

    /**
     * Get prev
     *
     * @return integer|null
     */
    public function getPrev()
    {
        $pageNo = $this->getPageNo() > 1 ? $this->getPageNo() - 1 : null;

        return $pageNo;
    }

    /**
     * Get next
     *
     * @return integer|null
     */
    public function getNext()
    {
        $pageNo = $this->getPageNo() < $this->getPagesNumber() ? $this->getPageNo() + 1 : null;

        return $pageNo;
    }

    /**
     * Get last
     *
     * @return integer|null
     */
    public function getLast()
    {
        $pageNo =
            $this->getPageNo() + $this->getPagesAround() < $this->getPagesNumber() ? $this->getPagesNumber() : null;

        return $pageNo;
    }

    /**
     * Is space after first
     *
     * @return boolean
     */
    public function isSpaceAfterFirst()
    {
        $isSpace = $this->getPageNo() - $this->getPagesAround() - 1 > 1;

        return $isSpace;
    }

    /**
     * Is space before last
     *
     * @return boolean
     */
    public function isSpaceBeforeLast()
    {
        $isSpace = $this->getPageNo() + $this->getPagesAround() + 1 < $this->getPagesNumber();

        return $isSpace;
    }

    /**
     * Get pages list
     *
     * @return array
     */
    public function getPagesList()
    {
        $startNo = max($this->getPageNo() - $this->getPagesAround(), 1);
        $finishNo = min($this->getPageNo() + $this->getPagesAround(), $this->getPagesNumber());
        $pagesList = range($startNo, $finishNo);

        return $pagesList;
    }

    /**
     * Set route name
     *
     * @param string $routeName route name
     *
     * @return self
     */
    public function setRouteName($routeName)
    {
        $this->routeName = $routeName;

        return $this;
    }

    /**
     * Get route name
     *
     * @return string
     */
    public function getRouteName()
    {
        return $this->routeName;
    }

    /**
     * Set page attribute name
     *
     * @param string $pageAttr page attribute name
     *
     * @return self
     */
    public function setPageAttr($pageAttr)
    {
        $this->pageAttr = $pageAttr;

        return $this;
    }

    /**
     * Get page attribute name
     *
     * @return string
     */
    public function getPageAttr()
    {
        return $this->pageAttr;
    }

    /**
     * Set route params
     *
     * @param array $routeParams route params
     *
     * @return self
     */
    public function setRouteParams(array $routeParams)
    {
        $this->routeParams = $routeParams;

        return $this;
    }

    /**
     * Get route params
     *
     * @param integer $pageNo page no
     *
     * @return array
     */
    public function getRouteParams($pageNo)
    {
        $routeParams = array_merge($this->routeParams, array(
            $this->getPageAttr() => $pageNo,
        ));

        return $routeParams;
    }
}
