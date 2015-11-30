<?php

namespace Ais\HariBundle\Model;

Interface HariInterface
{
    /**
     * Get id
     *
     * @return integer
     */
    public function getId();

    /**
     * Set nama
     *
     * @param string $nama
     *
     * @return Hari
     */
    public function setNama($nama);

    /**
     * Get nama
     *
     * @return string
     */
    public function getNama();

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return Hari
     */
    public function setIsActive($isActive);

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive();

    /**
     * Set isDelete
     *
     * @param boolean $isDelete
     *
     * @return Hari
     */
    public function setIsDelete($isDelete);

    /**
     * Get isDelete
     *
     * @return boolean
     */
    public function getIsDelete();
}
