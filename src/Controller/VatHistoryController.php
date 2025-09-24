<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\VatCalculation;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;

class VatHistoryController extends AbstractController
{
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/history', name: 'app_vat_history')]
    public function history(Request $request, PaginatorInterface $paginator): Response 
    {

        $min = $request->query->get('min');
        $max = $request->query->get('max');

        $query = $this->em->getRepository(VatCalculation::class)
            ->createQueryBuilder('h')
            ->orderBy('h.createdAt', 'DESC');

        if ($min !== null && $min !== '') {
        $query->andWhere('h.price >= :min')
           ->setParameter('min', (float)$min);
        }

        if ($max !== null && $max !== '') {
            $query->andWhere('h.price <= :max')
            ->setParameter('max', (float)$max);
        }

        $pagination = $paginator->paginate(
            $query,                             // Query or array
            $request->query->getInt('page', 1), // Current page (default 1)
            5                                  // Items per page
        );

        return $this->render('vat/history.html.twig', [
            'pagination' => $pagination,
            'min' => $min,
            'max' => $max,
        ]);
    }
}