<?php

namespace Ais\HariBundle\Handler;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Form\FormFactoryInterface;
use Ais\HariBundle\Model\HariInterface;
use Ais\HariBundle\Form\HariType;
use Ais\HariBundle\Exception\InvalidFormException;

class HariHandler implements HariHandlerInterface
{
    private $om;
    private $entityClass;
    private $repository;
    private $formFactory;

    public function __construct(ObjectManager $om, $entityClass, FormFactoryInterface $formFactory)
    {
        $this->om = $om;
        $this->entityClass = $entityClass;
        $this->repository = $this->om->getRepository($this->entityClass);
        $this->formFactory = $formFactory;
    }

    /**
     * Get a Hari.
     *
     * @param mixed $id
     *
     * @return HariInterface
     */
    public function get($id)
    {
        return $this->repository->find($id);
    }

    /**
     * Get a list of Haris.
     *
     * @param int $limit  the limit of the result
     * @param int $offset starting from the offset
     *
     * @return array
     */
    public function all($limit = 5, $offset = 0)
    {
        return $this->repository->findBy(array(), null, $limit, $offset);
    }

    /**
     * Create a new Hari.
     *
     * @param array $parameters
     *
     * @return HariInterface
     */
    public function post(array $parameters)
    {
        $hari = $this->createHari();

        return $this->processForm($hari, $parameters, 'POST');
    }

    /**
     * Edit a Hari.
     *
     * @param HariInterface $hari
     * @param array         $parameters
     *
     * @return HariInterface
     */
    public function put(HariInterface $hari, array $parameters)
    {
        return $this->processForm($hari, $parameters, 'PUT');
    }

    /**
     * Partially update a Hari.
     *
     * @param HariInterface $hari
     * @param array         $parameters
     *
     * @return HariInterface
     */
    public function patch(HariInterface $hari, array $parameters)
    {
        return $this->processForm($hari, $parameters, 'PATCH');
    }

    /**
     * Processes the form.
     *
     * @param HariInterface $hari
     * @param array         $parameters
     * @param String        $method
     *
     * @return HariInterface
     *
     * @throws \Ais\HariBundle\Exception\InvalidFormException
     */
    private function processForm(HariInterface $hari, array $parameters, $method = "PUT")
    {
        $form = $this->formFactory->create(new HariType(), $hari, array('method' => $method));
        $form->submit($parameters, 'PATCH' !== $method);
        if ($form->isValid()) {

            $hari = $form->getData();
            $this->om->persist($hari);
            $this->om->flush($hari);

            return $hari;
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }

    private function createHari()
    {
        return new $this->entityClass();
    }

}
