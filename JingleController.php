<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Traits\CommonMethods;
use App\Models\Jingles;

use Illuminate\Support\Facades\Storage;
use App\File;
use Illuminate\Support\Facades\URL;
use Carbon\Carbon;

class JingleController extends Controller
{
    use CommonMethods;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $filter = $request->query('filter');

        if (!empty($filter)) {
            $data = Jingles::sortable()
                        ->where('jingles.name', 'like', '%'.$filter.'%')
			->orderBy('id', 'DESC')
                        ->paginate(5);
        } else {
            $data = Jingles::sortable()->orderBy('id', 'DESC')->paginate(5);
        }

        return view('Admin/jingles/list')->with('data', $data)->with('filter', $filter);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Jingles  $jingle
     * @return \Illuminate\Http\Response
     */
    public function edit( $id )
    {
        $data   =   Jingles::find($id);

        if(!$data)
            return redirect()->back()
                ->with('edit_item_error', 'Jingle not found');

        $enum = Jingles::getEnums();

        return view('Admin/jingles/edit', [ 'data' => $data, 'enums' =>  $enum ] );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Jingles  $pljinan
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request )
    {
        $rules = [
            'jingle_jksdf' => 'required',
            'name' => 'required',
            'length' => 'required',
            'status' => 'required'
        ];

        if($request->hasFile('jingle'))
        {
            $rules['jingle'] = 'required|file|mimes:audio/mpeg,mpga,mp3,wav,aac,m4a|max:9999';
        }

        $this->validate($request, $rules);

        $data   =   Jingles::find($request->jingle_jksdf);

        $data->name         =   $request->name;
        $data->length       =   $request->length;
        $data->status       =   $request->status;
        $data->save();

        if($request->hasFile('jingle'))
        {
            $uniqueid=uniqid();
            $original_name=$request->file('jingle')->getClientOriginalName();
            $imagePath = $request->file('jingle')->getPathName();
            $size=$request->file('jingle')->getSize();
            $extension=$request->file('jingle')->getClientOriginalExtension();
            $filename=Carbon::now()->format('Ymd').'_'.$uniqueid.'.'.$extension;
            $store = storage_path().'/app/public/uploads/jingles/'.$filename;
            exec('ffmpeg -i '.$imagePath.' -ab 64 '.$store.'
            // 2>&1', $output);
            
            if( $data->jingle ){
                Storage::delete('public/uploads/jingles/'.$data->jingle);
            }

            $data->jingle = $filename;
            $data->folder = SELF::$JINGLES_STORAGE;
            $data->save();
        }

        return redirect()->route('jingles')
                ->with('success','Jingle details updated successfully');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $enums = Jingles::getEnums();
        return view('Admin.jingles.create',compact('enums'));
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'length' => 'required',
            'jingle' => 'required|file|mimes:audio/mpeg,mpga,mp3,wav,aac,m4a|max:9999',
            'status' => 'required'
        ]);

        if($request->hasFile('jingle'))
        {
            // $file = $request->file;
            $uniqueid=uniqid();
            $original_name=$request->file('jingle')->getClientOriginalName();
            $imagePath = $request->file('jingle')->getPathName();
            $size=$request->file('jingle')->getSize();
            $extension=$request->file('jingle')->getClientOriginalExtension();
            $filename=Carbon::now()->format('Ymd').'_'.$uniqueid.'.'.$extension;
            $store = storage_path().'/app/public/uploads/jingles/'.$filename;
            exec('ffmpeg -i '.$imagePath.' -ab 64 '.$store.'
            // 2>&1', $output);
   
            // $path=$request->file('jingle')->storeAs('public/uploads/jingles/',$filename);
            // $all_audios=$audiopath;

            $jingle = Jingles::create([
                'name' => $request->name, 
                'length' => $request->length, 
                'folder' => SELF::$JINGLES_STORAGE, 
                'jingle' => $filename, 
                'status' => $request->status
            ]);

            return redirect()->route('jingles')
                        ->with('success','Jingle added successfully');
        }

        return redirect()->route('jingles')
                        ->with('edit_item_error','Upload failed ! Something went wrong.');
    }

    /**
     * delete the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Jingles  $pljinan
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request )
    {
        $return = [
            'status'    =>  0,
            'message'   =>  'Something went wrong.'
        ];

        $rules = [  'id' => 'required'  ];

        $this->validate($request, $rules);

        $data   =   Jingles::find($request->id);
        if( $data->jingle ){
            Storage::delete('public/uploads/jingles/'.$data->jingle);
        }
        $data->delete();

        $return = [
            'status'    =>  1,
            'message'   =>  'Success.'
        ];
        return json_encode($return);
    }
}
