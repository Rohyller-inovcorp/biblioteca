<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use Illuminate\Support\Facades\Auth;
use App\Models\Loan;
use Inertia\Inertia;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewReviewNotificationMail;
class ReviewedController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');
        $reviews = Review::with(['user', 'book'])
            ->when($status, fn($q) => $q->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Reviews/Index', [
            'data' => $reviews,
            'filters' => [
                'status' => $status
            ]
        ]);
    }
    public function store(Request $request)
    {
        $request->validate([
            'book_id' => 'required|exists:books,id',
            'loan_id' => 'required|exists:loans,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'required|string|max:2000',
        ]);

        $loan = Loan::where('id', $request->loan_id)
            ->where('user_id', Auth::id())
            ->whereNotNull('actual_return_date')
            ->firstOrFail();

        $review = Review::create([
            'book_id' => $request->book_id,
            'loan_id' => $loan->id,
            'user_id' => Auth::id(),
            'rating' => $request->rating,
            'comment' => $request->comment,
            'status' => 'pending',
        ]);
        $admins = User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            Mail::to($admin->email)->queue(new NewReviewNotificationMail($review));
        }
        return redirect()->back()->with('success', 'Review enviada para moderação.');
    }
    public function update(Request $request, Review $review)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'reason' => 'nullable|string|max:1000',
        ]);

        if ($request->status === 'rejected' && !$request->reason) {
            abort(422, 'Justificação obrigatória');
        }

        $review->update([
            'status' => $request->status,
            'rejection_reason' => $request->reason,
        ]);

        Mail::to($review->user->email)->queue(new \App\Mail\ReviewStatusMail($review));

        return back();
    }
}
