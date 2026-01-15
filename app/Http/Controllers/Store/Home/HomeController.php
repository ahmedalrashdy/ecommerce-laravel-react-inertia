<?php

namespace App\Http\Controllers\Store\Home;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Services\LandingPageService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function __construct(public LandingPageService $landingPageService)
    {
    }

    public function index(Request $request)
    {
        return Inertia::render('store/index', [
            'heroSlides' => fn() => $this->landingPageService->getHeroSlides(),
            'productsGrid' => fn() => $this->landingPageService->productsGrid(),
            'testimonials' => Inertia::optional(function () {
                return Review::query()
                    ->where('is_approved', true)
                    ->whereNotNull('comment')
                    ->with(['user:id,name,avatar'])
                    ->latest()
                    ->limit(20)
                    ->get()
                    ->map(function ($review) {
                        return [
                            'id' => $review->id,
                            'name' => $review->user->name,
                            'avatar' => $review->user->avatar ?? 'https://i.pravatar.cc/150?img=' . $review->user->id,
                            'rating' => $review->rating,
                            'comment' => $review->comment,
                            'date' => $review->created_at->diffForHumans(),
                            'verified' => true,
                        ];
                    })
                    ->toArray();
            }),
        ]);
    }


}
