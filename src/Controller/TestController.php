<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class TestController extends AbstractController
{
    public function __construct(HttpClientInterface $client){
        $this->client = $client;
    }
    /**
     * @Route("/test", name="test")
     */
    public function index(): Response
    {

        return $this->render('test/index.html.twig', [
            'controller_name' => 'TestController',
        ]);
    }

    /**
     * @Route("/pdf/convert", name="pdfconvert")
     */
    public function pdfConvert()
    {
        $apikey = $this->getParameter('APIKEY');
        $pdfReactor = new PDFreactor("http://localhost:9423/service/rest");
        $pdfReactor->apiKey= $apikey;

        $config = array(
            // Specify the input document
            "document"=> 'https://fr.wikipedia.org/wiki/France',
            // Set a base URL for images, style sheets, links
            // Set an appropriate log level
            "logLevel"=> LogLevel::DEBUG,
            // Set the title of the created PDF
            "title"=> "Demonstration of PDFreactor PHP API",
            // Sets the author of the created PDF
            "author"=> "Myself",
            // Set some viewer preferences
            "viewerPreferences"=> array(
                ViewerPreferences::FIT_WINDOW,
                ViewerPreferences::PAGE_MODE_USE_THUMBS
            ),
            // Add user style sheets
            "userStyleSheets"=> array(
                array(
                    "content" => "* { -ro-image-resampling: 50dpi; -ro-image-recompression: jpeg(10%) }"
                ),
            )
        );
        $config["colorSpaceSettings"] = array(
            // When converting to RGB the profile is used for accurate conversion from CMYK
            "cmykIccProfile" => array("uri" => "URL/to/ICC/profile"),
            // Not necessary to set in this case (default), but recommended
            "targetColorSpace" => ColorSpace::RGB,
            // Enable conversion of CMYK colors and images to RGB
            "conversionEnabled" => true
        );

        $documentId = $pdfReactor->convertAsync($config, $connectionSettings);


        $data['id']=  $documentId;
        return $this->json($data, $status = 200);
    }

        /**
     * @Route("/pdf/status/{id}", name="pdfStatus")
     */
    public function pdfStatus(string $id)
    {

        $apikey = $this->getParameter('APIKEY');
        $pdfReactor = new PDFreactor("http://localhost:9423/service/rest");
        $pdfReactor->apiKey= $apikey;


        $progress = $pdfReactor->getProgress($id);


        return $this->json($progress, $status = 200);
    }

    /**
     * @Route("/pdf/getDocument/{id}", name="pdfGet")
     */
    public function pdfGet(string $id)
    {

        $apikey = $this->getParameter('APIKEY');
        $pdfReactor = new PDFreactor("http://localhost:9423/service/rest");
        $pdfReactor->apiKey= $apikey;


        $document = $pdfReactor->getDocumentAsBinary($id);


        return new Response($document, 200, ["Content-Type"=> "application/pdf"]);
    }
}
