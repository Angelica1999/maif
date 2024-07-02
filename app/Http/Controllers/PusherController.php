<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\PostNotif;
use App\Models\Notes;

class PusherController extends Controller{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function tasks(){
      $notes = Notes::with('user')->orderBy('updated_at', 'desc');
      return view('tasks',[
        'notes' => $notes->paginate(50)
      ]);
    }
    
    public function push(){
      return view('pusher');
    }

    public function note(){
      return view('note.notepad');
    }

    public function save(Request $request){

      $note = new Notes();
      $note->notes = $request->note;
      $note->created_by = Auth::user()->userid;
      $note->status = 0;
      $note->save();
      $data = [
          'title' => $request->note,
          'author' => Auth::user()->lname.', '.Auth::user()->fname
        ];
      // event(new PostNotif($data));
      return redirect()->back()->with('note', true);
    }
}
