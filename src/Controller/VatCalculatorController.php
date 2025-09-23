<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\VatCalculatorService;
use App\Entity\VatCalculation;
use Doctrine\ORM\EntityManagerInterface;

class VatCalculatorController extends AbstractController
{
    private EntityManagerInterface $em;
    private VatCalculatorService $vatCalculator;

    public function __construct(EntityManagerInterface $em, VatCalculatorService $vatCalculator)
    {
        $this->em = $em;
        $this->vatCalculator = $vatCalculator;
    }

    #[Route('/vat', name: 'app_vat')]
    public function index(Request $request): Response
    {
        $price = $request->query->get('price');
        $rate = $request->query->get('rate');
        $result = null;

        if (is_numeric($price) && is_numeric($rate)) {
            $result = $this->vatCalculator->calculate((float)$price, (float)$rate);

            $calculation = new VatCalculation();
            $calculation->setPrice($result['price']);
            $calculation->setRate($result['rate']);
            $calculation->setVat($result['vat']);
            $calculation->setTotal($result['total']);
            $calculation->setCreatedAt(new \DateTimeImmutable());

            $this->em->persist($calculation);
            $this->em->flush();
        }

        $history = $this->em->getRepository(VatCalculation::class)
                    ->findBy([], ['createdAt' => 'DESC'], 5);

        return $this->render('vat/index.html.twig', [
            'result' => $result,
            'history' => $history,
        ]);
    }

    #[Route('/history', name: 'app_vat_history')]
    public function history(): Response
    {
        // Simply denys access unless user logged in and has this role 
        // There was a flicker of the page before redirected? Swapped to access control in security.yaml
        // $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $history = $this->em->getRepository(VatCalculation::class)
            ->findBy([], ['createdAt' => 'DESC']); // all calculations

        return $this->render('vat/history.html.twig', [
            'history' => $history,
        ]);
    }


}