<?php

namespace Wyd2016Bundle\Controller\Admin;

use Doctrine\ORM\Tools\Pagination\Paginator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Wyd2016Bundle\Entity\Repository\PilgrimRepository;
use Wyd2016Bundle\Entity\Pilgrim;

/**
 * Admin controller
 */
class PilgrimController extends Controller
{
    /**
     * Index action
     *
     * @param integer $pageNo page no
     *
     * @return Response
     */
    public function indexAction($pageNo)
    {
        /** @var Paginator $pilgrims */
        $pilgrims = $this->getRepository()
            ->getPackOrException($pageNo, $this->getParameter('wyd2016.admin.pack_size'), array(), array(
                'createdAt' => 'DESC',
            ));

        return $this->render('Wyd2016Bundle::admin/pilgrim/index.html.twig', array(
            'pilgrims' => $pilgrims->setRouteName('admin_pilgrim_index'),
        ));
    }

    /**
     * Show action
     *
     * @param integer $id ID
     *
     * @return Response
     */
    public function showAction($id)
    {
        /** @var Pilgrim $pilgrim */
        $pilgrim = $this->getRepository()
            ->findOneByOrException(array(
                'id' => $id,
            ));

        return $this->render('Wyd2016Bundle::admin/pilgrim/show.html.twig', array(
            'ageLimit' => $this->getParameter('wyd2016.age.limit'),
            'pilgrim' => $pilgrim,
        ));
    }

    /**
     * Get repository
     *
     * @return PilgrimRepository
     */
    protected function getRepository()
    {
        $repository = $this->get('wyd2016bundle.pilgrim.repository');

        return $repository;
    }
}
