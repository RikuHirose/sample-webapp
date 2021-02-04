<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use FFMpeg;
use FFMpeg\Format\Video\X264;
use Illuminate\Http\File;
use Storage;

use App\Jobs\Hoge;
use App\Models\Job;
use Aws\MediaConvert\MediaConvertClient;
use Aws\Exception\AwsException;

use Pion\Laravel\ChunkUpload\Exceptions\UploadMissingFileException;
use Pion\Laravel\ChunkUpload\Handler\AbstractHandler;
use Pion\Laravel\ChunkUpload\Handler\HandlerFactory;
use Pion\Laravel\ChunkUpload\Receiver\FileReceiver;

class HomeController extends Controller
{
    protected $disk;

    public function __construct(
    ) {
        $this->disk = Storage::disk('s3');
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

    public function show(Request $request)
    {
        Hoge::dispatch()->delay(10);
        
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

        //     // convert HLS original video
        //     $hlsPath = 'artist-files/movies/streaming/'.$requestFile->hashName().'.m3u8';

        //     $lowBitrateFormat = (new X264('libmp3lame', 'libx264'))->setKiloBitrate(500);

        //     $hls = FFMpeg::fromDisk('s3')
        //         ->open($originalPath)
        //         ->exportForHLS()
        //         ->addFormat($lowBitrateFormat)
        //         ->save($hlsPath);

        //     $hlsUrl = $this->disk->url($hlsPath);

        //     // get thumbnail url from original video
        //     $originalThumbnailPath = 'artist-files/'.$requestFile->hashName().'.png';
        //     FFMpeg::fromDisk('s3')
        //       ->open($originalPath)
        //       ->getFrameFromSeconds(1)
        //       ->export()
        //       ->toDisk('s3')
        //       ->save($originalThumbnailPath);
        //     $originalThumbnailUrl = $this->disk->url($originalThumbnailPath);
        //     dd($originalThumbnailUrl);
        // }

            // $client = new MediaConvertClient([
            //     'profile' => 'default',
            //     'version' => '2017-08-29',
            //     'region' => config('filesystems.disks.s3.region')
            // ]);

            // //retrieve endpoint
            // $result = $client->describeEndpoints([]);
            // $single_endpoint_url = $result['Endpoints'][0]['Url'];
            //     dd($single_endpoint_url);
            // try {
            //     $result = $client->describeEndpoints([]);
            //     $single_endpoint_url = $result['Endpoints'][0]['Url'];
            // } catch (AwsException $e) {
            //     // output error message if fails
            //     echo $e->getMessage();
            //     echo "\n";
            // }

            // $mediaConvertClient = new MediaConvertClient([
            //     'version' => '2017-08-29',
            //     'region' => config('filesystems.disks.s3.region'),
            //     'profile' => 'default',
            //     // 'endpoint' => $single_endpoint_url
            // ]);

            // $jobSetting = json_decode(file_get_contents(base_path('MediaConvert.json')), true);

            // try {
            //     $result = $mediaConvertClient->createJob([
            //         "Role"     => "arn:aws:iam::xxxx:role/MediaConvert_Default_Role",
            //         "Settings" => $jobSetting, //JobSettings structure
            //         "Queue"    => "arn:aws:mediaconvert:ap-northeast-1:xxxx:queues/Default",
            //         // 'credentials' => [
            //         //        'key' => config('filesystems.disks.s3.key'),
            //         //        'secret' => config('filesystems.disks.s3.secret'),
            //         // ],
            //     ]);
            //     dd($result);
            // } catch (AwsException $e) {
            //     // output error message if fails
            //     echo $e->getMessage();
            //     echo "\n";
            // }
        }

    }

    private function chunkUploadStart(Request $request)
    {
        return response()->json([
            'data' => [
                'end_offset' => 1,
                'session_id' => '61db8102-fca6-44ae-81e2-a499d438e7a5ss',
            ],
            'status' => "success"
        ]);
    }

    private function chunkUploadPart(Request $request)
    {

        $file = $request->file('chunk');
        // $fileExtension     = $file->getClientOriginalExtension();

        $path = Storage::disk('local')->path("chunks/ssssss");
        // $path = Storage::disk('local')->path("chunks/{$request->file('name')}");
        // $path = Storage::disk('local')->path("chunks/{$file->getClientOriginalName()}");

        \Illuminate\Support\Facades\File::append($path, $file);

        return response()->json([
            'status' => "success"
        ]);
    }

    private function chunkUploadFinish(Request $request)
    {
        // dd($request->all());
        return response()->json([
            'status' => "success"
        ]);
    }

    public function uploadChunk(Request $request)
    {
        $file = $request->file('file');

        $fileExtension     = $file->getClientOriginalExtension();

        $path = Storage::disk('local')->path("chunks/{$file->getClientOriginalName()}");

        \Illuminate\Support\Facades\File::append($path, $file->get());

        if ($request->has('is_last') && (bool)$request->input('is_last')) {
            if ($request->input('is_last') == 'false') {
                dd($request->all());
            }


            if ($request->input('is_last') == 'true') {
                $requestFile = new \Illuminate\Http\File($path);

                // $originalPath = $this->disk->putFileAs('artist-files/', $requestFile, $file->getClientOriginalName(), 'public');
                $originalPath = $this->disk->putFileAs('artist-files/', $requestFile, 'test.mp4', 'public');
            }
        }


        // $receiver = new FileReceiver('file', $request, HandlerFactory::classFromRequest($request));

        // // check if the upload is success, throw exception or return response you need
        // if ($receiver->isUploaded() === false) {
        //     throw new UploadMissingFileException();
        // }

        // // receive the file
        // $save = $receiver->receive();
        // \Log::info($save->getFile());

        // // check if the upload has finished (in chunk mode it will send smaller files)
        // if ($save->isFinished()) {
        //     // save the file and return any response you need, current example uses `move` function. If you are
        //     // not using move, you need to manually delete the file by unlink($save->getFile()->getPathname())
        //     \Log::info($save->getFile());

        //     // $originalPath = $this->disk->putFileAs('artist-files/', $requestFile, $file->getClientOriginalName(), 'public');
        //     $originalPath = $this->disk->putFileAs('artist-files/', $save->getFile(), 'larastagram_auth_2.mp4', 'public');

        //     \Log::info($originalPath);
        //     // unlink($save->getFile()->getPathname());

        //     return response()->json(['uploaded' => true]);
        // }

        // // we are in chunk mode, lets send the current progress
        // /** @var AbstractHandler $handler */
        // $handler = $save->handler();

        // // \Log::info($handler->getCurrentChunk());
        // // \Log::info($handler->getPercentageDone().'%');

        // return response()->json([
        //     'done'   => $handler->getPercentageDone(),
        //     'status' => true,
        // ]);


        // return response()->json(['uploaded' => true]);
    }

    public function download(Request $request)
    {
        $this->disk = Storage::disk('s3');
        // １．ファイルの名前をURIや、リクエストなどで渡す
        $file_name = '名称未設定のデザイン (1).png';
        // ２．アップロードしたいディレクトリのパス
        $s3_dir_pash = '';

        // １と２をあわせて、アップロード先パスを指定
        $s3_file_pash = $s3_dir_pash.$file_name; //'upload/5007.jpg'

        // ダウンロードする際のファイル名を、Content-Dispositionで指定
        $headers = [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $file_name . '"'
        ];

        // dd($this->disk->exists('名称未設定のデザイン (1).png'));

        return \Response::make($this->disk->get($s3_file_pash), 200, $headers);
    }

    private function getFileName($requestFile, $fileExtension)
    {
        $fileHash      = str_replace('.'.$requestFile->extension(), '', $requestFile->hashName());
        $fileName      = $fileHash.'.'.$fileExtension;

        return $fileName;
    }
}
