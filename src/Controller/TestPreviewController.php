<?php

namespace App\Controller;

use Pimcore\Controller\FrontendController;
use Pimcore\Model\DataObject\Todolist;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use iio\libmergepdf\Merger;
use iio\libmergepdf\Pages;
class TestPreviewController extends FrontendController
{   /**
    * @Route("admin/object/test/{id}", name="blog_list")
    */
    public function print(Todolist $id)
    {

        $html = $this->renderView('web2print/print.html.twig',['test'=>'toto']);
        //return new Response($html,200);
        $processor = \Pimcore\Web2Print\Processor::getInstance();
        $pdf = $processor->getPdfFromString($html,
            [
                'landscape' => false,
                'printBackground' => true,
                'displayHeaderFooter' => true,
                'headerTemplate' => '
                    <style>
                        div{
                        color: #F0F;
                        font-size:15px !important
                    }
                    </style>
                    <div class="pageNumber"></div>/<div class="totalPages "></div>
                    <div>I am a header </div>
                ' ,
                'footerTemplate' => '<div style="font-size:10px !important;">I am a footer</div>',
                'margin' => ['top' => '20mm', 'bottom' => '40px']

            ]);

            $pdf1 = $processor->getPdfFromString($html,[],true);
            dd($pdf1);
            $file = 'people.pdf';
            $merger = new Merger;
            $merger->addFile('./people.pdf');
            $merger->addFile('./test.pdf');
            $createdPdf = $merger->merge();
        return new Response (
            $createdPdf,
            200,
            [
                'Content-Type' => 'application/pdf'
            ]
        );
    }

    /**
    * @Route("admin/header", name="header")
    */
    public function header()
    {
        return new Response('oklkkkkkkkkkkkkk',200);
    }
}

