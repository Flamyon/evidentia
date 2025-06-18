<?php

namespace App\Http\Controllers;

use App\Exports\EvidencesExport;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use App\Imports\UsersImport;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use Mockery\Exception;
use App\Http\Middleware\CheckRoles;

class ImportExportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(CheckRoles::class . ':LECTURE,PRESIDENT');
    }

    public function import()
    {
        
        $route = route('lecture.import.save');

        return view('importexport.import',
            ['route' => $route]);
    }

    public function import_save(Request $request)
    {
        $user = Auth::user();
        
        $token = $request->session()->token();
        $tmp = '/tmp/'.$user->username.'/'.$token.'/';

        try {
            foreach (Storage::files($tmp) as $filename) {
                $name = pathinfo($filename, PATHINFO_FILENAME);
                $type = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                $path = $tmp . $name.".".$type;

                Excel::import(new UsersImport, $path);
                $this->set_student_rol();
                Storage::delete($path);

                break;
            }

        }catch(\Exception $e){
            return redirect()->route('lecture.import')->with('error', 'Ocurrió un error: ' . $e->getMessage());
        }

        return redirect()->route('lecture.user.list')->with('success', 'Alumnos importados con éxito');

    }

    public function set_student_rol()
    {
        $users = User::all();
        foreach ($users as $user){

            // si un usuario no tiene roles es que se acaba de importar
            if($user->roles->isEmpty()){
                $student_role = Role::where('rol','STUDENT')->get();
                $user->roles()->attach($student_role);
            }
        }
    }

    public function export()
    {
        
        $route = route('lecture.export.save');

        return view('importexport.export',
            ['route' => $route]);
    }

    public function export_save(Request $request)
    {
        
        $evidences_select = $request->input('evidences');
        $meetings_select = $request->input('meetings');
        $events_select = $request->input('events');
        $bonus = $request->input('bonus');

        try{
            // limpiar búfer de salida
            if (ob_get_level()) {
                ob_end_clean();
            }
            return Excel::download(new EvidencesExport($evidences_select,$meetings_select,$events_select,$bonus), 'evidencias' . Carbon::now() . '.xlsx');
        }catch(\Exception $e){
            return redirect()->route('lecture.export')->with('error', 'Ocurrió un error: ' . $e->getMessage());
        }

    }

}
