<?php
namespace App\Semlohe\Repositories;

use JasonGrimes\Paginator;
use App\Semlohe\Exceptions;

abstract class AbstractRepository
{
    /**
     * Transform data item 
     * 
     * @param $resource
     * @return mixed|array
     */
    protected function transformItem($source, $code = 200, $message = 'Ok')
    {
        $this->transformer->setType('item');
        $response = $this->fractal->item(
            $source,
            $this->transformer
        );

        return $this->responseMeta($response, $code, $message);
    }

    /**
     * Generate additional meta
     *
     * @param  $source
     * @return array
     */
    protected function transformCollection($source, $page, $filters = [], $sorting = [])
    {   
        $meta = [];

        if ($page !== 'all') {
            $total = $source->total();
            $perPage = $this->datasource->getPerPage();
            
            try {
                $pattern = $this->url->generate($this->urlBind, $filters);
                $pattern .= ($filters === [] ? '?' : '&') . 'page=(:num)';
                
                $paginator = $this->generatePaginator(
                    $total,
                    $this->datasource->getPerPage(),
                    $page,
                    $pattern        
                );
            } catch (\Exception $e) {
                throw new Exceptions\InternalServerErrorException();
            }

            $meta = [
                'meta' => [
                    'pagination' => [
                        'current_page' => $page,
                        'total_data' => $total,
                        'item_per_page' => $perPage,
                        'last_page' => ceil($total / $perPage),
                        'links' => array_merge(
                            ['prev' => $paginator->getPrevUrl(),],
                            ['pages' => $paginator->getPages()],
                            ['next' => $paginator->getNextUrl(),]
                        ),
                    ],
                    'sorting' => $sorting
                ]
            ];
            $source = $source->items();
        }


        $result = $this->fractal->collection(
            $source,
            $this->transformer
        );


        return $this->responseMeta(array_merge($result, $meta));
    }
   
    
    /**
     * Add meta info to response
     *
     * @param array $response
     * @param int $status
     * @param string $message
     * @return array
     */
    protected function responseMeta(array $response = [], $status = 200, $message = 'Ok')
    {
        $meta = array_merge([
            'code'    => $status,
            'message' => $message
        ], array_get($response, 'meta', []));

        $response['meta'] = $meta;

        return $response;
    }

    /**
     * Trim each input if input is string
     * @return string
     */
    protected function trimAll($data)
    {
        $result = [];
        foreach ($data as $key => $value) {
            if (is_string($value)) {
                $result[$key] = trim($value);
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Generate pagination
     * 
     * @param int $total
     * @param int $perPage
     * @param int $currentPage
     * @param string $urlPattern
     * @return Paginator
     */
    protected function generatePaginator($total, $perPage, $currentPage, $urlPattern)
    {
        return new Paginator(
            $total, 
            $perPage, 
            $currentPage, 
            $urlPattern 
        );
    }
}
