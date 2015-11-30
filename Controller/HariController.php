<?php

namespace Ais\HariBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Util\Codes;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Request\ParamFetcherInterface;

use Symfony\Component\Form\FormTypeInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Ais\HariBundle\Exception\InvalidFormException;
use Ais\HariBundle\Form\HariType;
use Ais\HariBundle\Model\HariInterface;


class HariController extends FOSRestController
{
    /**
     * List all haris.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\QueryParam(name="offset", requirements="\d+", nullable=true, description="Offset from which to start listing haris.")
     * @Annotations\QueryParam(name="limit", requirements="\d+", default="5", description="How many haris to return.")
     *
     * @Annotations\View(
     *  templateVar="haris"
     * )
     *
     * @param Request               $request      the request object
     * @param ParamFetcherInterface $paramFetcher param fetcher service
     *
     * @return array
     */
    public function getHarisAction(Request $request, ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $offset = null == $offset ? 0 : $offset;
        $limit = $paramFetcher->get('limit');

        return $this->container->get('ais_hari.hari.handler')->all($limit, $offset);
    }

    /**
     * Get single Hari.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a Hari for a given id",
     *   output = "Ais\HariBundle\Entity\Hari",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the hari is not found"
     *   }
     * )
     *
     * @Annotations\View(templateVar="hari")
     *
     * @param int     $id      the hari id
     *
     * @return array
     *
     * @throws NotFoundHttpException when hari not exist
     */
    public function getHariAction($id)
    {
        $hari = $this->getOr404($id);

        return $hari;
    }

    /**
     * Presents the form to use to create a new hari.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\View(
     *  templateVar = "form"
     * )
     *
     * @return FormTypeInterface
     */
    public function newHariAction()
    {
        return $this->createForm(new HariType());
    }
    
    /**
     * Presents the form to use to edit hari.
     *
     * @ApiDoc(
     *   resource = true,
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "AisHariBundle:Hari:editHari.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the hari id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when hari not exist
     */
    public function editHariAction($id)
    {
		$hari = $this->getHariAction($id);
		
        return array('form' => $this->createForm(new HariType(), $hari), 'hari' => $hari);
    }

    /**
     * Create a Hari from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Creates a new hari from the submitted data.",
     *   input = "Ais\HariBundle\Form\HariType",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "AisHariBundle:Hari:newHari.html.twig",
     *  statusCode = Codes::HTTP_BAD_REQUEST,
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|View
     */
    public function postHariAction(Request $request)
    {
        try {
            $newHari = $this->container->get('ais_hari.hari.handler')->post(
                $request->request->all()
            );

            $routeOptions = array(
                'id' => $newHari->getId(),
                '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('api_1_get_hari', $routeOptions, Codes::HTTP_CREATED);

        } catch (InvalidFormException $exception) {

            return $exception->getForm();
        }
    }

    /**
     * Update existing hari from the submitted data or create a new hari at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "Ais\HariBundle\Form\HariType",
     *   statusCodes = {
     *     201 = "Returned when the Hari is created",
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "AisHariBundle:Hari:editHari.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the hari id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when hari not exist
     */
    public function putHariAction(Request $request, $id)
    {
        try {
            if (!($hari = $this->container->get('ais_hari.hari.handler')->get($id))) {
                $statusCode = Codes::HTTP_CREATED;
                $hari = $this->container->get('ais_hari.hari.handler')->post(
                    $request->request->all()
                );
            } else {
                $statusCode = Codes::HTTP_NO_CONTENT;
                $hari = $this->container->get('ais_hari.hari.handler')->put(
                    $hari,
                    $request->request->all()
                );
            }

            $routeOptions = array(
                'id' => $hari->getId(),
                '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('api_1_get_hari', $routeOptions, $statusCode);

        } catch (InvalidFormException $exception) {

            return $exception->getForm();
        }
    }

    /**
     * Update existing hari from the submitted data or create a new hari at a specific location.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = "Ais\HariBundle\Form\HariType",
     *   statusCodes = {
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @Annotations\View(
     *  template = "AisHariBundle:Hari:editHari.html.twig",
     *  templateVar = "form"
     * )
     *
     * @param Request $request the request object
     * @param int     $id      the hari id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when hari not exist
     */
    public function patchHariAction(Request $request, $id)
    {
        try {
            $hari = $this->container->get('ais_hari.hari.handler')->patch(
                $this->getOr404($id),
                $request->request->all()
            );

            $routeOptions = array(
                'id' => $hari->getId(),
                '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('api_1_get_hari', $routeOptions, Codes::HTTP_NO_CONTENT);

        } catch (InvalidFormException $exception) {

            return $exception->getForm();
        }
    }

    /**
     * Fetch a Hari or throw an 404 Exception.
     *
     * @param mixed $id
     *
     * @return HariInterface
     *
     * @throws NotFoundHttpException
     */
    protected function getOr404($id)
    {
        if (!($hari = $this->container->get('ais_hari.hari.handler')->get($id))) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.',$id));
        }

        return $hari;
    }
    
    public function postUpdateHariAction(Request $request, $id)
    {
		try {
            $hari = $this->container->get('ais_hari.hari.handler')->patch(
                $this->getOr404($id),
                $request->request->all()
            );

            $routeOptions = array(
                'id' => $hari->getId(),
                '_format' => $request->get('_format')
            );

            return $this->routeRedirectView('api_1_get_hari', $routeOptions, Codes::HTTP_NO_CONTENT);

        } catch (InvalidFormException $exception) {

            return $exception->getForm();
        }
	}
}
