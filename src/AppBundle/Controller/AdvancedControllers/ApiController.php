<?php
namespace AppBundle\Controller\AdvancedControllers;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiController extends Controller
{

    /**
     * Render data
     *
     * @param array $data
     *
     * @return Response
     */
    public function renderData($data = [])
    {
        $headers = ['Content-Type' => 'application/json'];
        $body = [
            'success' => true,
            'data' => $data
        ];
        return new Response(json_encode($body), 200, $headers);
    }

    /**
     * Render error
     *
     * @param integer $code
     * @param string $message
     *
     * @return Response
     */
    public function renderError($code, $message)
    {
        $headers = ['Content-Type' => 'application/json'];
        $body = [
            'success' => false,
            'error' => [
                'code' => $code,
                'message' => $message,
            ]
        ];
        return new Response(json_encode($body), 200, $headers);
    }
    
    
}