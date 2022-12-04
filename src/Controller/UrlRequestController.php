<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Doctrine\Persistence\ManagerRegistry;
use GuzzleHttp;

//entity call
use App\Entity\UrlRequest;

//Repository call
use App\Repository\UrlRequestRepository;

class UrlRequestController extends AbstractController
{
    private $client;

    function __construct(HttpClientInterface $client){
        $this->client = $client;
    }

    #[Route('/', name: 'app_request')]
    public function index(UrlRequestRepository $urlRequestRepositoryData): Response
    {
        $urlRequestData = $urlRequestRepositoryData->findAll();

        return $this->render('request/index.html.twig', [
            'request_data' => $urlRequestData
        ]);
    }

    #[Route('/request/form', name:'app_request_form', methods:['GET'])]
    public function form(): Response
    {
        return $this->render('request/form.html.twig');
    }

    #[Route('/request/form/action', name:'app_request_form_action', methods:['POST'])]
    public function formAction(Request $request, ManagerRegistry $doctrine): Response
    {
        try{
            $startTime = new \DateTime("now");
            $url = $request->request->get('url');
            $response = $this->client->request(
                'GET',
                $url
            );

            if ($response->getStatusCode() != 200){
                return new JsonResponse([
                    "error" => "Failed to scrap the URL."
                ]);
            }
            $html = (string) $response->getContent(); 
            $dom = new \DOMDocument();
            libxml_use_internal_errors(true);
            $dom->loadHTML($html);
 
            $tags = $dom->getElementsByTagName('*');
            $elementDistribution = [];
            foreach($tags as $element) {
                if(array_key_exists($element->tagName, $elementDistribution)) {
                    $elementDistribution[$element->tagName] += 1;
                } else {
                    $elementDistribution[$element->tagName] = 1;
                }
            }
            $endTime = new \DateTime("now");

            $urlRequest = new UrlRequest();
            $urlRequest->setUrl($url);
            // $urlRequest->setDomain(parse_url($url)['host']);
            $urlRequest->setTags($elementDistribution);
            $urlRequest->setStartedTime($startTime);
            $urlRequest->setEndTime($endTime);

            $entityManager = $doctrine->getManager();
            $entityManager->persist($urlRequest);
            $entityManager->flush();

            return $this->redirectToRoute('app_request');
        }catch(Exception $e){
            echo "Error: $e";
        }
    }

    #[Route('/request/{id}', name:'app_request_show')]
    public function show(ManagerRegistry $doctrine, int $id): Response
    {
        $requestData = $doctrine->getRepository(UrlRequest::class)->find($id);

        if (!$requestData) {
            throw $this->createNotFoundException(
                'No URL Request found.'
            );
        }
        
        return $this->render('request/show.html.twig', [
            'request_data' => $requestData
        ]);
    }

    #[Route('/request/delete/{id}', name:'app_request_delete')]
    public function delete(UrlRequest $urlRequest, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $entityManager->remove($urlRequest);
        $entityManager->flush();
        
        return $this->redirectToRoute('app_request');
    }
}
