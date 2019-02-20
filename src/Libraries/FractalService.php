<?php
namespace App\Semlohe\Libraries;

use League\Fractal;

class FractalService
{
    /** @var Fractal\Manager $manager */
    private $manager;

    public function __construct(Fractal\Manager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Fractal collection generator
     *
     * @param array $data
     * @param Fractal\TransformerAbstract $transformer
     * return array
     */
    public function collection($data, Fractal\TransformerAbstract $transformer)
    {
        $resource = new Fractal\Resource\Collection($data, $transformer);
        return $this->manager->createData($resource)->toArray();
    }

    /**
     * Fractal item generator
     *
     * @param array $data
     * @param Fractal\TransformerAbstract $transformer
     * return array
     */
    public function item($data, Fractal\TransformerAbstract$transformer)
    {
        $resource = new Fractal\Resource\Item($data, $transformer);
        return $this->manager->createData($resource)->toArray();
    }
}
