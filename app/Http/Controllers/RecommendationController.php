<?php

namespace App\Http\Controllers;

use App\Jobs\SendRecommendationEvaluatedEmail;
use App\Jobs\SendRecommendationSubmittedEmail;
use App\Mail\RecommendationEvaluated;
use App\Mail\RecommendationSubmitted;
use App\Models\Recommendation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class RecommendationController extends Controller
{
  
    public function createBug()
    {
        return view('recommendations.bug');
    }

  
    public function storeBug(Request $request)
    {
        $validated = $request->validate([
            'email'          => 'required|email|max:255',
            'recommendation' => 'required|string',
        ]);

        $validated['user_id'] = auth()->user()->userid ?? null;
        $validated['type']    = 'bug';

        $recommendation = Recommendation::create($validated);

        SendRecommendationSubmittedEmail::dispatch($recommendation);

        return redirect()->route('bugs.create')
            ->with('success', 'Your bug report has been submitted! A confirmation email has been sent.');
    }

  
    public function createRecommendation()
    {
        return view('recommendations.create');
    }


    public function storeRecommendation(Request $request)
    {
        $validated = $request->validate([
            'email'          => 'required|email|max:255',
            'recommendation' => 'required|string',
        ]);

        $validated['user_id'] = auth()->user()->userid ?? null;
        $validated['type']    = 'recommendation';

        $recommendation = Recommendation::create($validated);

        SendRecommendationSubmittedEmail::dispatch($recommendation);

        return redirect()->route('recommendations.create')
            ->with('success', 'Your recommendation has been submitted! A confirmation email has been sent.');
    }


    public function viewRecommendation(Request $request)
    {
        $filter    = $request->query('filter');
        $currentId = auth()->user()->userid ?? auth()->id();

        $query = Recommendation::query()
            ->with('user')
            ->where('user_id', $currentId)
            ->whereNull('parent_id');

        if (in_array($filter, ['approved', 'pending', 'rejected'])) {
            $query->where('status', $filter);
        }

        if ($filter === 'recommendations') {
            $query->where('type', 'recommendation');
        } elseif ($filter === 'bugs') {
            $query->where('type', 'bug');
        }

        $recommendations = $query->latest()->paginate(10);

        return view('recommendations.view', compact('recommendations'));
    }


    public function index(Request $request)
    {
        $recommendations = Recommendation::with('user')
            ->whereNull('parent_id')
            ->when($request->type,   fn ($q) => $q->where('type', $request->type))
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(10);

        return view('recommendations.index', compact('recommendations'));
    }


    public function indexBugs(Request $request)
    {
        $bugs = Recommendation::query()
            ->with('user')
            ->where('type', 'bug')
            ->whereNull('parent_id')
            ->when($request->status, fn ($q) => $q->where('status', $request->status))
            ->latest()
            ->paginate(10);

        return view('bugs.index', compact('bugs'));
    }


    public function evaluate(Request $request, Recommendation $recommendation)
    {
        $validated = $request->validate([
            'status'  => 'required|in:pending,approved,rejected',
            'remarks' => 'nullable|string',
        ]);

        $validated['evaluated_by'] = auth()->user()->fname . ' ' . auth()->user()->lname;

        $recommendation->update($validated);

        SendRecommendationEvaluatedEmail::dispatch($recommendation);

        return back()->with('success', "Report #{$recommendation->id} has been evaluated.");
    }


    public function conversationJson($id)
    {
        try {
            $recommendation = Recommendation::with(['replies.user'])->findOrFail($id);

            $replies = $recommendation->replies->map(function ($reply) {
                return [
                    'id'             => $reply->id,
                    'user_id'        => $reply->user_id,
                    'user_fname'     => $reply->user->fname ?? null,
                    'user_lname'     => $reply->user->lname ?? null,
                    'evaluated_by'   => $reply->evaluated_by ?? null,
                    'recommendation' => $reply->recommendation,
                    'created_at'     => $reply->created_at->format('M d, Y H:i'),
                ];
            });

            return response()->json(['replies' => $replies]);

        } catch (\Exception $e) {
            Log::error('conversationJson error', ['error' => $e->getMessage()]);
            return response()->json(['replies' => []], 200);
        }
    }

    public function viewConversation($id)
    {
        $recommendation = Recommendation::with(['user', 'replies' => function ($query) {
            $query->with('user')->oldest();
        }])->findOrFail($id);

        // FIX: Use loose comparison (!=) to avoid strict type mismatch between int and string IDs
        if ($recommendation->user_id != (auth()->user()->userid ?? auth()->id())) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->wantsJson()) {
            return response()->json([
                'main'    => $recommendation,
                'replies' => $recommendation->replies,
            ]);
        }

        return view('recommendations.conversation', compact('recommendation'));
    }

    public function submitReply(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|max:2000',
        ]);

        $parent = Recommendation::findOrFail($id);

      
        if ($parent->user_id != (auth()->user()->userid ?? auth()->id())) {
            abort(403, 'Unauthorized action.');
        }

        $reply = Recommendation::create([
            'parent_id'      => $parent->id,
            'user_id'        => $parent->user_id,
            'email'          => $parent->email,
            'type'           => $parent->type,
            'recommendation' => $request->message,
            'status'         => $parent->status,
            'remarks'        => null,
            'evaluated_by'   => null,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => 'Reply sent successfully!',
                'reply'   => [
                    'id'             => $reply->id,
                    'user_id'        => $reply->user_id,
                    'evaluated_by'   => $reply->evaluated_by,
                    'recommendation' => $reply->recommendation,
                    'created_at'     => $reply->created_at->format('M d, Y H:i'),
                ],
            ]);
        }

     
        return redirect()->route('recommendations.conversation', ['id' => $parent->id])
            ->with('success', 'Your reply has been submitted successfully.');
    }

    public function adminConversationJson($id)
    {
        $recommendation = Recommendation::with(['replies.user'])->findOrFail($id);

        $replies = $recommendation->replies->map(function ($reply) {
            return [
                'id'             => $reply->id,
                'user_id'        => $reply->user_id,
                'user_fname'     => $reply->user->fname ?? null,
                'user_lname'     => $reply->user->lname ?? null,
                'evaluated_by'   => $reply->evaluated_by ?? null,
                'recommendation' => $reply->recommendation,
                'created_at'     => $reply->created_at->format('M d, Y H:i'),
            ];
        });

        return response()->json(['replies' => $replies]);
    }

   
    public function adminViewConversation($id)
    {
        $recommendation = Recommendation::with(['user', 'replies' => function ($query) {
            $query->with('user')->oldest();
        }])->findOrFail($id);

        if (request()->wantsJson()) {
            return response()->json([
                'main'    => $recommendation,
                'replies' => $recommendation->replies,
            ]);
        }

        return view('recommendations.admin_conversation', compact('recommendation'));
    }

    /**
     * Admin submit reply
     */
    public function adminSubmitReply(Request $request, $id)
    {
        try {
            Log::info('Admin reply attempt', [
                'recommendation_id' => $id,
                'user'              => auth()->user()->email ?? 'unknown',
            ]);

            $request->validate([
                'message' => 'required|string|max:2000',
            ]);

            $parent    = Recommendation::findOrFail($id);
            $adminName = auth()->user()->fname . ' ' . auth()->user()->lname;

          
            $reply = Recommendation::create([
                'parent_id'      => $parent->id,
                'user_id'        => auth()->user()->userid, 
                'email' => $parent->email,
                'type'           => $parent->type,
                'recommendation' => $request->message,
                'status'         => $parent->status,
                'remarks'        => null,
                'evaluated_by'   => $adminName,
            ]);

            Log::info('Admin reply created', ['reply_id' => $reply->id]);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => 'Reply sent successfully!',
                    'reply'   => [
                        'id'             => $reply->id,
                        'user_id'        => $reply->user_id,
                        'evaluated_by'   => $reply->evaluated_by,
                        'recommendation' => $reply->recommendation,
                        'created_at'     => $reply->created_at->format('M d, Y H:i'),
                    ],
                ]);
            }

            return back()->with('success', 'Reply sent successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->wantsJson()) {
                return response()->json(['error' => $e->errors()], 422);
            }
            return back()->withErrors($e->errors())->withInput();

        } catch (\Exception $e) {
            Log::error('Admin reply failed', ['error' => $e->getMessage()]);

            if ($request->wantsJson()) {
                return response()->json(['error' => 'Failed to send reply: ' . $e->getMessage()], 500);
            }
            return back()->with('error', 'Failed to send reply: ' . $e->getMessage());
        }
    }

    public function replyPage($id)
    {
        $rec = Recommendation::findOrFail($id);

       
        if ($rec->user_id != (auth()->user()->userid ?? auth()->id())) {
            abort(403, 'Unauthorized action.');
        }

        return view('recommendations.reply', compact('rec'));
    }

    public function destroy($id)
    {
        
        $parent = Recommendation::findOrFail($id);
        Recommendation::where('parent_id', $id)->delete();
        $parent->delete();

        return redirect()->route('admin.reports.index')
            ->with('success', 'Record deleted successfully.');
    }
}