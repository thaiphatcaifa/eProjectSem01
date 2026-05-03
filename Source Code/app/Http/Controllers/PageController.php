<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Specialty;

class PageController extends Controller
{
    public function home()
    {
        $specialties = Specialty::take(6)->get();
        // Fetch medical news
        $news = Article::where('type', 'news')->latest()->take(3)->get();
        // Fetch diseases, preventions, and cures
        $healthInfo = Article::whereIn('type', ['disease', 'prevention'])->latest()->take(3)->get();
        
        return view('pages.home', compact('specialties', 'news', 'healthInfo'));
    }

    public function about()
    {
        return view('pages.about');
    }

    public function contact()
    {
        return view('pages.contact');
    }

    public function submitContact(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email',
            'phone' => 'required|string',
            'message' => 'required|string',
        ]);

        // Process message (e.g., save to DB or send email)
        return redirect()->back()->with('success', 'Thank you! Your message has been sent to MediConnect.');
    }

    public function sitemap()
    {
        return view('pages.sitemap');
    }
}