<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use FFMpeg;
use FFMpeg\Format\Video\X264;
use Storage;

use App\Jobs\Hoge;
use App\Models\Job;
use Aws\MediaConvert\MediaConvertClient;
use Aws\Exception\AwsException;

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
            //         "Role"     => "arn:aws:iam::818711851313:role/MediaConvert_Default_Role",
            //         "Settings" => $jobSetting, //JobSettings structure
            //         "Queue"    => "arn:aws:mediaconvert:ap-northeast-1:818711851313:queues/Default",
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
