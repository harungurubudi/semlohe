<?php
namespace App\Semlohe\Repositories\Admin;

use App\Semlohe\Exceptions;
use Symfony\Component\Translation\Translator;
use \Gumlet\ImageResize;

class Upload extends AbstractCrudRepository
{
    /** @var Translator $translator */
    protected $translator;

    public function __construct(Translator $translator) 
    {
        $this->translator = $translator;
    }

    /**
     * Move input file to local directory
     *
     * @param string $path
     * @param Symfony\Component\HttpFoundation\File\UploadedFile $file
     * @param string $filename
     * @return array
     */
    public function move($path, $file, $cropAndResize = false, $filename = '')
    {
        try {
            $filename = $filename === '' ? $file->getClientOriginalName() : $filename;
            $file->move(
                getUploadPath($path), 
                $filename
            );

            if ($cropAndResize) {
                $this->cropAndResize($path, $filename, '1_1');
                $this->cropAndResize($path, $filename, '16_9');
                $this->cropAndResize($path, $filename);
            }

        } catch (\Exception $e) {
            throw new Exceptions\BadRequestException();
        }
        
        $result = [
            'data' => [
                'filename' => $filename,
                'img' => config_get('base.url') . '/images/' . $path . '/' . $filename
            ]
        ];

        return $this->responseMeta(
            $result, 
            200,
            $this->translator->trans('upload.succeeded') 
        );
    }

    /**
     * Crop, resize and save image
     *
     * @param string $path
     * @param string $filename
     * @param string $dimension
     * @return boolean
     */
    private function cropAndResize($path, $filename, $dimension = '4_3')
    {
        switch ($dimension) {
            case '1_1':
                $width = 300;
                $height = 300;
                break;
            case '16_9': 
                $width = 800;
                $height = 450;
                break;
            case '18_9': 
                $width = 900;
                $height = 450;
                break;
            default: 
                $width = 800;
                $height = 600;
                break;
        }

        $image = new ImageResize(getUploadPath($path) . '/' . $filename);
        $image->crop($width, $height, true, ImageResize::CROPCENTER);
        $image->save(getUploadPath($path) . '/' . $dimension . '/' . $filename);
        return true;
    }
}