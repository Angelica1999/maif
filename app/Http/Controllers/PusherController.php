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
        $this->middleware('block.secure.nonadmin');
    }

    public function tasks(Request $request){
      $notes = Notes::with('user')->orderBy('updated_at', 'desc');
      if($request->viewAll){
        $request->keyword = '';
      }else if($request->keyword){
        $notes->where('notes', 'LIKE', "%$request->keyword%");
      }
      return view('tasks',[
        'all' => $notes->get(),
        'notes' => $notes->paginate(50),
        'keyword' => $request->keyword
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

    public function process($id){
        Notes::where('id', $id)->update(['status' => 1]);
        return redirect()->back()->with('notes_update', true);
    }

    public function update(Request $request){

        $id = $request->id;
        $note = Notes::where('id', $id)->first();
        if($note){
            $note->notes = $request->note;
            $note->created_by = Auth::user()->userid;
            $note->save();
        }
        return redirect()->back()->with('note_update', true);
      }

    public function delete($id){
        Notes::where('id', $id)->delete();
        return redirect()->back()->with('note_update', true);
    }
  }
