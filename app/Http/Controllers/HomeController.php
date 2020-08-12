<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use FFMpeg;
use FFMpeg\Format\Video\X264;
use Storage;

class HomeController extends Controller
{
    protected $disk;

    public function __construct(
    ) {
        $this->disk           = Storage::disk('s3');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        \SeoHelper::setIndexSeo();
        return view('home');
    }

    public function store(Request $request)
    {
        if ($request->hasFile('file') && $request->file('file')->isValid()) {
            $requestFile = $request->file('file');

            $fileExtension     = $requestFile->getClientOriginalExtension();
            $originalFileName  = $this->getFileName($requestFile, $fileExtension);
            $folderName        = 'artist-files/movies/original';

            $originalPath      = $this->disk->putFileAs($folderName, $requestFile, $originalFileName, 'public');
            $originalUrl       = $this->disk->url($originalPath);

            // convert HLS original video
            $hlsPath = 'artist-files/movies/streaming/'.$requestFile->hashName().'.m3u8';

            $lowBitrateFormat = (new X264('libmp3lame', 'libx264'))->setKiloBitrate(500);

            $hls = FFMpeg::fromDisk('s3')
                ->open($originalPath)
                ->exportForHLS()
                ->addFormat($lowBitrateFormat)
                ->save($hlsPath);

            $hlsUrl = $this->disk->url($hlsPath);

            // get thumbnail url from original video
            $originalThumbnailPath = 'artist-files/'.$requestFile->hashName().'.png';
            FFMpeg::fromDisk('s3')
              ->open($originalPath)
              ->getFrameFromSeconds(1)
              ->export()
              ->toDisk('s3')
              ->save($originalThumbnailPath);
            $originalThumbnailUrl = $this->disk->url($originalThumbnailPath);
            dd($originalThumbnailUrl);
        }
    }

    private function getFileName($requestFile, $fileExtension)
    {
        $fileHash      = str_replace('.'.$requestFile->extension(), '', $requestFile->hashName());
        $fileName      = $fileHash.'.'.$fileExtension;

        return $fileName;
    }
}
