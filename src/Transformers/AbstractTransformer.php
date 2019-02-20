<?php
namespace App\Semlohe\Transformers;

use League\Fractal\TransformerAbstract;

abstract class AbstractTransformer extends TransformerAbstract
{
    /** @var string $type */
    private $type = 'collection';
    
    /**
     * Type attribute setter
     * 
     * @param string $type
     * @return Admin $this
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Type attribute getter
     * 
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

}
