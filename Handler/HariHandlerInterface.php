<?php

namespace Ais\HariBundle\Handler;

use Ais\HariBundle\Model\HariInterface;

interface HariHandlerInterface
{
    /**
     * Get a Hari given the identifier
     *
     * @api
     *
     * @param mixed $id
     *
     * @return HariInterface
     */
    public function get($id);

    /**
     * Get a list of Haris.
     *
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 5, $offset = 0);

    /**
     * Post Hari, creates a new Hari.
     *
     * @api
     *
     * @param array $parameters
     *
     * @return HariInterface
     */
    public function post(array $parameters);

    /**
     * Edit a Hari.
     *
     * @api
     *
     * @param HariInterface   $hari
     * @param array           $parameters
     *
     * @return HariInterface
     */
    public function put(HariInterface $hari, array $parameters);

    /**
     * Partially update a Hari.
     *
     * @api
     *
     * @param HariInterface   $hari
     * @param array           $parameters
     *
     * @return HariInterface
     */
    public function patch(HariInterface $hari, array $parameters);
}
