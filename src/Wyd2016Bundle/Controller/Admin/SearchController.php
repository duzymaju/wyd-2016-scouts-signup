<?php

namespace Wyd2016Bundle\Controller\Admin;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Translation\TranslatorInterface;
use Wyd2016Bundle\Entity\Repository\SearchRepositoryInterface;
use Wyd2016Bundle\Form\RegistrationLists;
use Wyd2016Bundle\Form\Type\SearchType;

/**
 * Admin controller
 */
class SearchController extends AbstractController
{
    /**
     * Index action
     *
     * @param Request $request request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        /** @var TranslatorInterface $translator */
        $translator = $this->get('translator');
        /** @var RegistrationLists $registrationLists */
        $registrationLists = $this->get('wyd2016bundle.registration.lists');
        $formType = new SearchType($translator, $registrationLists);

        $form = $this->createForm($formType, null, array(
            'action' => $this->generateUrl('admin_search_index'),
            'method' => 'POST',
        ));
        $form->handleRequest($request);

        $type = SearchType::CHOICE_ALL;
        if ($form->isValid()) {
            $type = $form->get('type')
                ->getData();
            $query = $form->get('query')
                ->getData();

            $queries = array();
            foreach (explode(' ', $query) as $element) {
                if  (!empty($element)) {
                    $queries[] = $element;
                }
            }

            if ($type == SearchType::CHOICE_ALL) {
                $results = array(
                    SearchType::CHOICE_VOLUNTEER => $this->getRepository(SearchType::CHOICE_VOLUNTEER)
                        ->searchBy($queries),
                    SearchType::CHOICE_PILGRIM => $this->getRepository(SearchType::CHOICE_PILGRIM)
                        ->searchBy($queries),
                    SearchType::CHOICE_TROOP => $this->getRepository(SearchType::CHOICE_TROOP)
                        ->searchBy($queries),
                    SearchType::CHOICE_GROUP => $this->getRepository(SearchType::CHOICE_GROUP)
                        ->searchBy($queries),
                );
            } else {
                $results = array(
                    $type => $this->getRepository($type)
                        ->searchBy($queries),
                );
            }
        } else {
            $query = null;
            $results = null;
        }

        return $this->render('Wyd2016Bundle::admin/search.html.twig', array(
            'form' => $form->createView(),
            'query' => $query,
            'results' => $results,
        ));
    }

    /**
     * Get repository
     *
     * @param string $type type
     * 
     * @return SearchRepositoryInterface
     *
     * @throw Exception
     */
    protected function getRepository($type)
    {
        switch ($type) {
            case SearchType::CHOICE_GROUP:
                $serviceName = 'wyd2016bundle.group.repository';
                break;

            case SearchType::CHOICE_PILGRIM:
                $serviceName = 'wyd2016bundle.pilgrim.repository';
                break;

            case SearchType::CHOICE_TROOP:
                $serviceName = 'wyd2016bundle.troop.repository';
                break;

            case SearchType::CHOICE_VOLUNTEER:
                $serviceName = 'wyd2016bundle.volunteer.repository';
                break;

            default:
                throw new Exception('There is no proper repository service name defined.');
        }
        $repository = $this->get($serviceName);

        return $repository;
    }
}
