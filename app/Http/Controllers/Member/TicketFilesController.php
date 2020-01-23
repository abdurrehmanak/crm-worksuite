<?php

namespace App\Http\Controllers\Member;

use App\EmployeeDocs;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\EmployeeDocs\CreateRequest;
use App\TaskFile;
use App\TicketFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class TicketFilesController extends MemberBaseController
{

    private $mimeType = [
        'txt' => 'fa-file-text',
        'htm' => 'fa-file-code-o',
        'html' => 'fa-file-code-o',
        'php' => 'fa-file-code-o',
        'css' => 'fa-file-code-o',
        'js' => 'fa-file-code-o',
        'json' => 'fa-file-code-o',
        'xml' => 'fa-file-code-o',
        'swf' => 'fa-file-o',
        'flv' => 'fa-file-video-o',

        // images
        'png' => 'fa-file-image-o',
        'jpe' => 'fa-file-image-o',
        'jpeg' => 'fa-file-image-o',
        'jpg' => 'fa-file-image-o',
        'gif' => 'fa-file-image-o',
        'bmp' => 'fa-file-image-o',
        'ico' => 'fa-file-image-o',
        'tiff' => 'fa-file-image-o',
        'tif' => 'fa-file-image-o',
        'svg' => 'fa-file-image-o',
        'svgz' => 'fa-file-image-o',

        // archives
        'zip' => 'fa-file-o',
        'rar' => 'fa-file-o',
        'exe' => 'fa-file-o',
        'msi' => 'fa-file-o',
        'cab' => 'fa-file-o',

        // audio/video
        'mp3' => 'fa-file-audio-o',
        'qt' => 'fa-file-video-o',
        'mov' => 'fa-file-video-o',
        'mp4' => 'fa-file-video-o',
        'mkv' => 'fa-file-video-o',
        'avi' => 'fa-file-video-o',
        'wmv' => 'fa-file-video-o',
        'mpg' => 'fa-file-video-o',
        'mp2' => 'fa-file-video-o',
        'mpeg' => 'fa-file-video-o',
        'mpe' => 'fa-file-video-o',
        'mpv' => 'fa-file-video-o',
        '3gp' => 'fa-file-video-o',
        'm4v' => 'fa-file-video-o',

        // adobe
        'pdf' => 'fa-file-pdf-o',
        'psd' => 'fa-file-image-o',
        'ai' => 'fa-file-o',
        'eps' => 'fa-file-o',
        'ps' => 'fa-file-o',

        // ms office
        'doc' => 'fa-file-text',
        'rtf' => 'fa-file-text',
        'xls' => 'fa-file-excel-o',
        'ppt' => 'fa-file-powerpoint-o',
        'docx' => 'fa-file-text',
        'xlsx' => 'fa-file-excel-o',
        'pptx' => 'fa-file-powerpoint-o',


        // open office
        'odt' => 'fa-file-text',
        'ods' => 'fa-file-text',
    ];

    /**
     * ManageLeadFilesController constructor.
     */
    public function __construct() {
        parent::__construct();
        $this->pageIcon = 'icon-layers';
        $this->pageTitle = __('app.menu.ticketFiles');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }


    /**
     * @param Request $request
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Throwable
     */
    public function store(Request $request)
    {
        if ($request->hasFile('file')) {
            foreach ($request->file as $fileData){
                $storage = config('filesystems.default');
                $file = new TicketFile();
                $file->user_id = $this->user->id;
                $file->ticket_reply_id = $request->ticket_reply_id;
                switch($storage) {
                    case 'local':
                        $fileData->storeAs('ticket-files/'.$request->ticket_reply_id, $fileData->hashName());
                        break;
                    case 's3':
                        Storage::disk('s3')->putFileAs('ticket-files/'.$request->ticket_reply_id, $fileData, $fileData->getClientOriginalName(), 'public');
                        break;
                    case 'google':
                        $dir = '/';
                        $recursive = false;
                        $contents = collect(Storage::cloud()->listContents($dir, $recursive));
                        $dir = $contents->where('type', '=', 'dir')
                            ->where('filename', '=', 'ticket-files')
                            ->first();

                        if(!$dir) {
                            Storage::cloud()->makeDirectory('ticket-files');
                        }

                        $directory = $dir['path'];
                        $recursive = false;
                        $contents = collect(Storage::cloud()->listContents($directory, $recursive));
                        $directory = $contents->where('type', '=', 'dir')
                            ->where('filename', '=', $request->ticket_reply_id)
                            ->first();

                        if ( ! $directory) {
                            Storage::cloud()->makeDirectory($dir['path'].'/'.$request->ticket_reply_id);
                            $contents = collect(Storage::cloud()->listContents($directory, $recursive));
                            $directory = $contents->where('type', '=', 'dir')
                                ->where('filename', '=', $request->ticket_reply_id)
                                ->first();
                        }

                        Storage::cloud()->putFileAs($directory['basename'], $fileData, $fileData->getClientOriginalName());

                        $file->google_url = Storage::cloud()->url($directory['path'].'/'.$fileData->getClientOriginalName());

                        break;
                    case 'dropbox':
                        Storage::disk('dropbox')->putFileAs('ticket-files/'.$request->ticket_reply_id.'/', $fileData, $fileData->getClientOriginalName());
                        $dropbox = new Client(['headers' => ['Authorization' => "Bearer ".config('filesystems.disks.dropbox.token'), "Content-Type" => "application/json"]]);
                        $res = $dropbox->request('POST', 'https://api.dropboxapi.com/2/sharing/create_shared_link_with_settings',
                            [\GuzzleHttp\RequestOptions::JSON => ["path" => '/ticket-files/'.$request->ticket_reply_id.'/'.$fileData->getClientOriginalName()]]
                        );
                        $dropboxResult = $res->getBody();
                        $dropboxResult = json_decode($dropboxResult, true);
                        $file->dropbox_link = $dropboxResult['url'];
                        break;
                }

                $file->filename = $fileData->getClientOriginalName();
                $file->hashname = $fileData->hashName();
                $file->size = $fileData->getSize();
                $file->save();
            }

        }
        return Reply::redirect(route('member.all-tasks.index'), 'Task Updated');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
//        $this->lead = Lead::findOrFail($id);
//        return view('admin.lead.lead-files.show', $this->data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }


    /**
     * @param Request $request
     * @param $id
     * @return array
     * @throws \Throwable
     */
    public function destroy(Request $request, $id)
    {
        $file = TicketFile::findOrFail($id);

        Files::deleteFile($file->hashname,'ticket-files/'.$file->ticket_id);

        TicketFile::destroy($id);

        $this->ticketFiles = TicketFile::where('ticket_id', $file->ticket_id)->get();

        $view = view('member.ticket.ajax-list', $this->data)->render();

        return Reply::successWithData(__('messages.fileDeleted'), ['html' => $view, 'totalFiles' => sizeof($this->taskFiles)]);
    }


    /**
     * @param $id
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse|\Symfony\Component\HttpFoundation\StreamedResponse
     */
    public function download($id) {
        $file = TicketFile::findOrFail($id);

        return response()->download('user-uploads/ticket-files/'.$file->ticket_reply_id.'/'.$file->hashname, $file->filename);
    }

}
